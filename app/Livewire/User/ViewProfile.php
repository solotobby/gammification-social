<?php

namespace App\Livewire\User;

use App\Models\Follow;
use App\Models\Post;
use App\Models\ProfileViews;
use App\Models\User;
use App\Models\UserLike;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

class ViewProfile extends Component
{

    public $username, $timelines, $highestEngagement;

    public User $user;

    public $perpage = 10;

    public bool $isFollowing = false;

    #[On('view-profile.{user.username}')]

    // #[On('refreshFeed')]

    public function mount($username,)
    {

        $this->timeline($username);
        $this->isFollowing = Auth::user()?->isFollowing($this->user) ?? false;
        $this->recordProfileViews();
    }

    private function recordProfileViews(): void
    {

        $viewerId = auth()->id();
        $userId = $this->user->id;

        if ($viewerId === $userId) {
            return;
        }

        $created = ProfileViews::firstOrCreate(
            ['user_id' => $userId, 'viewer_id' => $viewerId]
        );

        $this->user->increment(
            $created->wasRecentlyCreated
                ? 'profile_views'
                : 'profile_views_external'
        );

        if ($created->wasRecentlyCreated) {
            $this->user->notify(new GeneralNotification([
                'title'   => displayName(auth()->user()->name) . ' viewed your profile',
                'message' => displayName(auth()->user()->name) . ' viewed your profile',
                'icon'    => 'fa-eye text-primary',
                'url'     => url('profile/' . auth()->user()->username),
            ]));
        }
    }

    public function timeline($username)
    {
        $this->username = $username;
        // dd($this->username);
        $this->user = User::withPostStatsByUsername($this->username)->firstOrFail();
        $this->timelines = $this->user->posts()->where(['status' => 'LIVE', 'user_id' =>  $this->user->id])->orderBy('created_at', 'desc')->take($this->perpage)->get(); //$this->timelines();

    }

    public function toggleLike($postId)
    {
        $user = Auth::user();

        // Fetch post with owner user eager-loaded
        $post = Post::with('user')
            ->where('unicode', $postId)
            ->firstOrFail(); // fail early if post not found

        DB::transaction(function () use ($post, $user) {
            // Check if already liked
            $like = $post->likes()->where('user_id', $user->id)->first();

            if ($like) {
                // Unlike: delete like & decrement counter atomically
                $like->delete();
                $post->decrement('likes');
            } else {
                // Like: create record & increment counter atomically
                $post->likes()->create([
                    'user_id'        => $user->id,
                    'is_paid'        => false,
                    'amount'         => calculateUniqueEarningPerLike(),
                    'poster_user_id' => $post->user_id,
                ]);
                $post->increment('likes');

                // Queue notification for async processing
                $post->user->notify((new GeneralNotification([
                    'title'   => displayName($user->name) . ' liked your post',
                    'message' => displayName($user->name) . ' liked your post',
                    'icon'    => 'fa-thumbs-up text-primary',
                    'url'     => url('show/' . $post->id),
                ]))->delay(now()->addSeconds(1)));
            }
        });

        // Refresh the timeline efficiently
        $this->timeline($this->username);
    }


    // public function toggleLike($postId)
    // {

    //     $post = Post::where('unicode', $postId)->first();
    //     $user = User::find($post->user_id);

    //     if ($post->isLikedBy(Auth::user())) {
    //         $post->likes()->where('user_id', Auth::id())->delete();
    //         $post->decrement('likes');
    //     } else {
    //         // if (auth()->user()->id != $post->user_id) {
    //         $post->likes()->create(['user_id' => Auth::id(), 'is_paid' => false, 'amount' => calculateUniqueEarningPerLike(), 'poster_user_id' => $post->user_id]);
    //         $post->increment('likes');

    //         $user->notify(new GeneralNotification([
    //             'title'   =>  displayName(auth()->user()->name) . ' liked your post',
    //             'message' => displayName(auth()->user()->name) . 'liked your post',
    //             'icon'    => 'fa-thumbs-up text-primary',
    //             'url'     => url('show/' . $post->id),
    //         ]));
    //         // }
    //     }

    //     $this->timeline($this->username);

    //     // $this->dispatch('user.timeline');
    // }

    public function toggleFollow()
    {

        if (! Auth::check() || Auth::id() === $this->user->id) {
            return;
        }

        $authUser = Auth::user(); // the logged-in user
        $targetUser = $this->user; // the user being followed/unfollowed


        if ($this->isFollowing) {
            Follow::where([
                'follower_id' => $authUser->id,
                'following_id' => $this->user->id,
            ])->delete();

            // Prevent negative counts
            if ($authUser->following > 0) {
                $authUser->decrement('following');
            }

            if ($targetUser->followers > 0) {
                $targetUser->decrement('followers');
            }


            // $authUser->following()->detach($this->user->id); //unfollow
            $this->isFollowing = false;

            $this->user->notify(new GeneralNotification([
                'title'   =>  displayName(auth()->user()->name) . ' unfollowed you',
                'message' => displayName(auth()->user()->name) . ' unfollowed you',
                'icon'    => 'fa-user-minus text-primary',
                'url'     => url('profile/' . auth()->user()->username),
            ]));
        } else {


            Follow::create([
                'follower_id' => $authUser->id, //update user following
                'following_id' => $this->user->id,
            ]);
            //increase 'following' by 1

            // Increment counts
            $authUser->increment('following');
            $targetUser->increment('followers');

            // $authUser->following()->attach($this->user->id); //follow
            $this->isFollowing = true;

            $this->user->notify(new GeneralNotification([
                'title'   =>  displayName(auth()->user()->name) . ' followed you',
                'message' => displayName(auth()->user()->name) . ' followed you',
                'icon'    => 'fa-user-plus text-primary',
                'url'     => url('profile/' . auth()->user()->username),
            ]));
        }


        $this->clearUserFeedCache($authUser->id);
        $this->clearUserFeedCache($targetUser->id);

        $this->dispatch('refreshFeed');
    }

    public function loadMore()
    {
        $this->perpage += 10;
    }

    private function rememberUserCacheKey(string $userId, string $key): void
    {
        $indexKey = "feed:keys:user:{$userId}";

        $keys = Cache::get($indexKey, []);

        if (!in_array($key, $keys)) {
            $keys[] = $key;
            Cache::put($indexKey, $keys, now()->addMinutes(10));
        }
    }



    private function clearUserFeedCache(string $userId): void
    {
        $indexKey = "feed:keys:user:{$userId}";
        $keys = Cache::get($indexKey, []);

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Cache::forget($indexKey);
    }



    public function render()
    {
        // $timelines = $this->timeline($this->id);

        $timelines = $this->user->posts()->where(['status' => 'LIVE', 'user_id' =>  $this->user->id])->orderBy('created_at', 'desc')->take($this->perpage)->get(); //$this->timelines();

        return view('livewire.user.view-profile', ['posts' => $timelines]);
    }
}
