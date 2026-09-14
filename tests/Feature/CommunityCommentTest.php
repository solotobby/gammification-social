<?php

namespace Tests\Feature;

use App\Livewire\User\CommunityDetails;
use App\Models\Community;
use App\Models\CommunityCategory;
use App\Models\User;
use App\Services\CommunityMembershipService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class CommunityCommentTest extends TestCase
{
    use DatabaseTransactions;

    protected function createUser(): User
    {
        return User::factory()->create([
            'username' => 'user_' . Str::lower(Str::random(10)),
            'referral_code' => 'REF' . Str::upper(Str::random(6)),
        ]);
    }

    public function test_comment_shows_only_once_when_posted_without_refresh(): void
    {
        $owner = $this->createUser();
        $member = $this->createUser();

        $category = CommunityCategory::first() ?? CommunityCategory::create([
            'name' => 'General',
            'slug' => 'general-' . Str::random(5),
        ]);

        $community = Community::create([
            'user_id' => $owner->id,
            'community_categories_id' => $category->id,
            'name' => 'Comment Test Community',
            'slug' => 'comm-test-' . Str::random(6),
            'description' => 'Test description',
            'type' => 'public',
        ]);

        $membership = new CommunityMembershipService();
        $membership->attachMember($community, $owner->id, 'owner');
        $membership->attachMember($community, $member->id);

        $post = $community->posts()->create([
            'user_id' => $owner->id,
            'content' => 'First post in test community',
        ]);

        $uniqueCommentText = 'UniqueComment_' . Str::random(12);

        $testable = Livewire::actingAs($member)
            ->test(CommunityDetails::class, ['community' => $community])
            ->set("newComment.{$post->id}", $uniqueCommentText)
            ->call('addComment', $post->id);

        // Assert the database row was created
        $this->assertDatabaseHas('community_post_comments', [
            'community_post_id' => $post->id,
            'user_id' => $member->id,
            'content' => $uniqueCommentText,
        ]);

        // Get the rendered HTML output
        $html = $testable->html();

        // Count how many times the unique comment text appears in the rendered HTML
        $count = substr_count($html, $uniqueCommentText);

        $this->assertEquals(
            1,
            $count,
            "Comment should appear exactly once in the rendered HTML, but appeared {$count} times."
        );
    }

    public function test_comment_deletion_updates_count_and_removes_comment(): void
    {
        $owner = $this->createUser();
        $member = $this->createUser();

        $category = CommunityCategory::first() ?? CommunityCategory::create([
            'name' => 'General',
            'slug' => 'general-' . Str::random(5),
        ]);

        $community = Community::create([
            'user_id' => $owner->id,
            'community_categories_id' => $category->id,
            'name' => 'Delete Comment Test',
            'slug' => 'del-test-' . Str::random(6),
            'description' => 'Test description',
            'type' => 'public',
        ]);

        $membership = new CommunityMembershipService();
        $membership->attachMember($community, $owner->id, 'owner');
        $membership->attachMember($community, $member->id);

        $post = $community->posts()->create([
            'user_id' => $owner->id,
            'content' => 'First post in test community',
        ]);

        $commentText = 'CommentToDelete_' . Str::random(8);

        $testable = Livewire::actingAs($member)
            ->test(CommunityDetails::class, ['community' => $community])
            ->set("newComment.{$post->id}", $commentText)
            ->call('addComment', $post->id);

        $comment = \App\Models\CommunityPostComment::where('community_post_id', $post->id)
            ->where('content', $commentText)
            ->firstOrFail();

        // Delete the comment as author
        $testable->call('deleteComment', $comment->id);

        $this->assertDatabaseMissing('community_post_comments', [
            'id' => $comment->id,
        ]);

        $html = $testable->html();
        $this->assertEquals(0, substr_count($html, $commentText));
    }
}
