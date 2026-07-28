<?php
// app/Services/CommunitySubscriptionService.php
namespace App\Services;

use App\Models\Community;
use App\Models\CommunitySubscription;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Carbon;
use App\Services\Payments\PaymentGatewayResolver;
use Illuminate\Support\Str;

class CommunitySubscriptionService
{
    //  public function __construct(private readonly PaymentGatewayResolver $gateways) {}

    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService, private readonly PaymentGatewayResolver $gateways)
    {
        $this->transactionService = $transactionService;
    }


    public function pendingOrActiveFor(Community $community, User $user): ?CommunitySubscription
    {
        return CommunitySubscription::where('community_id', $community->id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'active'])
            ->latest()
            ->first();
    }

    /**
     * Creates a "pending" subscription/purchase record for a paid
     * community — for BOTH billing types. Does NOT charge anyone; that's
     * the gateway step, left for you to wire in. Once the gateway confirms
     * payment (webhook/callback), call activate().
     */
    public function initiate(Community $community, User $user): CommunitySubscription
    {
        $baseCurrency = userBaseCurrency();
        $rate = ($community->platform_fee_percent ?: 0) / 100;
        $basePrice = (float) $community->monthly_fee;

        $memberCharge = $community->fee_payer === 'members'
            ? round($basePrice / (1 - $rate), 2)
            : $basePrice;

        $platformCut = round($memberCharge * $rate, 2);
        $creatorAmount = round($memberCharge - $platformCut, 2);

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

        if ($initiate) {

            $amount = $memberCharge;
            $reference = generateTransactionRef();
            $idempotencyKey = $data['idempotency_key']
                ?? Str::uuid()->toString();

            $existingTransaction = Transaction::query()
                ->where('idempotency_key', $idempotencyKey)
                ->first();


            if (!$existingTransaction) {

                // Create transaction
               $this->transactionService->createTransaction(
                    user: $user,
                    idempotencyKey: $idempotencyKey,
                    provider: 'kora',
                    reference: $reference,
                    amount: $amount,
                    currency: $baseCurrency,
                    status: 'pending',
                    action: 'Debit',
                    type:  'community_'.$community->billing_type,
                    description: '',
                    meta: [
                        'community_subscription_id' => $initiate->id,
                        'community_id' => $community->id
                    ]
                );
            }
        }


        return $initiate;
    }

    public function checkoutUrl(CommunitySubscription $subscription): string
    {
        return $this->gateways->forSubscription($subscription)->initializeCheckout($subscription);
    }

    /**
     * Call this once the gateway confirms payment. Grants community
     * membership and marks the subscription/purchase active. Works
     * identically for one_off and subscription — the only difference is
     * what calculateExpiry() returns.
     */
    public function activate(CommunitySubscription $subscription): void
    {
        $subscription->update([
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => $this->calculateExpiry($subscription),
            'cancelled_at' => null,
        ]);

        $subscription->community->members()->syncWithoutDetaching([
            $subscription->user_id => [
                'id' => (string) Str::uuid(),
                'role' => 'member',
                'status' => 'active',
            ],
        ]);
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
            'yearly', 'annual' => now()->addYear(),
            default => now()->addMonth(),
        };
    }
}
