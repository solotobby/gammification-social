<?php

namespace App\Livewire\User;

use App\Models\Follow;
use App\Models\Post;
use App\Models\User;
use App\Models\UserLike;
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


        if ($post->isLikedBy(Auth::user())) {
            $post->likes()->where('user_id', Auth::id())->delete();
            $post->decrement('likes');
        } else {
            // if (auth()->user()->id != $post->user_id) {
            $post->likes()->create(['user_id' => Auth::id(), 'is_paid' => false, 'amount' => calculateUniqueEarningPerLike(), 'poster_user_id' => $post->user_id]);
            $post->increment('likes');
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
