<?php

namespace Tests\Feature;

use App\Livewire\User\PostContent;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class TimelineCommentReplyTest extends TestCase
{
    use DatabaseTransactions;

    protected function createUser(): User
    {
        return User::factory()->create([
            'username' => 'user_' . Str::lower(Str::random(10)),
            'referral_code' => 'REF' . Str::upper(Str::random(6)),
        ]);
    }

    public function test_user_can_submit_top_level_comment_on_post(): void
    {
        $postOwner = $this->createUser();
        $commenter = $this->createUser();

        $post = Post::create([
            'user_id' => $postOwner->id,
            'content' => 'First post on the feed!',
            'status' => 'LIVE',
            'unicode' => 'P-'.uniqid(),
        ]);

        Livewire::actingAs($commenter)
            ->test(PostContent::class, ['post' => $post])
            ->set('commentMessage', 'Great feed post!')
            ->call('submitComment');

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $commenter->id,
            'message' => 'Great feed post!',
            'parent_id' => null,
        ]);
    }

    public function test_user_can_reply_to_a_comment_on_post(): void
    {
        $postOwner = $this->createUser();
        $commenter = $this->createUser();
        $replier = $this->createUser();

        $post = Post::create([
            'user_id' => $postOwner->id,
            'content' => 'Feed post to reply on',
            'status' => 'LIVE',
            'unicode' => 'P-'.uniqid(),
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => $commenter->id,
            'message' => 'Top level comment',
        ]);

        Livewire::actingAs($replier)
            ->test(PostContent::class, ['post' => $post])
            ->set('replyMessage.'.$comment->id, 'This is a reply to your comment!')
            ->call('submitReply', $comment->id)
            ->assertSee('This is a reply to your comment!');

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $replier->id,
            'parent_id' => $comment->id,
            'message' => 'This is a reply to your comment!',
        ]);
    }

    public function test_nested_reply_to_reply_is_attached_to_root_parent(): void
    {
        $postOwner = $this->createUser();
        $user1 = $this->createUser();
        $user2 = $this->createUser();
        $user3 = $this->createUser();

        $post = Post::create([
            'user_id' => $postOwner->id,
            'content' => 'Feed post for nested test',
            'status' => 'LIVE',
            'unicode' => 'P-'.uniqid(),
        ]);

        // Root comment
        $root = Comment::create([
            'post_id' => $post->id,
            'user_id' => $user1->id,
            'message' => 'Root comment',
        ]);

        // First reply attached to root
        $reply1 = Comment::create([
            'post_id' => $post->id,
            'user_id' => $user2->id,
            'parent_id' => $root->id,
            'message' => 'Reply 1',
        ]);

        // User3 replies to Reply 1
        Livewire::actingAs($user3)
            ->test(PostContent::class, ['post' => $post])
            ->set('replyMessage.'.$reply1->id, 'Reply to reply 1')
            ->call('submitReply', $reply1->id);

        // Must be attached to root, NOT reply1
        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $user3->id,
            'parent_id' => $root->id,
            'message' => 'Reply to reply 1',
        ]);
    }

    public function test_parent_comment_author_receives_notification_when_replied_to(): void
    {
        Notification::fake();

        $postOwner = $this->createUser();
        $commentAuthor = $this->createUser();
        $replier = $this->createUser();

        $post = Post::create([
            'user_id' => $postOwner->id,
            'content' => 'Post with notification check',
            'status' => 'LIVE',
            'unicode' => 'P-'.uniqid(),
        ]);

        $rootComment = Comment::create([
            'post_id' => $post->id,
            'user_id' => $commentAuthor->id,
            'message' => 'My insightful comment',
        ]);

        Livewire::actingAs($replier)
            ->test(PostContent::class, ['post' => $post])
            ->set('replyMessage.'.$rootComment->id, 'I agree with you!')
            ->call('submitReply', $rootComment->id);

        Notification::assertSentTo(
            $commentAuthor,
            GeneralNotification::class,
            function ($notification) {
                $data = $notification->data;
                return str_contains($data['title'], 'replied to your comment');
            }
        );
    }

    public function test_deleting_parent_comment_cascades_and_removes_replies(): void
    {
        $postOwner = $this->createUser();
        $commentAuthor = $this->createUser();
        $replier = $this->createUser();

        $post = Post::create([
            'user_id' => $postOwner->id,
            'content' => 'Post for cascade deletion test',
            'status' => 'LIVE',
            'unicode' => 'P-'.uniqid(),
        ]);

        $root = Comment::create([
            'post_id' => $post->id,
            'user_id' => $commentAuthor->id,
            'message' => 'Root to delete',
        ]);

        $reply = Comment::create([
            'post_id' => $post->id,
            'user_id' => $replier->id,
            'parent_id' => $root->id,
            'message' => 'Reply that should cascade',
        ]);

        $post->forceFill(['comments' => 2])->save();

        Livewire::actingAs($commentAuthor)
            ->test(PostContent::class, ['post' => $post])
            ->call('deleteComment', $root->id);

        // Root and reply should both be deleted from DB
        $this->assertDatabaseMissing('comments', ['id' => $root->id]);
        $this->assertDatabaseMissing('comments', ['id' => $reply->id]);
        $this->assertEquals(0, Comment::where('post_id', $post->id)->count());

        // Counter decremented
        $this->assertEquals(0, $post->fresh()->comments);
    }
}
