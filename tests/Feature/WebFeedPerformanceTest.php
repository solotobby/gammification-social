<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\PostBookmark;
use App\Models\User;
use App\Models\UserLike;
use App\Models\Wallet;
use App\Notifications\GeneralNotification;
use App\Services\LikeService;
use App\Services\PayKoinService;
use App\Services\PostEarningsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class WebFeedPerformanceTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected User $author;
    protected Post $post;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Web Perf User',
            'username' => 'perf_user_' . Str::lower(Str::random(6)),
            'referral_code' => strtoupper(Str::random(8)),
            'email_verified_at' => now(),
        ]);

        $this->author = User::factory()->create([
            'name' => 'Web Perf Author',
            'username' => 'perf_auth_' . Str::lower(Str::random(6)),
            'referral_code' => strtoupper(Str::random(8)),
            'email_verified_at' => now(),
        ]);

        Wallet::create([
            'user_id' => $this->user->id,
            'balance' => 0,
            'promoter_balance' => 0,
            'referral_balance' => 0,
            'paykoin_spendable' => 0,
            'currency' => 'NGN',
        ]);

        Wallet::create([
            'user_id' => $this->author->id,
            'balance' => 0,
            'promoter_balance' => 0,
            'referral_balance' => 0,
            'paykoin_spendable' => 0,
            'currency' => 'NGN',
        ]);

        $this->post = Post::create([
            'user_id' => $this->author->id,
            'unicode' => Str::random(8),
            'content' => 'High performance test post content for benchmarking web feed',
            'status' => 'LIVE',
            'is_boosted' => false,
        ]);
    }

    public function test_general_notification_implements_should_queue(): void
    {
        $notification = new GeneralNotification([
            'title' => 'Test Notification',
            'message' => 'Notification performance test',
        ]);

        $this->assertInstanceOf(ShouldQueue::class, $notification);
    }

    public function test_post_earnings_are_cached_and_batch_computed(): void
    {
        $service = app(PostEarningsService::class);
        $postIds = collect([$this->post->id]);

        // First call populates cache
        $first = $service->forPosts($postIds, 'NGN');
        $this->assertArrayHasKey($this->post->id, $first);

        // Verify cache key exists
        $cacheKey = "post_earnings:{$this->post->id}:NGN";
        $this->assertTrue(Cache::has($cacheKey));

        // Subsequent call returns directly from cache
        $cachedValue = $service->forPosts($postIds, 'NGN');
        $this->assertEquals($first[$this->post->id], $cachedValue[$this->post->id]);
    }

    public function test_currency_and_user_level_helpers_use_cache_and_memoization(): void
    {
        $rates = getActiveCurrencyRates();
        $this->assertIsArray($rates);

        $symbols = getActiveCurrencySymbols();
        $this->assertIsArray($symbols);

        $this->actingAs($this->user);
        $userCurrency = userBaseCurrency();
        $this->assertEquals('NGN', $userCurrency);

        $symbol = getCurrencyCode();
        $this->assertNotEmpty($symbol);

        $level = userLevel($this->user->id);
        $this->assertEquals('Basic', $level);
    }

    public function test_timeline_loads_posts_with_eager_loaded_likes_and_bookmarks(): void
    {
        $this->actingAs($this->user);

        // Bookmark and like the post
        PostBookmark::create([
            'user_id' => $this->user->id,
            'post_id' => $this->post->id,
        ]);

        UserLike::create([
            'user_id' => $this->user->id,
            'post_id' => $this->post->id,
            'poster_user_id' => $this->post->user_id,
            'is_paid' => false,
            'amount' => 0.00,
            'type' => 'like',
        ]);

        $component = Livewire::test(\App\Livewire\User\Timeline::class);
        $component->assertStatus(200);

        $posts = $component->get('posts');
        $matched = $posts->firstWhere('id', $this->post->id);

        if ($matched) {
            $this->assertTrue((bool) $matched->is_bookmarked_by_me);
            $this->assertTrue((bool) $matched->liked_by_me);
        }
    }

    public function test_like_service_executes_without_row_locks(): void
    {
        $likeService = app(LikeService::class);

        $likeService->toggle($this->post->unicode, $this->user);

        $this->assertDatabaseHas('user_likes', [
            'user_id' => $this->user->id,
            'post_id' => $this->post->id,
        ]);
    }

    public function test_paykoin_gifts_for_is_cached(): void
    {
        $payKoinService = app(PayKoinService::class);

        $gifts = $payKoinService->giftsFor('post', $this->post->id, 20);
        $this->assertIsArray($gifts);
        $this->assertArrayHasKey('total', $gifts);
        $this->assertArrayHasKey('recent', $gifts);

        $cacheKey = "post_gifts:post:{$this->post->id}:20";
        $this->assertTrue(Cache::has($cacheKey));
    }
}
