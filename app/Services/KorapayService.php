<?php

namespace App\Services;

use App\Mail\GeneralMail;
use App\Models\Level;
use App\Models\Transaction;
use App\Models\UserPaymentPlan;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;
use App\Models\SubscriptionStat;
use App\Models\User;
use App\Models\UserLevel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class KorapayService
{

    // private string $baseUrl;
    private string $secretKey;
    public TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        // $this->baseUrl = rtrim(config('services.env.flutterwave_base_url'), '/');

        $this->secretKey = config('services.env.kora_sec'); //config('services.env.flutterwave_secret_key');
        $this->transactionService = $transactionService;
    }

    public function initiatePayment($levelId, $amount = null)
    {

        $user = Auth::user();

        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $level = Level::find($levelId);

        if (!$level) {
            throw new \Exception('Invalid subscription level');
        }

        // Always charge in NGN (single source of truth)
        $amountNGN = convertToBaseCurrency($level->amount, 'NGN');

        return $this->callKoraPay($amountNGN, $level);
    }

    private function callKoraPay($amount, $level)
    {

        $user = Auth::user();
        $reference = generateTransactionRef();


        $idempotencyKey = $data['idempotency_key']
            ?? Str::uuid()->toString();

        $existingTransaction = Transaction::query()
            ->where('idempotency_key', $idempotencyKey)
            ->first();

        if ($existingTransaction) {

            return [
                'success' => true,
                'message' => 'Existing transaction returned',
                'data' => $existingTransaction,
            ];
        }

        // Create transaction
        $transaction = $this->transactionService->createTransaction(
            user: $user,
            idempotencyKey: $idempotencyKey,
            provider: 'kora',
            reference: $reference,
            amount: $amount,
            currency: 'NGN',
            status: 'initiated',
            action: 'Debit',
            type: 'subscription_upgrade',
            description: $user->name . ' upgrade to ' . $level->name,
            meta: [
                'level_id' => $level->id,
            ]
        );

        $payload = [
            "amount" => $amount,
            "redirect_url" => route('verify.subscription'),
            "currency" => "NGN",
            "reference" => $reference,
            "narration" => $level->name . " Upgrade",
            "channels" => ["card", "bank_transfer", "pay_with_bank"],
            "customer" => [
                "name" => $user->name,
                "email" => $user->email
            ],
            "metadata" => [
                'user_id' => $user->id,
                'level_id' => $level->id
            ]
        ];

        try {
            $res = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.env.kora_sec'),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post(
                'https://api.korapay.com/merchant/api/v1/charges/initialize',
                $payload
            );

            if (!$res->successful()) {
                $transaction->update(['status' => 'failed']);
                throw new \Exception('Payment initialization failed');
            }

            $body = $res->json();

            return $body['data']['checkout_url'] ?? null;
        } catch (\Exception $e) {
            $transaction->update(['status' => 'failed']);
            throw new \Exception('KoraPay error: ' . $e->getMessage());
        }
    }


    private function verifyWithKoraPay($reference)
    {
        try {
            $res = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.env.kora_sec'),
                'Accept' => 'application/json',
            ])->get(
                'https://api.korapay.com/merchant/api/v1/charges/' . $reference
            );

            if (!$res->successful()) {
                throw new \Exception('Verification request failed');
            }

            return $res->json();
        } catch (\Exception $e) {
            throw new \Exception('Verification error: ' . $e->getMessage());
        }
    }

    public function verifySubscriptionPayment(string $reference): array
    {
        $transaction = Transaction::where('ref', $reference)->first();

        if (!$transaction) {
            throw new \Exception('Transaction not found');
        }

        // Idempotency
        if ($transaction->status === 'success') {
            $nextPaymentDate = UserLevel::where('user_id', $transaction->user_id)
                ->value('next_payment_date');

            return [
                'level_name' => null,
                'next_payment_date' => Carbon::parse($nextPaymentDate),
            ];
        }

        // Retry logic (improved)
        $attempts = 0;
        $maxAttempts = 5;
        $data = null;

        do {
            $response = $this->verifyWithKoraPay($reference);
            $data = $response['data'] ?? null;

            if ($data && $data['status'] === 'success') {
                break;
            }

            $attempts++;
            sleep(3);
        } while ($attempts < $maxAttempts);

        if (!$data || $data['status'] !== 'success') {
            $this->transactionService->markFailed($transaction, $data);
            throw new \Exception('Payment failed or still pending');
        }

        // 🔐 Strong validation
        if (
            $data['reference'] !== $transaction->ref ||
            $data['amount'] != $transaction->amount ||
            $data['currency'] !== $transaction->currency
        ) {
            $this->transactionService->markFailed($transaction, $data);
            throw new \Exception('Payment validation failed');
        }

        // Fetch level securely (DO NOT trust metadata blindly)
        $levelId = $data['metadata']['level_id'] ?? null;

        $level = Level::find($levelId);

        if (!$level) {
            throw new \Exception('Invalid level from payment metadata');
        }

        $nextPaymentDate = now()->addMonth();
        $user = $transaction->user;
        $amount = $transaction->amount;
        $currency = $transaction->currency;

        DB::transaction(function () use ($transaction, $data, $level, $nextPaymentDate, $user, $reference, $amount, $currency) {

            // Lock transaction row
            $lockedTransaction = Transaction::where('id', $transaction->id)
                ->lockForUpdate()
                ->first();

            if ($lockedTransaction->status === 'success') {
                return;
            }
            $this->transactionService->markProcessing($lockedTransaction, $data);
        });

        return [
            'level_name' => $level->name,
            'next_payment_date' => $nextPaymentDate,
        ];
    }
}
