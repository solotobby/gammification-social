<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use App\Models\UserComment;
use App\Models\UserLike;
use App\Models\UserView;
use App\Models\Wallet;
use App\Services\CommentService;
use App\Services\LikeService;
use App\Services\PostQualityService;
use App\Services\ViewService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class PostQualityMonetizationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $creator;
    protected User $viewer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->creator = User::factory()->create([
            'username' => 'creator_' . Str::lower(Str::random(6)),
            'referral_code' => strtoupper(Str::random(8)),
            'email_verified_at' => now(),
            'status' => 'ACTIVE',
        ]);

        Wallet::create([
            'user_id' => $this->creator->id,
            'balance' => 0,
            'promoter_balance' => 0,
            'referral_balance' => 0,
            'currency' => 'NGN',
            'paykoin_spendable' => 0,
            'paykoin_earned' => 0,
        ]);

        $this->viewer = User::factory()->create([
            'username' => 'viewer_' . Str::lower(Str::random(6)),
            'referral_code' => strtoupper(Str::random(8)),
            'email_verified_at' => now(),
            'status' => 'ACTIVE',
        ]);

        Wallet::create([
            'user_id' => $this->viewer->id,
            'balance' => 0,
            'promoter_balance' => 0,
            'referral_balance' => 0,
            'currency' => 'NGN',
            'paykoin_spendable' => 0,
            'paykoin_earned' => 0,
        ]);
        $this->actingAs($this->creator);
    }

    public function test_short_and_two_letter_word_posts_are_published_live_but_unmonetized(): void
    {
        $post = Post::create([
            'user_id' => $this->creator->id,
            'unicode' => Str::random(8),
            'content' => 'hi',
            'status' => 'LIVE',
        ]);

        $this->assertEquals('LIVE', $post->status);
        $this->assertFalse((bool) $post->is_monetized);
        $this->assertFalse($post->canMonetize());
        $this->assertNotNull($post->monetization_note);
        $this->assertStringContainsString('too short', $post->monetization_note);
    }

    public function test_two_letter_dominant_phrases_are_published_live_but_unmonetized(): void
    {
        // 8 words, 7 of which are 2-letter words (87.5% short words)
        $post = Post::create([
            'user_id' => $this->creator->id,
            'unicode' => Str::random(8),
            'content' => 'is it me to go at ya on it',
            'status' => 'LIVE',
        ]);

        $this->assertEquals('LIVE', $post->status);
        $this->assertFalse((bool) $post->is_monetized);
        $this->assertFalse($post->canMonetize());
        $this->assertNotNull($post->monetization_note);
    }

    public function test_gibberish_and_consonant_mashes_are_unmonetized(): void
    {
        $post = Post::create([
            'user_id' => $this->creator->id,
            'unicode' => Str::random(8),
            'content' => 'asdfghjkl testing something bcdfgh project today',
            'status' => 'LIVE',
        ]);

        $this->assertEquals('LIVE', $post->status);
        $this->assertFalse((bool) $post->is_monetized);
        $this->assertFalse($post->canMonetize());
        $this->assertStringContainsString('unreadable character sequences', $post->monetization_note);
    }

    public function test_character_repetition_spam_is_unmonetized(): void
    {
        $post = Post::create([
            'user_id' => $this->creator->id,
            'unicode' => Str::random(8),
            'content' => 'helloooooooo everyone look at this',
            'status' => 'LIVE',
        ]);

        $this->assertEquals('LIVE', $post->status);
        $this->assertFalse((bool) $post->is_monetized);
        $this->assertFalse($post->canMonetize());
        $this->assertStringContainsString('repeated characters', $post->monetization_note);
    }

    public function test_repetitive_words_spam_is_unmonetized(): void
    {
        $post = Post::create([
            'user_id' => $this->creator->id,
            'unicode' => Str::random(8),
            'content' => 'awesome awesome awesome awesome awesome awesome',
            'status' => 'LIVE',
        ]);

        $this->assertEquals('LIVE', $post->status);
        $this->assertFalse((bool) $post->is_monetized);
        $this->assertFalse($post->canMonetize());
        $this->assertStringContainsString('repetition', $post->monetization_note);
    }

    public function test_random_word_soup_without_verbs_is_unmonetized(): void
    {
        $post = Post::create([
            'user_id' => $this->creator->id,
            'unicode' => Str::random(8),
            'content' => 'banana apple sky river chair table laptop road',
            'status' => 'LIVE',
        ]);

        $this->assertEquals('LIVE', $post->status);
        $this->assertFalse((bool) $post->is_monetized);
        $this->assertFalse($post->canMonetize());
        $this->assertStringContainsString('missing an action or verb predicate', $post->monetization_note);
    }

    public function test_meaningless_conjunction_and_preposition_chains_are_unmonetized(): void
    {
        $post = Post::create([
            'user_id' => $this->creator->id,
            'unicode' => Str::random(8),
            'content' => 'because of the and with for or but so then',
            'status' => 'LIVE',
        ]);

        $this->assertEquals('LIVE', $post->status);
        $this->assertFalse((bool) $post->is_monetized);
        $this->assertFalse($post->canMonetize());
        $this->assertStringContainsString('fragmented conjunctions or prepositions', $post->monetization_note);
    }

    public function test_superficial_greeting_without_substance_is_unmonetized(): void
    {
        $post = Post::create([
            'user_id' => $this->creator->id,
            'unicode' => Str::random(8),
            'content' => 'Good morning to all my followers',
            'status' => 'LIVE',
        ]);

        $this->assertEquals('LIVE', $post->status);
        $this->assertFalse((bool) $post->is_monetized);
        $this->assertFalse($post->canMonetize());
        $this->assertStringContainsString('greeting or salutation', $post->monetization_note);
    }

    public function test_greeting_with_valuable_content_is_monetized(): void
    {
        $post = Post::create([
            'user_id' => $this->creator->id,
            'unicode' => Str::random(8),
            'content' => 'Good morning to all my followers, here is an in-depth summary of the new economy report.',
            'status' => 'LIVE',
        ]);

        $this->assertEquals('LIVE', $post->status);
        $this->assertTrue((bool) $post->is_monetized);
        $this->assertTrue($post->canMonetize());
        $this->assertNull($post->monetization_note);
    }

    public function test_all_caps_shouting_is_unmonetized(): void
    {
        $post = Post::create([
            'user_id' => $this->creator->id,
            'unicode' => Str::random(8),
            'content' => 'BUY THIS COIN RIGHT NOW TODAY DO NOT MISS OUT',
            'status' => 'LIVE',
        ]);

        $this->assertEquals('LIVE', $post->status);
        $this->assertFalse((bool) $post->is_monetized);
        $this->assertFalse($post->canMonetize());
        $this->assertStringContainsString('uppercase shouting', $post->monetization_note);
    }

    public function test_excessive_punctuation_spam_is_unmonetized(): void
    {
        $post = Post::create([
            'user_id' => $this->creator->id,
            'unicode' => Str::random(8),
            'content' => 'Check this out right now everyone!!!!',
            'status' => 'LIVE',
        ]);

        $this->assertEquals('LIVE', $post->status);
        $this->assertFalse((bool) $post->is_monetized);
        $this->assertFalse($post->canMonetize());
        $this->assertStringContainsString('excessive punctuation spam', $post->monetization_note);
    }

    public function test_repeated_phrase_spam_is_unmonetized(): void
    {
        $post = Post::create([
            'user_id' => $this->creator->id,
            'unicode' => Str::random(8),
            'content' => 'great job on this amazing project, click here now to join us, click here now to see why, click here now right away',
            'status' => 'LIVE',
        ]);

        $this->assertEquals('LIVE', $post->status);
        $this->assertFalse((bool) $post->is_monetized);
        $this->assertFalse($post->canMonetize());
        $this->assertStringContainsString('repetitive phrase spam', $post->monetization_note);
    }

    public function test_quality_post_is_monetized(): void
    {
        $post = Post::create([
            'user_id' => $this->creator->id,
            'unicode' => Str::random(8),
            'content' => 'Excited to announce our new product update launch today with multiple new features!',
            'status' => 'LIVE',
        ]);

        $this->assertEquals('LIVE', $post->status);
        $this->assertTrue((bool) $post->is_monetized);
        $this->assertTrue($post->canMonetize());
        $this->assertNull($post->monetization_note);
    }

    public function test_views_on_unmonetized_post_do_not_generate_earnings(): void
    {
        $post = Post::create([
            'user_id' => $this->creator->id,
            'unicode' => Str::random(8),
            'content' => 'ok ok ok',
            'status' => 'LIVE',
        ]);

        $this->assertFalse($post->canMonetize());

        $viewService = app(ViewService::class);
        $viewService->recordView($post, $this->viewer->id);

        $view = UserView::where('post_id', $post->id)->where('user_id', $this->viewer->id)->first();
        $this->assertNotNull($view);
        $this->assertEquals(0.00, (float) $view->amount);
        $this->assertEquals('unmonetized', $view->type);

        $this->assertEquals(0.00, (float) viewsAmountCalculator($post->id));
    }

    public function test_likes_on_unmonetized_post_do_not_generate_earnings(): void
    {
        $post = Post::create([
            'user_id' => $this->creator->id,
            'unicode' => Str::random(8),
            'content' => 'hi there',
            'status' => 'LIVE',
        ]);

        $this->assertFalse($post->canMonetize());

        $likeService = app(LikeService::class);
        $likeService->toggle($post->unicode, $this->viewer);

        $like = UserLike::where('post_id', $post->id)->where('user_id', $this->viewer->id)->first();
        $this->assertNotNull($like);
        $this->assertEquals(0.00, (float) $like->amount);
        $this->assertEquals('unmonetized', $like->type);

        $this->assertEquals(0.00, (float) likesAmountCalculator($post->id));
    }

    public function test_comments_on_unmonetized_post_do_not_generate_earnings(): void
    {
        $post = Post::create([
            'user_id' => $this->creator->id,
            'unicode' => Str::random(8),
            'content' => 'yo bro',
            'status' => 'LIVE',
        ]);

        $this->assertFalse($post->canMonetize());

        $commentService = app(CommentService::class);
        $commentService->addComment($post->id, $this->viewer, 'This is a comment on unmonetized post');

        $userComment = UserComment::where('post_id', $post->id)->where('user_id', $this->viewer->id)->first();
        $this->assertNotNull($userComment);
        $this->assertEquals(0.00, (float) $userComment->amount);
        $this->assertEquals('unmonetized', $userComment->type);

        $this->assertEquals(0.00, (float) commentsAmountCalculator($post->id));
    }

    public function test_editing_post_from_low_quality_to_high_quality_restores_monetization(): void
    {
        $post = Post::create([
            'user_id' => $this->creator->id,
            'unicode' => Str::random(8),
            'content' => 'ok',
            'status' => 'LIVE',
        ]);

        $this->assertFalse((bool) $post->is_monetized);

        // Edit to high quality content
        $post->content = 'Here is a comprehensive breakdown of today\'s industry news and tech developments.';
        $post->save();

        $post->refresh();
        $this->assertTrue((bool) $post->is_monetized);
        $this->assertTrue($post->canMonetize());
        $this->assertNull($post->monetization_note);
    }

    public function test_editing_post_from_high_quality_to_low_quality_disables_monetization(): void
    {
        $post = Post::create([
            'user_id' => $this->creator->id,
            'unicode' => Str::random(8),
            'content' => 'Here is a comprehensive breakdown of today\'s industry news and tech developments.',
            'status' => 'LIVE',
        ]);

        $this->assertTrue((bool) $post->is_monetized);

        // Edit to low quality content
        $post->content = 'hi';
        $post->save();

        $post->refresh();
        $this->assertFalse((bool) $post->is_monetized);
        $this->assertFalse($post->canMonetize());
        $this->assertNotNull($post->monetization_note);
    }

    public function test_timeline_renders_monetization_quality_notice(): void
    {
        \Livewire\Livewire::actingAs($this->creator)
            ->test(\App\Livewire\User\Timeline::class)
            ->assertSee('Monetization Quality Standards')
            ->assertSee('What makes a post eligible?')
            ->assertSee('minimum 5 words and 25 characters');
    }
}

