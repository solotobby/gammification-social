<?php

namespace Tests\Feature;

use App\Models\Follow;
use App\Models\Post;
use App\Models\PostBoost;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\UserComment;
use App\Models\UserLike;
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

    public function test_dynamic_cadence_interleaves_between_5_and_8_posts(): void
    {
        $feedService = app(TimelineFeedService::class);

        $organic = collect();
        for ($i = 1; $i <= 30; $i++) {
            $organic->push((object) ['id' => "org_{$i}", 'is_boosted' => false]);
        }

        $boosted = collect();
        for ($b = 1; $b <= 5; $b++) {
            $boosted->push((object) ['id' => "boost_{$b}", 'is_boosted' => true]);
        }

        $seed = 54321;
        $interleaved = $feedService->interleave(
            organicPosts: $organic,
            boostedPosts: $boosted,
            targetCount: 30,
            cadence: null, // dynamic 5-8 cadence
            seed: $seed
        );

        $this->assertNotEmpty($interleaved);

        // Calculate intervals between boosted posts
        $consecutiveOrganic = 0;
        $foundBoosted = false;

        foreach ($interleaved as $item) {
            if ($item->is_boosted) {
                if ($foundBoosted) {
                    $this->assertGreaterThanOrEqual(5, $consecutiveOrganic, "Interval between boosted posts was {$consecutiveOrganic}, expected >= 5");
                    $this->assertLessThanOrEqual(8, $consecutiveOrganic, "Interval between boosted posts was {$consecutiveOrganic}, expected <= 8");
                }
                $consecutiveOrganic = 0;
                $foundBoosted = true;
            } else {
                $consecutiveOrganic++;
            }
        }

        $this->assertTrue($foundBoosted, 'At least one boosted post should be interleaved.');
    }

    protected function createTestUser(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'username' => 'testuser_' . Str::lower(Str::random(6)),
            'referral_code' => strtoupper(Str::random(8)),
            'email_verified_at' => now(),
        ], $overrides));
    }

    public function test_for_you_prioritizes_warm_network_with_recency_guardrail_over_cold_posts(): void
    {
        $friend = $this->createTestUser(['username' => 'friend_' . Str::lower(Str::random(6))]);
        $stranger = $this->createTestUser(['username' => 'stranger_' . Str::lower(Str::random(6))]);

        // User follows friend (making friend part of warm network)
        Follow::create([
            'follower_id' => $this->user->id,
            'following_id' => $friend->id,
        ]);

        $feedService = app(TimelineFeedService::class);
        $feedService->clearUserCache($this->user->id);

        // Warm post created 5 days ago (within 4-10 day recency guardrail)
        $warmPost = Post::create([
            'user_id' => $friend->id,
            'unicode' => Str::random(8),
            'content' => 'Warm Post 5 days old',
            'status' => 'LIVE',
            'is_boosted' => false,
            'created_at' => now()->subDays(5),
        ]);

        // Cold post by stranger
        $coldPost = Post::create([
            'user_id' => $stranger->id,
            'unicode' => Str::random(8),
            'content' => 'Cold Post by stranger',
            'status' => 'LIVE',
            'is_boosted' => false,
            'views' => 10,
            'created_at' => now()->subHours(2),
        ]);

        $result = $feedService->buildFeedForTab(
            tab: 'for_you',
            userId: $this->user->id,
            targetCount: 10,
            seed: 42
        );

        $postIds = $result['posts']->pluck('id')->all();
        $this->assertContains($warmPost->id, $postIds);

        // Warm post is in Tier 1 and appears before or alongside discovery
        $warmIndex = array_search($warmPost->id, $postIds, true);
        $coldIndex = array_search($coldPost->id, $postIds, true);

        $this->assertLessThan($coldIndex, $warmIndex, 'Tier 1 warm network post should appear before Tier 2 discovery post.');
    }

    public function test_for_you_falls_back_to_platform_discovery_when_warm_network_is_exhausted(): void
    {
        $popularCreator = $this->createTestUser(['username' => 'creator_' . Str::lower(Str::random(6))]);

        // High engagement post from creator not in warm circle
        $viralPost = Post::create([
            'user_id' => $popularCreator->id,
            'unicode' => Str::random(8),
            'content' => 'Trending viral post',
            'status' => 'LIVE',
            'is_boosted' => false,
            'views' => 5000,
            'likes' => 300,
            'comments' => 150,
            'comment_external' => 50,
            'created_at' => now()->subHours(6),
        ]);

        $feedService = app(TimelineFeedService::class);
        $feedService->clearUserCache($this->user->id);

        $result = $feedService->buildFeedForTab(
            tab: 'for_you',
            userId: $this->user->id,
            targetCount: 10,
            seed: 123
        );

        $postIds = $result['posts']->pluck('id')->all();
        $this->assertContains($viralPost->id, $postIds, 'Viral post should be surfaced via Tier 2 platform discovery.');
    }

    public function test_following_tab_only_returns_mutual_connections(): void
    {
        $mutualUser = $this->createTestUser(['username' => 'mutual_' . Str::lower(Str::random(6))]);
        $oneWayFollowed = $this->createTestUser(['username' => 'oneway1_' . Str::lower(Str::random(6))]);
        $oneWayFollower = $this->createTestUser(['username' => 'oneway2_' . Str::lower(Str::random(6))]);

        // Mutual: User follows mutualUser AND mutualUser follows User
        Follow::create(['follower_id' => $this->user->id, 'following_id' => $mutualUser->id]);
        Follow::create(['follower_id' => $mutualUser->id, 'following_id' => $this->user->id]);

        // One-way: User follows oneWayFollowed (but they don't follow back)
        Follow::create(['follower_id' => $this->user->id, 'following_id' => $oneWayFollowed->id]);

        // One-way: oneWayFollower follows User (but User doesn't follow back)
        Follow::create(['follower_id' => $oneWayFollower->id, 'following_id' => $this->user->id]);

        $feedService = app(TimelineFeedService::class);
        $feedService->clearUserCache($this->user->id);

        $mutualPost = Post::create([
            'user_id' => $mutualUser->id,
            'unicode' => Str::random(8),
            'content' => 'Mutual friend post',
            'status' => 'LIVE',
            'is_boosted' => false,
            'created_at' => now()->subHour(),
        ]);

        $oneWayPost1 = Post::create([
            'user_id' => $oneWayFollowed->id,
            'unicode' => Str::random(8),
            'content' => 'Non-mutual post 1',
            'status' => 'LIVE',
            'is_boosted' => false,
            'created_at' => now()->subHour(),
        ]);

        $oneWayPost2 = Post::create([
            'user_id' => $oneWayFollower->id,
            'unicode' => Str::random(8),
            'content' => 'Non-mutual post 2',
            'status' => 'LIVE',
            'is_boosted' => false,
            'created_at' => now()->subHour(),
        ]);

        $result = $feedService->buildFeedForTab(
            tab: 'following',
            userId: $this->user->id,
            targetCount: 10,
            seed: 777
        );

        $postIds = $result['posts']->pluck('id')->all();

        $this->assertContains($mutualPost->id, $postIds, 'Mutual connection post must be in Following feed.');
        $this->assertNotContains($oneWayPost1->id, $postIds, 'One-way followed user post must NOT be in Following feed.');
        $this->assertNotContains($oneWayPost2->id, $postIds, 'One-way follower user post must NOT be in Following feed.');
    }

    public function test_timeline_livewire_component_switches_tabs(): void
    {
        $mutualUser = $this->createTestUser(['username' => 'tabuser_' . Str::lower(Str::random(6))]);
        Follow::create(['follower_id' => $this->user->id, 'following_id' => $mutualUser->id]);
        Follow::create(['follower_id' => $mutualUser->id, 'following_id' => $this->user->id]);

        Post::create([
            'user_id' => $mutualUser->id,
            'unicode' => Str::random(8),
            'content' => 'Tab test post',
            'status' => 'LIVE',
            'is_boosted' => false,
            'created_at' => now(),
        ]);

        $component = Livewire::actingAs($this->user)->test(\App\Livewire\User\Timeline::class);

        // Default tab is for_you
        $this->assertEquals('for_you', $component->get('activeTab'));

        // Switch to following
        $component->call('setTab', 'following');
        $this->assertEquals('following', $component->get('activeTab'));

        // Switch back to for_you
        $component->call('setTab', 'for_you');
        $this->assertEquals('for_you', $component->get('activeTab'));
    }
}
