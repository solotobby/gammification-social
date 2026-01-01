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
use Livewire\Attributes\On;

class ViewProfile extends Component
{

    public $username, $timelines, $highestEngagement;

    public User $user;

    public $perpage = 10;

    public bool $isFollowing = false;

    #[On('view-profile.{user.username}')]

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

        $post = Post::where('unicode', $postId)->first();
        $user = User::find($post->user_id);

        if ($post->isLikedBy(Auth::user())) {
            $post->likes()->where('user_id', Auth::id())->delete();
            $post->decrement('likes');
        } else {
            // if (auth()->user()->id != $post->user_id) {
            $post->likes()->create(['user_id' => Auth::id(), 'is_paid' => false, 'amount' => calculateUniqueEarningPerLike(), 'poster_user_id' => $post->user_id]);
            $post->increment('likes');

            $user->notify(new GeneralNotification([
                'title'   =>  displayName(auth()->user()->name).' liked your post',
                'message' => displayName(auth()->user()->name).'liked your post',
                'icon'    => 'fa-thumbs-up text-primary',
                'url'     => url('show/'.$post->id),
            ]));
            // }
        }

        $this->timeline($this->username);

        // $this->dispatch('user.timeline');
    }

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
                'title'   =>  displayName(auth()->user()->name).' unfollowed you',
                'message' => displayName(auth()->user()->name).' unfollowed you',
                'icon'    => 'fa-user-minus text-primary',
                'url'     => url('profile/'.auth()->user()->username),
            ]));


        } else {
            //following $this->user->id user

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
                'title'   =>  displayName(auth()->user()->name).' followed you',
                'message' => displayName(auth()->user()->name).' followed you',
                'icon'    => 'fa-user-plus text-primary',
                'url'     => url('profile/'.auth()->user()->username),
            ]));

        }
    }

    public function loadMore()
    {
        $this->perpage += 10;
    }

    public function render()
    {
        // $timelines = $this->timeline($this->id);

        $timelines = $this->user->posts()->where(['status' => 'LIVE', 'user_id' =>  $this->user->id])->orderBy('created_at', 'desc')->take($this->perpage)->get(); //$this->timelines();

        return view('livewire.user.view-profile', ['posts' => $timelines]);
    }
}
