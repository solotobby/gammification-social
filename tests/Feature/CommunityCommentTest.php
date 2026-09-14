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

    public function test_member_can_reply_to_a_comment(): void
    {
        $owner = $this->createUser();
        $member1 = $this->createUser();
        $member2 = $this->createUser();

        $category = CommunityCategory::first() ?? CommunityCategory::create([
            'name' => 'General',
            'slug' => 'general-' . Str::random(5),
        ]);

        $community = Community::create([
            'user_id' => $owner->id,
            'community_categories_id' => $category->id,
            'name' => 'Reply Test Community',
            'slug' => 'reply-test-' . Str::random(6),
            'description' => 'Test description',
            'type' => 'public',
        ]);

        $membership = new CommunityMembershipService();
        $membership->attachMember($community, $owner->id, 'owner');
        $membership->attachMember($community, $member1->id);
        $membership->attachMember($community, $member2->id);

        $post = $community->posts()->create([
            'user_id' => $owner->id,
            'content' => 'Post with replies',
        ]);

        // Member1 creates a top-level comment
        $comment = \App\Models\CommunityPostComment::create([
            'community_post_id' => $post->id,
            'user_id' => $member1->id,
            'content' => 'Top level comment',
        ]);
        $post->increment('comments_count');

        $replyText = 'ReplyMessage_' . Str::random(10);

        // Member2 replies to Member1's comment via Livewire
        $testable = Livewire::actingAs($member2)
            ->test(CommunityDetails::class, ['community' => $community])
            ->set("replyText.{$comment->id}", $replyText)
            ->call('addReply', $post->id, $comment->id);

        // Assert reply exists in database with parent_id
        $this->assertDatabaseHas('community_post_comments', [
            'community_post_id' => $post->id,
            'parent_id' => $comment->id,
            'user_id' => $member2->id,
            'content' => $replyText,
        ]);

        // Assert relationships
        $this->assertEquals(1, $comment->replies()->count());
        $createdReply = $comment->replies()->first();
        $this->assertEquals($comment->id, $createdReply->parent->id);
        $this->assertTrue($createdReply->isReply());

        // Assert total post comments_count is 2 (1 comment + 1 reply)
        $this->assertEquals(2, $post->fresh()->comments_count);

        // Assert reply renders in HTML
        $html = $testable->html();
        $this->assertEquals(1, substr_count($html, $replyText));
    }

    public function test_nested_reply_to_reply_is_attached_to_root_parent(): void
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
            'name' => 'Nested Reply Community',
            'slug' => 'nested-' . Str::random(6),
            'description' => 'Test description',
            'type' => 'public',
        ]);

        $membership = new CommunityMembershipService();
        $membership->attachMember($community, $owner->id, 'owner');
        $membership->attachMember($community, $member->id);

        $post = $community->posts()->create([
            'user_id' => $owner->id,
            'content' => 'Post testing nested replies',
        ]);

        $rootComment = \App\Models\CommunityPostComment::create([
            'community_post_id' => $post->id,
            'user_id' => $member->id,
            'content' => 'Root comment',
        ]);

        $firstReply = \App\Models\CommunityPostComment::create([
            'community_post_id' => $post->id,
            'user_id' => $member->id,
            'content' => 'First reply',
            'parent_id' => $rootComment->id,
        ]);

        // Replying to $firstReply (which is already a reply)
        $engagementService = app(\App\Services\CommunityPostEngagementService::class);
        $nestedReply = $engagementService->addComment($post, $member, 'Second reply replying to first reply', $firstReply->id);

        // Should attach to root comment, keeping a clean 1-level hierarchy
        $this->assertEquals($rootComment->id, $nestedReply->parent_id);
    }

    public function test_deleting_parent_comment_cascades_and_updates_post_comments_count(): void
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
            'name' => 'Cascade Delete Community',
            'slug' => 'cascade-' . Str::random(6),
            'description' => 'Test description',
            'type' => 'public',
        ]);

        $membership = new CommunityMembershipService();
        $membership->attachMember($community, $owner->id, 'owner');
        $membership->attachMember($community, $member->id);

        $post = $community->posts()->create([
            'user_id' => $owner->id,
            'content' => 'Post with comment and replies to delete',
        ]);

        $rootComment = \App\Models\CommunityPostComment::create([
            'community_post_id' => $post->id,
            'user_id' => $member->id,
            'content' => 'Root to delete',
        ]);

        \App\Models\CommunityPostComment::create([
            'community_post_id' => $post->id,
            'user_id' => $member->id,
            'content' => 'Reply 1',
            'parent_id' => $rootComment->id,
        ]);

        \App\Models\CommunityPostComment::create([
            'community_post_id' => $post->id,
            'user_id' => $member->id,
            'content' => 'Reply 2',
            'parent_id' => $rootComment->id,
        ]);

        // Total 3 comments on post
        $post->forceFill(['comments_count' => 3])->save();

        // Member deletes root comment via Livewire
        Livewire::actingAs($member)
            ->test(CommunityDetails::class, ['community' => $community])
            ->call('deleteComment', $rootComment->id);

        // Root comment and replies should all be deleted
        $this->assertDatabaseMissing('community_post_comments', ['id' => $rootComment->id]);
        $this->assertEquals(0, \App\Models\CommunityPostComment::where('community_post_id', $post->id)->count());

        // Post comments_count should be decremented by 3 to 0
        $this->assertEquals(0, $post->fresh()->comments_count);
    }
}
