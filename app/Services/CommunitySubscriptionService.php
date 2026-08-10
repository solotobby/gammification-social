<?php
// app/Services/CommunitySubscriptionService.php
namespace App\Services;

use App\Models\Community;
use App\Models\CommunitySubscription;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Payments\FlutterwaveGateway;
use Illuminate\Support\Carbon;
use App\Services\Payments\PaymentGatewayResolver;
use App\Support\CommunityFeeCalculator;
use App\Services\CommunityMembershipService;
use App\Services\CommunityFlutterwaveService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

use RuntimeException;

class CommunitySubscriptionService
{
    //  public function __construct(private readonly PaymentGatewayResolver $gateways) {}

    protected TransactionService $transactionService;
    protected FlutterwaveGateway $flutterwaveGateway;
    private string $flutterwaveSecretKey;
    private string $flutterwaveBaseUrl;
    private string $webhookSecret;



    public function __construct(TransactionService $transactionService, private readonly PaymentGatewayResolver $gateways, FlutterwaveGateway $flutterwaveGateway)
    {
        $this->transactionService = $transactionService;
        $this->flutterwaveGateway = $flutterwaveGateway;

        $this->flutterwaveBaseUrl = rtrim(config('services.env.flutterwave_base_url'), '/');
        $this->flutterwaveSecretKey = config('services.env.flutterwave_secret_key');
        $this->webhookSecret = (string) config('services.env.webhook_hash');
    }


