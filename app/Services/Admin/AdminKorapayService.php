<?php

namespace App\Services\Admin;

use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use App\Support\AdminDateRange;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class AdminKorapayService
{
    public function __construct(
        protected TransactionService $transactionService,
    ) {}

    public function fetchBalances(): array
    {
        $baseUrl = rtrim((string) config('services.env.kora_base_url'), '/');

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.config('services.env.kora_sec'),
            ])->get("{$baseUrl}/balances");

            if (! $response->successful()) {
                return [
                    'ok' => false,
                    'error' => $response->json('message') ?? 'Unable to fetch Korapay balances.',
                    'currencies' => collect(),
                ];
            }

            $data = $response->json('data') ?? [];

            $ngn = $data['NGN'] ?? $data['ngn'] ?? null;

            $currencies = $ngn
                ? collect([[
                    'code' => 'NGN',
                    'available' => (float) ($ngn['available_balance'] ?? 0),
                    'pending' => (float) ($ngn['pending_balance'] ?? 0),
                ]])
                : collect();

            return [
                'ok' => true,
                'error' => null,
                'currencies' => $currencies,
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'error' => 'Korapay balance request failed.',
                'currencies' => collect(),
            ];
        }
    }

    public function fundingStats(AdminDateRange $dateRange): array
    {
        $base = Transaction::query()
            ->where('type', 'korapay_funding')
            ->whereBetween('created_at', [$dateRange->start, $dateRange->end]);

        $successful = (clone $base)->where('status', 'successful');

        return [
            'countInRange' => (clone $base)->count(),
            'successfulInRange' => (clone $successful)->count(),
            'ngnInRange' => (float) (clone $successful)->sum('amount'),
            'pendingInRange' => (clone $base)->whereIn('status', ['initiated', 'processing'])->count(),
        ];
    }

    public function fundingHistory(AdminDateRange $dateRange, ?string $status = null): LengthAwarePaginator
    {
        $query = Transaction::query()
            ->with('user:id,name,username,email')
            ->where('type', 'korapay_funding')
            ->whereBetween('created_at', [$dateRange->start, $dateRange->end])
            ->latest();

        if ($status) {
            $query->where('status', $status);
        }

        return $query->paginate(20)->withQueryString();
    }

    public function initiateDeposit(User $admin, float $amount, ?string $note = null): string
    {
        $currency = 'NGN';
        $min = 100.0;

        if ($amount < $min) {
            throw new RuntimeException('Minimum deposit is ₦'.number_format($min).'.');
        }

        $reference = generateTransactionRef('krf');
        $idempotencyKey = Str::uuid()->toString();

        $this->transactionService->createTransaction(
            user: $admin,
            idempotencyKey: $idempotencyKey,
            provider: 'kora',
            reference: $reference,
            amount: $amount,
            currency: $currency,
            status: 'initiated',
            action: 'Credit',
            type: 'korapay_funding',
            description: $note ?: 'Korapay merchant balance deposit',
            meta: [
                'admin_id' => $admin->id,
                'note' => $note,
                'charge_amount' => $amount,
                'charge_currency' => $currency,
            ],
        );

        $baseUrl = rtrim((string) config('services.env.kora_base_url'), '/');

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.config('services.env.kora_sec'),
        ])->post("{$baseUrl}/charges/initialize", [
            'amount' => $amount,
            'redirect_url' => route('admin.korapay.verify'),
            'notification_url' => route('korapay.webhook'),
            'currency' => $currency,
            'reference' => $reference,
            'narration' => 'Korapay balance funding',
            'channels' => ['card', 'bank_transfer', 'pay_with_bank'],
            'customer' => [
                'name' => $admin->name,
                'email' => $admin->email,
            ],
            'metadata' => [
                'admin_id' => $admin->id,
                'type' => 'korapay_funding',
            ],
        ]);

        if (! $response->successful()) {
            Transaction::where('ref', $reference)->update(['status' => 'failed']);
            throw new RuntimeException($response->json('message') ?? 'Unable to initialize Korapay deposit.');
        }

        $url = $response->json('data.checkout_url');

        if (! $url) {
            Transaction::where('ref', $reference)->update(['status' => 'failed']);
            throw new RuntimeException('Payment provider did not return a checkout URL.');
        }

        return $url;
    }

    public function acknowledgeDepositReturn(string $reference): array
    {
        if (! str_starts_with(strtoupper($reference), 'KRF-')) {
            throw new RuntimeException('Invalid deposit reference.');
        }

        $transaction = Transaction::query()
            ->where('ref', $reference)
            ->where('type', 'korapay_funding')
            ->firstOrFail();

        if ($transaction->status === 'successful') {
            return [
                'status' => 'success',
                'message' => '₦'.number_format($transaction->amount, 0).' has been added to your Korapay balance.',
            ];
        }

        if (in_array($transaction->status, ['failed', 'cancelled', 'flagged'], true)) {
            return [
                'status' => 'failed',
                'message' => 'Deposit was not successful. Please try again.',
            ];
        }

        return [
            'status' => 'pending',
            'message' => 'Payment received. Your Korapay balance will update shortly once confirmed.',
        ];
    }

    public function completeFundingFromWebhook(Transaction $transaction, array $payload): void
    {
        if ($transaction->type !== 'korapay_funding') {
            throw new RuntimeException('Invalid Korapay funding transaction.');
        }

        if (! str_starts_with(strtoupper($transaction->ref), 'KRF-')) {
            throw new RuntimeException('Invalid Korapay funding reference.');
        }

        $event = $payload['event'] ?? null;
        $data = $payload['data'] ?? [];

        if ($event !== 'charge.success' || ($data['status'] ?? '') !== 'success') {
            throw new RuntimeException('Webhook event is not a successful charge.');
        }

        if (($data['reference'] ?? '') !== $transaction->ref) {
            $this->transactionService->markFailed($transaction, $payload);
            throw new RuntimeException('Payment reference mismatch.');
        }

        if (! $this->webhookAmountMatches($transaction, $data)) {
            $this->transactionService->markFailed($transaction, $payload);
            throw new RuntimeException('Payment amount mismatch.');
        }

        DB::transaction(function () use ($transaction, $payload) {
            $locked = Transaction::where('id', $transaction->id)->lockForUpdate()->firstOrFail();

            if ($locked->status === 'successful') {
                return;
            }

            $this->transactionService->markSuccessful($locked, $payload);
        });
    }

    public function statusLabels(): Collection
    {
        return collect([
            'initiated' => 'Initiated',
            'processing' => 'Processing',
            'successful' => 'Successful',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
        ]);
    }

    protected function webhookAmountMatches(Transaction $transaction, array $data): bool
    {
        $gatewayAmount = (float) ($data['amount'] ?? 0);
        $expected = (float) ($transaction->meta['charge_amount'] ?? $transaction->amount);

        if ($gatewayAmount <= 0 || $expected <= 0) {
            return false;
        }

        if ($gatewayAmount > $expected * 50) {
            $gatewayAmount /= 100;
        }

        return abs($gatewayAmount - $expected) <= 0.02
            && strtoupper((string) ($data['currency'] ?? 'NGN')) === 'NGN';
    }
}
