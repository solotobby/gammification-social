<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\CommunityCategory;
use App\Models\CommunitySubscription;
use App\Models\Level;
use App\Models\PaykoinTransaction;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserLevel;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CrossPlatformPaymentWebhookTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Level $level;
    protected Community $community;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();

        $this->user = User::factory()->create([
            'username' => 'mobile_user',
            'referral_code' => 'MREF1234',
            'email' => 'mobile_user@example.com',
            'name' => 'Mobile User',
        ]);

        Wallet::create([
            'user_id' => $this->user->id,
            'currency' => 'NGN',
            'balance' => 0,
            'promoter_balance' => 0,
            'referral_balance' => 0,
            'paykoin_spendable' => 0,
            'paykoin_earned' => 0,
        ]);

        $this->level = Level::where('name', 'Creator')->first() ?: Level::create([
            'name' => 'Creator',
            'amount' => 10,
            'reg_bonus' => 2,
            'ref_bonus' => 3,
            'min_withdrawal' => 15,
            'earning_per_view' => 2,
            'earning_per_like' => 0.6,
            'earning_per_comment' => 0.7,
        ]);

        $category = CommunityCategory::create([
            'name' => 'Creative Arts',
            'slug' => 'creative-arts',
            'icon' => 'art-icon',
        ]);

        $this->community = Community::create([
            'user_id' => $this->user->id,
            'community_categories_id' => $category->id,
            'name' => 'Exclusive Creators Hub',
            'slug' => 'creators-hub',
            'type' => 'paid',
            'billing_type' => 'one_off',
            'member_charge' => 2000,
            'status' => 'active',
        ]);
    }

    public function test_mobile_initiated_paykoin_topup_is_credited_via_korapay_webhook(): void
    {
        $ref = 'PKN-20260913-11223344';

        $transaction = Transaction::create([
            'user_id' => $this->user->id,
            'ref' => $ref,
            'amount' => 1000,
            'currency' => 'NGN',
            'status' => 'initiated',
            'type' => 'paykoin_topup',
            'action' => 'Debit',
            'description' => 'PayKoin top-up',
            'meta' => [
                'channel' => 'mobile',
                'pk_amount' => 100,
                'charge_amount_ngn' => 1000,
            ],
        ]);

        $payload = [
            'event' => 'charge.success',
            'data' => [
                'reference' => $ref,
                'status' => 'success',
                'amount' => 100000, // 1000 * 100
                'payment_method' => 'card',
                'currency' => 'NGN',
                'metadata' => [
                    'channel' => 'mobile',
                    'type' => 'paykoin_topup',
                    'pk_amount' => 100,
                ],
            ],
        ];

        $response = $this->postJson('/korapay/webhook', $payload);

        $response->assertStatus(200);

        $this->user->refresh();
        $this->assertEquals(100, $this->user->wallet->paykoin_spendable);
        $this->assertEquals('successful', $transaction->fresh()->status);

        $this->assertDatabaseHas('paykoin_transactions', [
            'user_id' => $this->user->id,
            'ref' => $ref,
            'pk_amount' => 100,
            'type' => 'topup',
        ]);

        // Idempotency: re-sending the same webhook should return 200 without double crediting
        $retryResponse = $this->postJson('/korapay/webhook', $payload);
        $retryResponse->assertStatus(200);

        $this->user->refresh();
        $this->assertEquals(100, $this->user->wallet->paykoin_spendable);
    }

    public function test_mobile_initiated_level_upgrade_is_fulfilled_via_korapay_webhook(): void
    {
        $ref = 'PKY-20260913-99887766';

        $transaction = Transaction::create([
            'user_id' => $this->user->id,
            'ref' => $ref,
            'amount' => 5000,
            'currency' => 'NGN',
            'status' => 'initiated',
            'type' => 'subscription_upgrade',
            'action' => 'Debit',
            'description' => 'Mobile upgrade to Creator',
            'meta' => [
                'channel' => 'mobile',
                'level_id' => $this->level->id,
            ],
        ]);

        $payload = [
            'event' => 'charge.success',
            'data' => [
                'reference' => $ref,
                'status' => 'success',
                'amount' => 500000,
                'payment_method' => 'card',
                'currency' => 'NGN',
                'metadata' => [
                    'channel' => 'mobile',
                    'level_id' => $this->level->id,
                ],
            ],
        ];

        $response = $this->postJson('/korapay/webhook', $payload);

        $response->assertStatus(200);

        $this->assertEquals('successful', $transaction->fresh()->status);

        $this->assertDatabaseHas('user_levels', [
            'user_id' => $this->user->id,
            'level_id' => $this->level->id,
            'status' => 'active',
        ]);

        // Registration bonus should be credited to wallet
        $this->user->refresh();
        $expectedBonus = convertToBaseCurrency($this->level->reg_bonus, 'NGN');
        $this->assertEquals($expectedBonus, $this->user->wallet->balance);
    }

    public function test_mobile_initiated_community_subscription_is_activated_via_korapay_webhook(): void
    {
        $ref = 'COM-20260913-55443322';

        $transaction = Transaction::create([
            'user_id' => $this->user->id,
            'ref' => $ref,
            'amount' => 2000,
            'currency' => 'NGN',
            'status' => 'initiated',
            'type' => 'community_one_off',
            'action' => 'Debit',
            'description' => 'Payment for community',
            'meta' => [
                'channel' => 'mobile',
                'community_id' => $this->community->id,
                'user_id' => $this->user->id,
                'billing_type' => 'one_off',
            ],
        ]);

        CommunitySubscription::create([
            'community_id' => $this->community->id,
            'user_id' => $this->user->id,
            'billing_type' => 'one_off',
            'fee_payer' => 'creator',
            'amount' => 2000,
            'platform_fee' => 200,
            'creator_amount' => 1800,
            'gateway_reference' => $ref,
            'status' => 'pending',
        ]);

        $payload = [
            'event' => 'charge.success',
            'data' => [
                'reference' => $ref,
                'status' => 'success',
                'amount' => 200000,
                'payment_method' => 'card',
                'currency' => 'NGN',
            ],
        ];

        $response = $this->postJson('/korapay/webhook', $payload);

        $response->assertStatus(200);

        $this->assertEquals('successful', $transaction->fresh()->status);

        $this->assertDatabaseHas('community_subscriptions', [
            'community_id' => $this->community->id,
            'user_id' => $this->user->id,
            'status' => 'active',
        ]);
    }

    public function test_mobile_initiated_level_upgrade_via_flutterwave_webhook(): void
    {
        $ref = 'PKY-20260913-77665544';
        $secretHash = 'test_flw_hash';
        config(['services.env.flutterwave_webhook_hash' => $secretHash]);

        $transaction = Transaction::create([
            'user_id' => $this->user->id,
            'ref' => $ref,
            'amount' => 5000,
            'currency' => 'NGN',
            'status' => 'initiated',
            'type' => 'subscription_upgrade',
            'action' => 'Debit',
            'description' => 'Mobile upgrade to Creator',
            'meta' => [
                'channel' => 'mobile',
                'level_id' => $this->level->id,
            ],
        ]);

        $payload = [
            'event' => 'charge.completed',
            'data' => [
                'tx_ref' => $ref,
                'status' => 'successful',
                'amount' => 5000,
                'currency' => 'NGN',
                'meta' => [
                    'channel' => 'mobile',
                    'level_id' => $this->level->id,
                ],
            ],
        ];

        $response = $this->withHeaders([
            'verif-hash' => $secretHash,
        ])->postJson('/flutterwave/webhook', $payload);

        $response->assertStatus(200);

        $this->assertEquals('successful', $transaction->fresh()->status);
        $this->assertDatabaseHas('user_levels', [
            'user_id' => $this->user->id,
            'level_id' => $this->level->id,
            'status' => 'active',
        ]);
    }
}