    public function pendingOrActiveFor(Community $community, User $user): ?CommunitySubscription
    {
        return CommunitySubscription::where('community_id', $community->id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'active'])
            ->latest()
            ->first();
    }

    public function generatePaymentLink(Community $community, User $user)
    {
        if (! $community->isInCurrency(userBaseCurrency($user->id))) {
            throw new RuntimeException('This community is not available in your currency.');
        }

        //get current user base currency
        $userCurrency = userBaseCurrency();


        if ($userCurrency === 'NGN') {
            // Use Korapay for NGN
            return $this->korapayPaymentLink($community, $user);
        } else {
            // Use Flutterwave for other currencies
            $paymentLink = $this->flutterwavePaymentLink($community, $user);
        }

        return $paymentLink;
    }

    private function korapayPaymentLink(Community $community, User $user)
    {

        $userCurrency = userBaseCurrency();

        $reference = generateTransactionRef('community');

        $idempotencyKey = Str::uuid()->toString();

        $chargeAmount = (float) $community->member_charge;
        $convertedAmount = (float) convertCurrency($chargeAmount, $community->currency, $userCurrency);

        $existingTransaction = Transaction::query()
            ->where('idempotency_key', $idempotencyKey)
            ->first();


        // if ($existingTransaction) {
        //     return $existingTransaction;
        // }

        // Create transaction
        $transaction = $this->transactionService->createTransaction(
            user: $user,
            idempotencyKey: $idempotencyKey,
            provider: 'kora',
            reference: $reference,
            amount: $convertedAmount,
            currency: $userCurrency,
            status: 'initiated',
            action: 'Debit',
            type: 'community_' . $community->billing_type,
            description: ' Payment for community ( ' .  $community->billing_type . ' ): ' . $community->name,
            meta: [
                'community' => $community->name,
                'community_id' => $community->id,
                'user_id' => $user->id,
                'amount' => $convertedAmount,
                'currency' => $userCurrency
            ]
        );


        $payload = [
            "amount" => (float) $convertedAmount,
            "redirect_url" => route('verify.korapay.community.subscription'),
            "currency" => strtoupper($userCurrency),
            "reference" => $reference,
            "narration" => 'Payment for community (' .
                $community->billing_type . '): ' .
                $community->name,
            "channels" => [
                "card",
                "bank_transfer",
                "pay_with_bank",
            ],
            "notification_url" => route('korapay.webhook'),
            "customer" => [
                "name" => $user->name,
                "email" => $user->email,
            ],
            "metadata" => [
                "community_id" => (string) $community->id,
                "user_id" => (string) $user->id,
                "billing_type" => $community->billing_type,
            ],
        ];

        try {

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.env.kora_sec'),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post(
                'https://api.korapay.com/merchant/api/v1/charges/initialize',
                $payload
            );

            if ($response->failed()) {
                Log::error('KoraPay initialization failed', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                    'body' => $response->body(),
                    'amount' => $convertedAmount,
                    'currency' => $userCurrency,
                    'reference' => $reference,
                ]);

                throw new \Exception(
                    'KoraPay initialization failed: ' .
                        $response->body()
                );
            }

            if (!$response->successful()) {
                $transaction->update(['status' => 'failed']);
                throw new \Exception('Payment initialization failed');
            }

            return $response->json('data.checkout_url');
        } catch (\Exception $e) {
            $transaction->update(['status' => 'failed']);
            throw new \Exception('KoraPay error: ' . $e->getMessage());
        }
    }

    public function verifyKoraCommunityPayment(string $reference)
    {
        try {
            $res = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.env.kora_sec'),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->get(
                'https://api.korapay.com/merchant/api/v1/charges/' . $reference
            );

            $transaction = Transaction::where('ref', $reference)->first();


            app(TransactionService::class)->markProcessing(
                $transaction,
                $res->json()
            );

            if (!$res->successful()) {
                throw new \Exception('KoraPay verification failed');
            }

            return $res->json();
        } catch (\Exception $e) {
            report($e);
            return [
                'status' => 'failed',
                'message' => 'KoraPay verification error: ' . $e->getMessage()
            ];
        }
    }

    private function flutterwavePaymentLink(Community $community, User $user): string
    {
        $userCurrency = userBaseCurrency();
        $reference = generateTransactionRef('community');
        $idempotencyKey = Str::uuid()->toString();

        $chargeAmount = (float) $community->member_charge;
        $convertedAmount = (float) convertCurrency($chargeAmount, $community->currency, $userCurrency);

        $transaction = $this->transactionService->createTransaction(
            user: $user,
            idempotencyKey: $idempotencyKey,
            provider: 'flutterwave',
            reference: $reference,
            amount: $convertedAmount,
            currency: $userCurrency,
            status: 'initiated',
            action: 'Debit',
            type: 'community_' . $community->billing_type,
            description: 'Payment for community (' . $community->billing_type . '): ' . $community->name,
            meta: [
                'community' => $community->name,
                'community_id' => $community->id,
                'user_id' => $user->id,
                'amount' => $convertedAmount,
                'currency' => $userCurrency,
                'billing_type' => $community->billing_type,
                'billing_interval' => $community->billing_interval,
            ]
        );

        return app(CommunityFlutterwaveService::class)->checkout($community, $user, $transaction);
    }




    /**
     * Creates a "pending" subscription/purchase record for a paid
     * community — for BOTH billing types. Does NOT charge anyone; that's
     * the gateway step, left for you to wire in. Once the gateway confirms
     * payment (webhook/callback), call activate().
     */
    public function initiate(Community $community, User $user, Transaction $transaction): CommunitySubscription
    {
        $baseCurrency = userBaseCurrency();
        $basePrice = (float) $community->monthly_fee;
        $breakdown = CommunityFeeCalculator::breakdown(
            $basePrice,
            (int) ($community->platform_fee_percent ?: 0),
            (string) $community->fee_payer,
        );

        $memberCharge = $breakdown['memberCharge'];
        $platformCut = $breakdown['platformCut'];
        $creatorAmount = $breakdown['creatorPayout'];

        $initiate = CommunitySubscription::create([
            'id' => (string) Str::uuid(),
            'community_id' => $community->id,
            'user_id' => $user->id,
            'billing_type' => $community->billing_type,
            // billing_interval is meaningless for one_off — keep it null
            // rather than copying whatever stray value the community row has.
            'billing_interval' => $community->billing_type === 'subscription'
                ? $community->billing_interval
                : null,
            'fee_payer' => $community->fee_payer,
            'amount' => $memberCharge,
            'platform_fee' => $platformCut,
            'creator_amount' => $creatorAmount,
            'status' => 'pending',
        ]);

        $this->activate($initiate, $transaction);

        return $initiate;
    }

    /**
     * Entry point for gateway webhooks — first payment or one-off purchase.
     */
    public function processSuccessfulPayment(Community $community, User $user, Transaction $transaction): void
    {
        $active = CommunitySubscription::query()
            ->where('community_id', $community->id)
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        if ($active && $active->isRecurring() && $community->billing_type === 'subscription') {
            $this->renew($active, $transaction);

            return;
        }

        if ($active && $active->isOneOff()) {
            app(TransactionService::class)->markSuccessful($transaction, $transaction->meta ?? []);

            return;
        }

        $this->initiate($community, $user, $transaction);
    }

    /**
     * Extend an active recurring subscription after a renewal charge.
     */
    public function renew(CommunitySubscription $subscription, Transaction $transaction): void
    {
        $subscription->update([
            'expires_at' => $this->calculateExpiry($subscription),
            'cancelled_at' => null,
        ]);

        app(CommunityMembershipService::class)->attachMember(
            $subscription->community,
            $subscription->user_id,
        );

        app(TransactionService::class)->markSuccessful(
            $transaction,
            [
                'community_id' => $subscription->community_id,
                'user_id' => $subscription->user_id,
                'amount' => $subscription->amount,
                'currency' => $transaction->currency,
                'billing_type' => $subscription->billing_type,
                'billing_interval' => $subscription->billing_interval,
                'renewal' => true,
            ]
        );

        app(CommunityPayoutService::class)->recordFromSubscription($subscription, $transaction);
    }



    /**
     * Call this once the gateway confirms payment. Grants community
     * membership and marks the subscription/purchase active. Works
     * identically for one_off and subscription — the only difference is
     * what calculateExpiry() returns.
     */
    public function activate(CommunitySubscription $subscription, Transaction $transaction): void
    {
        $subscription->update([
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => $this->calculateExpiry($subscription),
            'cancelled_at' => null,
        ]);

        app(CommunityMembershipService::class)->attachMember(
            $subscription->community,
            $subscription->user_id,
        );

        app(TransactionService::class)->markSuccessful(
            $transaction,
            [
                'community_id' => $subscription->community_id,
                'user_id' => $subscription->user_id,
                'amount' => $subscription->amount,
                'currency' => userBaseCurrency(),
                'billing_type' => $subscription->billing_type,
                'billing_interval' => $subscription->billing_interval,
            ]
        );

        app(CommunityPayoutService::class)->recordFromSubscription($subscription, $transaction);
    }

    public function markFailed(CommunitySubscription $subscription): void
    {
        $subscription->update(['status' => 'failed']);
    }

    /**
     * Called by a scheduled job for recurring subscriptions once
     * expires_at has passed and no renewal payment came in. One-off
     * purchases are never touched by this — they have no expires_at.
     */
    public function expire(CommunitySubscription $subscription): void
    {
        if ($subscription->isOneOff()) {
            return;
        }

        $subscription->update(['status' => 'expired']);

        $subscription->community->members()
            ->wherePivot('id', '!=', null) // no-op guard, keep intent explicit
            ->detach($subscription->user_id);
    }

    private function calculateExpiry(CommunitySubscription $subscription): ?Carbon
    {
        if ($subscription->isOneOff()) {
            // One-time payment buys permanent access — no expiry, ever.
            return null;
        }

        // TODO: confirm these match your real config('community.billing_intervals') keys
        return match ($subscription->billing_interval) {
            'weekly' => now()->addWeek(),
            'monthly' => now()->addMonth(),
            'quarterly' => now()->addMonths(3),
            'biannual' => now()->addMonths(6),
            'yearly', 'annual' => now()->addYear(),
            default => now()->addMonth(),
        };
    }
}
