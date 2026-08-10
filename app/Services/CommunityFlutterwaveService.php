<?php

namespace App\Services;

use App\Models\Community;
use App\Models\CommunityPaymentPlan;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class CommunityFlutterwaveService
{
    private string $baseUrl;

    private string $secretKey;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.env.flutterwave_base_url'), '/');
        $this->secretKey = (string) config('services.env.flutterwave_secret_key');
    }

    /**
     * Initialize Flutterwave checkout for a paid community (non-NGN only).
     */
    public function checkout(Community $community, User $user, Transaction $transaction): string
    {
        $currency = strtoupper((string) $transaction->currency);
        $amount = (float) $transaction->amount;

        $payload = [
            'tx_ref' => $transaction->ref,
            'amount' => $amount,
            'currency' => $currency,
            'redirect_url' => route('community.show', $community),
            'customer' => [
                'email' => $user->email,
                'name' => $user->name,
            ],
            'customizations' => [
                'title' => $community->name,
            ],
            'meta' => [
                'community_id' => $community->id,
                'user_id' => $user->id,
                'billing_type' => $community->billing_type,
                'billing_interval' => $community->billing_interval,
                'type' => 'community_' . $community->billing_type,
            ],
        ];

        if ($community->billing_type === 'subscription') {
            $payload['payment_plan'] = $this->getOrCreatePaymentPlan($community, $currency, $amount);
        }

        $response = Http::withToken($this->secretKey)
            ->acceptJson()
            ->timeout(30)
            ->post("{$this->baseUrl}/payments", $payload);

        if (! $response->successful() || $response->json('status') !== 'success') {
            Log::error('Flutterwave community checkout failed', [
                'community_id' => $community->id,
                'response' => $response->json(),
                'body' => $response->body(),
            ]);

            throw new RuntimeException(
                'Flutterwave payment initialization failed: ' . $response->body()
            );
        }

        $link = $response->json('data.link');

        if (! $link) {
            throw new RuntimeException('Flutterwave payment link missing.');
        }

        $transaction->update([
            'status' => 'initiated',
            'payment_link' => $link,
            'gateway_response' => $response->json(),
        ]);

        return $link;
    }

    /**
     * Reuse an existing Flutterwave plan for this community/currency/interval,
     * or create one when the price changes.
     */
    public function getOrCreatePaymentPlan(Community $community, string $currency, float $amount): string
    {
        $interval = (string) $community->billing_interval;

        $existing = CommunityPaymentPlan::query()
            ->where('community_id', $community->id)
            ->where('currency', $currency)
            ->where('billing_interval', $interval)
            ->where('status', 'active')
            ->first();

        if ($existing && round((float) $existing->amount, 2) === round($amount, 2)) {
            return $existing->flutterwave_plan_id;
        }

        $planId = $this->createPaymentPlan($community, $currency, $amount, $interval);

        CommunityPaymentPlan::updateOrCreate(
            [
                'community_id' => $community->id,
                'currency' => $currency,
                'billing_interval' => $interval,
            ],
            [
                'id' => (string) Str::uuid(),
                'amount' => $amount,
                'flutterwave_plan_id' => $planId,
                'flutterwave_plan_token' => null,
                'status' => 'active',
            ]
        );

        return $planId;
    }

    private function createPaymentPlan(
        Community $community,
        string $currency,
        float $amount,
        string $billingInterval,
    ): string {
        $payload = [
            'name' => "{$community->name} — {$billingInterval}",
            'amount' => $amount,
            'currency' => $currency,
            'interval' => $this->mapInterval($billingInterval),
            'duration' => 60,
        ];

        $response = Http::withToken($this->secretKey)
            ->acceptJson()
            ->timeout(30)
            ->post("{$this->baseUrl}/payment-plans", $payload);

        if (! $response->successful() || $response->json('status') !== 'success') {
            Log::error('Flutterwave community payment plan creation failed', [
                'community_id' => $community->id,
                'payload' => $payload,
                'response' => $response->json(),
            ]);

            throw new RuntimeException(
                'Failed to create Flutterwave subscription plan: ' . $response->body()
            );
        }

        $planId = (string) data_get($response->json(), 'data.id');

        if ($planId === '') {
            throw new RuntimeException('Flutterwave plan id missing from response.');
        }

        Log::info('Flutterwave community payment plan created', [
            'community_id' => $community->id,
            'plan_id' => $planId,
            'currency' => $currency,
            'amount' => $amount,
            'interval' => $billingInterval,
        ]);

        return $planId;
    }

    /**
     * Flutterwave subscription renewals arrive with a fresh tx_ref that was
     * not created at checkout — resolve the community + user and record it.
     */
    public function resolveRenewalTransaction(
        string $txRef,
        float $amount,
        string $currency,
        array $gatewayData,
    ): ?Transaction {
        if (Transaction::query()->where('ref', $txRef)->exists()) {
            return Transaction::query()->where('ref', $txRef)->first();
        }

        $meta = data_get($gatewayData, 'meta', []);
        if (is_string($meta)) {
            $meta = json_decode($meta, true) ?? [];
        }

        $communityId = $meta['community_id'] ?? null;
        $userId = $meta['user_id'] ?? null;

        if (! $communityId) {
            $planId = data_get($gatewayData, 'payment_plan')
                ?? data_get($gatewayData, 'plan')
                ?? data_get($gatewayData, 'payment_plan_id');

            if ($planId) {
                $communityId = CommunityPaymentPlan::query()
                    ->where('flutterwave_plan_id', (string) $planId)
                    ->value('community_id');
            }
        }

        if (! $communityId) {
            return null;
        }

        $community = Community::find($communityId);
        if (! $community) {
            return null;
        }

        $user = $userId
            ? User::find($userId)
            : User::query()->where('email', data_get($gatewayData, 'customer.email'))->first();

        if (! $user) {
            return null;
        }

        return app(TransactionService::class)->createTransaction(
            user: $user,
            idempotencyKey: 'flw-renewal-' . $txRef,
            provider: 'flutterwave',
            reference: $txRef,
            amount: $amount,
            currency: $currency,
            status: 'initiated',
            action: 'Debit',
            type: 'community_' . $community->billing_type,
            description: 'Community subscription renewal: ' . $community->name,
            meta: [
                'community_id' => $community->id,
                'user_id' => $user->id,
                'billing_type' => $community->billing_type,
                'billing_interval' => $community->billing_interval,
                'renewal' => true,
            ],
        );
    }

    private function mapInterval(string $billingInterval): string
    {
        return match ($billingInterval) {
            'weekly' => 'weekly',
            'monthly' => 'monthly',
            'quarterly' => 'quarterly',
            'biannual' => 'bi-annually',
            'annual', 'yearly' => 'yearly',
            default => 'monthly',
        };
    }
}
