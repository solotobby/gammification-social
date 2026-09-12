<?php

namespace Tests\Feature;

use App\Models\PaykoinTransaction;
use App\Models\Post;
use App\Models\PostBoost;
use App\Models\PostBoostClick;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\PostBoostedNotification;
use App\Services\PostBoostService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class PostBoostTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Wallet $wallet;
    protected Post $post;
    protected PostBoostService $service;

    protected function setUp(): void
    {
        parent::setUp();

        \App\Models\SystemSetting::enableBoost();

        $this->user = User::factory()->create([
            'username' => 'testuser_' . Str::lower(Str::random(6)),
            'referral_code' => strtoupper(Str::random(8)),
            'email_verified_at' => now(),
        ]);

        $this->wallet = Wallet::create([
            'user_id' => $this->user->id,
            'balance' => 50000,
            'promoter_balance' => 0,
            'referral_balance' => 0,
            'currency' => 'NGN',
            'paykoin_spendable' => 1000,
            'paykoin_earned' => 0,
        ]);

        $this->post = Post::create([
            'user_id' => $this->user->id,
            'unicode' => Str::random(8),
            'content' => 'Test boosting this post to thousands of users!',
            'status' => 'LIVE',
            'views' => 10,
            'clicks' => 0,
        ]);

        $this->service = app(PostBoostService::class);
    }

    public function test_can_create_boost_and_debit_paykoin(): void
    {
        Notification::fake();

        $data = [
            'target_url' => 'https://example.com/product',
            'cta' => 'Shop Now',
            'clicks' => 100,
            'platforms' => ['payhankey' => true, 'partner' => true],
        ];

        $boost = $this->service->createBoost($this->user, $this->post, $data);

        $this->assertInstanceOf(PostBoost::class, $boost);
        $this->assertEquals(100, $boost->total_clicks);
        $this->assertEquals(100, $boost->remaining_clicks);
        $this->assertEquals(0, $boost->delivered_clicks);
        $this->assertEquals(300, $boost->pk_cost); // 100 * 3
        $this->assertEquals('active', $boost->status);

        // Verify wallet debit
        $this->wallet->refresh();
        $this->assertEquals(700, $this->wallet->paykoin_spendable); // 1000 - 300

        // Verify transaction ledger
        $tx = PaykoinTransaction::where('user_id', $this->user->id)->where('type', 'post_boost')->first();
        $this->assertNotNull($tx);
        $this->assertEquals(-300, $tx->pk_amount);

        // Verify post flags
        $this->post->refresh();
        $this->assertTrue($this->post->is_boosted);
        $this->assertTrue($this->post->monetization_paused);

        // Verify notification sent
        Notification::assertSentTo($this->user, PostBoostedNotification::class);
    }

    public function test_cannot_boost_without_enough_paykoin(): void
    {
        $this->wallet->update(['paykoin_spendable' => 50]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Insufficient PayKoin balance/i');

        $this->service->createBoost($this->user, $this->post, [
            'target_url' => 'https://example.com/product',
            'cta' => 'Shop Now',
            'clicks' => 100, // needs 300 PK
        ]);
    }

    public function test_record_click_logs_location_device_and_browser(): void
    {
        $boost = $this->service->createBoost($this->user, $this->post, [
            'target_url' => 'https://example.com/destination',
            'cta' => 'Order Now',
            'clicks' => 10,
        ]);

        $request = Request::create('/boost/click/'.$boost->id, 'GET', [], [], [], [
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5 Mobile/15E148 Safari/604.1',
            'REMOTE_ADDR' => '127.0.0.1',
            'HTTP_REFERER' => 'https://partnerwebsite.com/ad-slot',
        ]);

        $targetUrl = $this->service->recordClick($boost, $request, 'partner');

        $this->assertEquals('https://example.com/destination', $targetUrl);

        $click = PostBoostClick::where('post_boost_id', $boost->id)->first();
        $this->assertNotNull($click);
        $this->assertEquals('partner', $click->platform);
        $this->assertEquals('Mobile', $click->device);
        $this->assertEquals('Safari', $click->browser);
        $this->assertEquals('iOS (iPhone)', $click->os);
        $this->assertNotNull($click->ip);

        // Verify delivered clicks incremented and remaining clicks decremented
        $boost->refresh();
        $this->assertEquals(1, $boost->delivered_clicks);
        $this->assertEquals(9, $boost->remaining_clicks);
    }

    public function test_click_tracking_redirect_endpoint(): void
    {
        $boost = $this->service->createBoost($this->user, $this->post, [
            'target_url' => 'https://example.com/my-shop',
            'cta' => 'Visit Store',
            'clicks' => 25,
        ]);

        $response = $this->get(route('boost.click', [
            'boostId' => $boost->id,
            'platform' => 'payhankey',
        ]));

        $response->assertRedirect('https://example.com/my-shop');
        $this->assertDatabaseHas('post_boost_clicks', [
            'post_boost_id' => $boost->id,
            'platform' => 'payhankey',
        ]);
    }

    public function test_partner_syndication_api(): void
    {
        $this->service->createBoost($this->user, $this->post, [
            'target_url' => 'https://example.com/offer',
            'cta' => 'Get Deal',
            'clicks' => 50,
            'platforms' => ['payhankey' => true, 'partner' => true],
        ]);

        $response = $this->getJson(route('api.boosted-ads'));

        $response->assertOk();
        $response->assertJsonStructure([
            'status',
            'count',
            'ads' => [
                '*' => ['boost_id', 'post_id', 'author', 'content', 'cta', 'click_url'],
            ],
        ]);
    }

    public function test_post_analytics_renders_boost_tab_and_toggles_pause_resume(): void
    {
        $boost = $this->service->createBoost($this->user, $this->post, [
            'target_url' => 'https://example.com/shop',
            'cta' => 'Shop Now',
            'clicks' => 100,
        ]);

        // Record a mock click
        PostBoostClick::create([
            'post_boost_id' => $boost->id,
            'post_id' => $this->post->id,
            'platform' => 'payhankey',
            'country' => 'Nigeria',
            'city' => 'Lagos',
            'device' => 'Mobile',
            'browser' => 'Chrome',
            'os' => 'Android',
        ]);
        $boost->increment('delivered_clicks');
        $boost->decrement('remaining_clicks');

        $this->actingAs($this->user);

        $testable = \Livewire\Livewire::test(\App\Livewire\User\PostAnalytics::class, ['id' => $this->post->id]);
        $testable->assertStatus(200);

        // Switch to boost tab
        $testable->call('setTab', 'boost');
        $testable->assertSet('tab', 'boost');
        $testable->assertSee('Guaranteed Clicks Delivered');
        $testable->assertSee('Lagos, Nigeria');
        $testable->assertSee('Shop Now');

        // Pause campaign
        $testable->call('toggleBoostStatus');
        $boost->refresh();
        $this->assertEquals('paused', $boost->status);
        $this->post->refresh();
        $this->assertFalse($this->post->is_boosted);

        // Resume campaign
        $testable->call('toggleBoostStatus');
        $boost->refresh();
        $this->assertEquals('active', $boost->status);
        $this->post->refresh();
        $this->assertTrue($this->post->is_boosted);
    }
}

