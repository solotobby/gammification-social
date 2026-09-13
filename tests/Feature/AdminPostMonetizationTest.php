<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminPostMonetizationTest extends TestCase
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
            'balance' => 1000,
            'promoter_balance' => 0,
            'referral_balance' => 0,
            'currency' => 'NGN',
            'paykoin_spendable' => 100,
            'paykoin_earned' => 0,
        ]);
    }

    public function test_non_admin_cannot_access_posts_management(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.posts.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_post_list_with_monetization_status(): void
    {
        $monetizedPost = Post::create([
            'user_id' => $this->regularUser->id,
            'unicode' => Str::random(8),
            'content' => 'Excited to announce our new product update launch today with multiple new features!',
            'status' => 'LIVE',
            'is_monetized' => true,
        ]);

        $unmonetizedPost = Post::create([
            'user_id' => $this->regularUser->id,
            'unicode' => Str::random(8),
            'content' => 'hi',
            'status' => 'LIVE',
            'is_monetized' => false,
            'monetization_note' => 'Post is too short for monetization.',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.posts.index'));

        $response->assertOk();
        $response->assertSee('Timeline Posts');
        $response->assertSee('Monetized');
        $response->assertSee('Ineligible');
        $response->assertSee('Eligible');
        $response->assertSee('Post is too short for monetization.');
    }

    public function test_admin_can_filter_posts_by_monetization_eligibility(): void
    {
        $eligiblePost = Post::create([
            'user_id' => $this->regularUser->id,
            'unicode' => Str::random(8),
            'content' => 'High quality unique post content about technology growth today in Nigeria.',
            'status' => 'LIVE',
            'is_monetized' => true,
        ]);

        $ineligiblePost = Post::create([
            'user_id' => $this->regularUser->id,
            'unicode' => Str::random(8),
            'content' => 'ok ok ok ok ok ok ok ok',
            'status' => 'LIVE',
            'is_monetized' => false,
            'monetization_note' => 'Repetitive word spam.',
        ]);

        // Filter eligible only
        $resEligible = $this->actingAs($this->admin)->get(route('admin.posts.index', ['monetization' => 'eligible']));
        $resEligible->assertOk();
        $resEligible->assertSee('High quality unique post content');
        $resEligible->assertDontSee('ok ok ok ok ok ok ok ok');

        // Filter ineligible only
        $resIneligible = $this->actingAs($this->admin)->get(route('admin.posts.index', ['monetization' => 'ineligible']));
        $resIneligible->assertOk();
        $resIneligible->assertSee('ok ok ok ok ok ok ok ok');
        $resIneligible->assertDontSee('High quality unique post content');
    }

    public function test_admin_can_manually_update_monetization_status(): void
    {
        $post = Post::create([
            'user_id' => $this->regularUser->id,
            'unicode' => Str::random(8),
            'content' => 'short text',
            'status' => 'LIVE',
            'is_monetized' => false,
            'monetization_note' => 'Post is too short.',
        ]);

        $this->assertFalse((bool) $post->is_monetized);

        // Admin manual override to eligible
        $response = $this->actingAs($this->admin)->post(route('admin.posts.monetization', $post), [
            'is_monetized' => 1,
            'monetization_note' => 'Manually approved by administrator.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $post->refresh();
        $this->assertTrue((bool) $post->is_monetized);
        $this->assertEquals('Manually approved by administrator.', $post->monetization_note);

        // Admin manual override to ineligible
        $response2 = $this->actingAs($this->admin)->post(route('admin.posts.monetization', $post), [
            'is_monetized' => 0,
            'monetization_note' => 'Disqualified due to content policy.',
        ]);

        $response2->assertRedirect();
        $response2->assertSessionHas('success');

        $post->refresh();
        $this->assertFalse((bool) $post->is_monetized);
        $this->assertEquals('Disqualified due to content policy.', $post->monetization_note);
    }

    public function test_admin_can_reevaluate_post_quality_engine(): void
    {
        // Initially force is_monetized = true on a garbage post
        $post = Post::create([
            'user_id' => $this->regularUser->id,
            'unicode' => Str::random(8),
            'content' => 'hi',
            'status' => 'LIVE',
            'is_monetized' => true,
        ]);

        $this->assertTrue((bool) $post->is_monetized);

        // Run re-evaluate
        $response = $this->actingAs($this->admin)->post(route('admin.posts.re-evaluate', $post));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $post->refresh();
        // Quality engine should correctly flag 'hi' as ineligible
        $this->assertFalse((bool) $post->is_monetized);
        $this->assertNotNull($post->monetization_note);
    }

    public function test_admin_can_view_post_show_with_monetization_card(): void
    {
        $post = Post::create([
            'user_id' => $this->regularUser->id,
            'unicode' => Str::random(8),
            'content' => 'banana apple sky river chair table laptop road',
            'status' => 'LIVE',
            'is_monetized' => false,
            'monetization_note' => 'Post does not form a complete sentence (missing an action or verb predicate).',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.posts.show', $post));

        $response->assertOk();
        $response->assertSee('Monetization Status');
        $response->assertSee('Ineligible');
        $response->assertSee('missing an action or verb predicate');
        $response->assertSee('Save Monetization Status');
        $response->assertSee('Re-evaluate with Quality Engine');
    }
}
