<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\PostBoost;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Wallet;
use App\Services\TimelineFeedService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class TimelineFeedInterleavingTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Feed Tester',
            'username' => 'feedtester_' . Str::lower(Str::random(6)),
            'referral_code' => strtoupper(Str::random(8)),
            'email_verified_at' => now(),
        ]);

        Wallet::create([
            'user_id' => $this->user->id,
            'balance' => 0,
            'promoter_balance' => 0,
            'referral_balance' => 0,
            'currency' => 'NGN',
            'paykoin_spendable' => 10000,
        ]);

        SystemSetting::enableBoost();
        Cache::forget(TimelineFeedService::CACHE_KEY);
    }

    public function test_boosted_posts_are_interleaved_in_between_and_not_stacked_at_the_top(): void
    {
        // 1. Create 10 organic posts
        $organicPosts = collect();
        for ($i = 1; $i <= 10; $i++) {
            $organicPosts->push(Post::create([
                'user_id' => $this->user->id,
                'unicode' => Str::random(8),
                'content' => "Organic Post #{$i}",
                'status' => 'LIVE',
                'is_boosted' => false,
                'created_at' => now()->subMinutes(100 - $i),
            ]));
        }

        // 2. Create 2 boosted posts
        $boostedPosts = collect();
        for ($b = 1; $b <= 2; $b++) {
            $post = Post::create([
                'user_id' => $this->user->id,
                'unicode' => Str::random(8),
                'content' => "Boosted Ad #{$b}",
                'status' => 'LIVE',
                'is_boosted' => true,
                'created_at' => now(), // Even if created more recently, must NOT be stacked at top
            ]);

            PostBoost::create([
                'user_id' => $this->user->id,
                'post_id' => $post->id,
                'target_url' => 'https://example.com/ad' . $b,
                'cta' => 'Learn More',
                'total_clicks' => 100,
                'delivered_clicks' => 0,
                'remaining_clicks' => 100,
                'pk_cost' => 300,
                'rate_pk' => 3,
                'platform_payhankey' => true,
                'platform_partner' => true,
                'status' => 'active',
                'ref' => 'REF_' . $b,
            ]);

            $boostedPosts->push($post);
        }

        Cache::forget(TimelineFeedService::CACHE_KEY);

        $feedService = app(TimelineFeedService::class);
        $result = $feedService->buildFeed(
            organicQuery: Post::where('status', 'LIVE'),
            targetCount: 12,
            seed: 12345,
            cadence: 4
        );

        $feedPosts = $result['posts'];

        // Feed must not be empty
        $this->assertCount(12, $feedPosts);

        // First 4 posts must be organic (NOT boosted!)
        for ($k = 0; $k < 4; $k++) {
            $this->assertFalse(
                (bool) $feedPosts[$k]->is_boosted,
                "Expected post at index {$k} to be organic, but found boosted post."
            );
        }

        // The 5th post (index 4) must be a boosted post injected in between
        $this->assertTrue(
            (bool) $feedPosts[4]->is_boosted,
            "Expected post at index 4 to be the first interleaved boosted post."
        );

        // Posts at index 5, 6, 7, 8 must be organic
        for ($k = 5; $k < 9; $k++) {
            $this->assertFalse(
                (bool) $feedPosts[$k]->is_boosted,
                "Expected post at index {$k} to be organic, but found boosted post."
            );
        }

        // The 10th post (index 9) must be the second interleaved boosted post
        $this->assertTrue(
            (bool) $feedPosts[9]->is_boosted,
            "Expected post at index 9 to be the second interleaved boosted post."
        );
    }

    public function test_boosted_posts_are_not_served_when_boost_is_disabled(): void
    {
        SystemSetting::disableBoost();

        for ($i = 1; $i <= 5; $i++) {
            Post::create([
                'user_id' => $this->user->id,
                'unicode' => Str::random(8),
                'content' => "Organic Post #{$i}",
                'status' => 'LIVE',
                'is_boosted' => false,
            ]);
        }

        $boostedPost = Post::create([
            'user_id' => $this->user->id,
            'unicode' => Str::random(8),
            'content' => "Boosted Post",
            'status' => 'LIVE',
            'is_boosted' => true,
        ]);

        PostBoost::create([
            'user_id' => $this->user->id,
            'post_id' => $boostedPost->id,
            'target_url' => 'https://example.com',
            'cta' => 'Buy Now',
            'total_clicks' => 50,
            'delivered_clicks' => 0,
            'remaining_clicks' => 50,
            'pk_cost' => 150,
            'rate_pk' => 3,
            'platform_payhankey' => true,
            'status' => 'active',
            'ref' => 'REF_DIS',
        ]);

        $feedService = app(TimelineFeedService::class);
        $result = $feedService->buildFeed(
            organicQuery: Post::where('status', 'LIVE'),
            targetCount: 10,
            cadence: 2
        );

        $hasAnyBoosted = $result['posts']->contains(fn ($p) => (bool) $p->is_boosted);
        $this->assertFalse($hasAnyBoosted, 'No boosted post should appear in feed when boost is disabled.');
    }

    public function test_scalable_sampling_from_large_pool_of_boosted_campaigns(): void
    {
        $feedService = app(TimelineFeedService::class);

        // Simulate a pool of 50,000 active boost campaign IDs
        $pool = [];
        for ($i = 0; $i < 50000; $i++) {
            $pool[] = "uuid-boost-post-{$i}";
        }

        $startTime = microtime(true);
        $sample = $feedService->selectSample($pool, 5, 42);
        $duration = microtime(true) - $startTime;

        // Must select exactly 5 unique items
        $this->assertCount(5, $sample);
        $this->assertCount(5, array_unique($sample));

        // Sub-millisecond execution even with 50,000 candidates
        $this->assertLessThan(0.01, $duration, 'Sampling from 50,000 candidate IDs must complete in < 10ms.');
    }

    public function test_pagination_stability_with_seed(): void
    {
        $feedService = app(TimelineFeedService::class);

        $pool = [];
        for ($i = 0; $i < 1000; $i++) {
            $pool[] = "id_{$i}";
        }

        $seed = 98765;
        // Page 1 needs 4 items
        $page1Sample = $feedService->selectSample($pool, 4, $seed);
        // Page 2 needs 8 items (cumulative)
        $page2Sample = $feedService->selectSample($pool, 8, $seed);

        // The first 4 items on page 2 must be identical to page 1
        $this->assertEquals($page1Sample, array_slice($page2Sample, 0, 4));
        $this->assertCount(8, array_unique($page2Sample));
    }

    public function test_timeline_livewire_component_renders_interleaved_feed(): void
    {
        for ($i = 1; $i <= 8; $i++) {
            Post::create([
                'user_id' => $this->user->id,
                'unicode' => Str::random(8),
                'content' => "Timeline Organic #{$i}",
                'status' => 'LIVE',
                'is_boosted' => false,
            ]);
        }

        $boosted = Post::create([
            'user_id' => $this->user->id,
            'unicode' => Str::random(8),
            'content' => "Timeline Boosted Ad",
            'status' => 'LIVE',
            'is_boosted' => true,
        ]);

        PostBoost::create([
            'user_id' => $this->user->id,
            'post_id' => $boosted->id,
            'target_url' => 'https://example.com/promo',
            'cta' => 'Claim Offer',
            'total_clicks' => 100,
            'delivered_clicks' => 0,
            'remaining_clicks' => 100,
            'pk_cost' => 300,
            'rate_pk' => 3,
            'platform_payhankey' => true,
            'status' => 'active',
            'ref' => 'REF_LW',
        ]);

        Cache::forget(TimelineFeedService::CACHE_KEY);

        $test = Livewire::actingAs($this->user)->test(\App\Livewire\User\Timeline::class);
        $posts = $test->get('posts');

        $this->assertNotEmpty($posts);
        // First post is organic
        $this->assertFalse((bool) $posts->first()->is_boosted);
    }
}
