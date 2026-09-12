<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\PostBoost;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Wallet;
use App\Services\AdminGateService;
use App\Services\PostBoostService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminBoostManagementTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('roles')->insertOrIgnore([
            ['id' => 1, 'name' => 'admin', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'user', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->admin = User::factory()->create([
            'email' => 'admin_' . Str::random(5) . '@payhankey.com',
            'username' => 'admin_' . Str::random(5),
            'referral_code' => strtoupper(Str::random(8)),
            'email_verified_at' => now(),
        ]);
        $this->admin->assignRole('admin');

        $this->regularUser = User::factory()->create([
            'email' => 'user_' . Str::random(5) . '@payhankey.com',
            'username' => 'usr_' . Str::random(5),
            'referral_code' => strtoupper(Str::random(8)),
            'email_verified_at' => now(),
        ]);
        $this->regularUser->assignRole('user');

        Wallet::create([
            'user_id' => $this->regularUser->id,
            'balance' => 50000,
            'promoter_balance' => 0,
            'referral_balance' => 0,
            'currency' => 'NGN',
            'paykoin_spendable' => 1000,
            'paykoin_earned' => 0,
        ]);
    }

    public function test_non_admin_cannot_access_boost_management_panel(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.boosts.index'));
        $this->assertTrue(in_array($response->status(), [403, 302], true));
    }

    public function test_admin_can_access_boost_management_panel(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession([AdminGateService::SESSION_PANEL_ACCESS => true])
            ->get(route('admin.boosts.index'));

        $response->assertStatus(200);
        $response->assertSee('Post Boost & Advertising Management', false);
        $response->assertSee('Total Boost Campaigns');
    }

    public function test_admin_can_toggle_post_boost_on_and_off(): void
    {
        SystemSetting::disableBoost();
        $this->assertFalse(SystemSetting::isBoostEnabled());

        // Toggle ON
        $response = $this->actingAs($this->admin)
            ->withSession([AdminGateService::SESSION_PANEL_ACCESS => true])
            ->post(route('admin.boosts.toggle'), ['state' => '1']);

        $response->assertRedirect();
        $this->assertTrue(SystemSetting::isBoostEnabled());

        // Toggle OFF
        $response = $this->actingAs($this->admin)
            ->withSession([AdminGateService::SESSION_PANEL_ACCESS => true])
            ->post(route('admin.boosts.toggle'), ['state' => '0']);

        $response->assertRedirect();
        $this->assertFalse(SystemSetting::isBoostEnabled());
    }

    public function test_timeline_reflects_post_boost_system_toggle(): void
    {
        $post = Post::create([
            'user_id' => $this->regularUser->id,
            'unicode' => Str::random(8),
            'content' => 'Test post for boost reflection on timeline',
            'status' => 'active',
        ]);

        // When boost is disabled
        SystemSetting::disableBoost();
        $viewDisabled = $this->actingAs($this->regularUser)->blade(
            '<livewire:user.post-content :post="$post" :key="$post->id" />',
            ['post' => $post]
        );
        $this->assertStringNotContainsString('pk-boost-strip', (string) $viewDisabled);
        // Ellipses options menu and analytics must still be present even when boost is disabled
        $this->assertStringContainsString('pk-options-btn', (string) $viewDisabled);
        $this->assertStringContainsString('View analytics', (string) $viewDisabled);

        // When boost is enabled
        SystemSetting::enableBoost();
        $viewEnabled = $this->actingAs($this->regularUser)->blade(
            '<livewire:user.post-content :post="$post" :key="$post->id" />',
            ['post' => $post]
        );
        $this->assertStringContainsString('pk-boost-strip', (string) $viewEnabled);
        $this->assertStringContainsString('Boost Post', (string) $viewEnabled);
        $this->assertStringContainsString('pk-options-btn', (string) $viewEnabled);
        $this->assertStringContainsString('View analytics', (string) $viewEnabled);
    }

    public function test_creating_boost_fails_when_boost_is_disabled(): void
    {
        SystemSetting::disableBoost();

        $post = Post::create([
            'user_id' => $this->regularUser->id,
            'unicode' => Str::random(8),
            'content' => 'Post to test boost disabled exception',
            'status' => 'active',
        ]);

        $boostService = app(PostBoostService::class);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Post boost feature is currently paused by administrators.');

        $boostService->createBoost($this->regularUser, $post, [
            'target_url' => 'https://example.com/item',
            'cta' => 'Shop Now',
            'clicks' => 100,
            'platforms' => ['payhankey' => true, 'partner' => true],
        ]);
    }

    public function test_admin_can_update_individual_campaign_status(): void
    {
        SystemSetting::enableBoost();

        $post = Post::create([
            'user_id' => $this->regularUser->id,
            'unicode' => Str::random(8),
            'content' => 'Post for status update test',
            'status' => 'active',
        ]);

        $boostService = app(PostBoostService::class);
        $boost = $boostService->createBoost($this->regularUser, $post, [
            'target_url' => 'https://example.com/promo',
            'cta' => 'Visit Website',
            'clicks' => 100,
            'platforms' => ['payhankey' => true, 'partner' => true],
        ]);

        $this->assertEquals('active', $boost->fresh()->status);
        $this->assertTrue((bool) $post->fresh()->is_boosted);

        // Admin pauses campaign
        $response = $this->actingAs($this->admin)
            ->withSession([AdminGateService::SESSION_PANEL_ACCESS => true])
            ->post(route('admin.boosts.status', $boost), ['status' => 'paused']);

        $response->assertRedirect();
        $this->assertEquals('paused', $boost->fresh()->status);
        $this->assertFalse((bool) $post->fresh()->is_boosted);

        // Admin resumes campaign
        $response = $this->actingAs($this->admin)
            ->withSession([AdminGateService::SESSION_PANEL_ACCESS => true])
            ->post(route('admin.boosts.status', $boost), ['status' => 'active']);

        $response->assertRedirect();
        $this->assertEquals('active', $boost->fresh()->status);
        $this->assertTrue((bool) $post->fresh()->is_boosted);
    }
}
