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

    public function mount($username, )
    {

        $this->timeline($username);
        $this->isFollowing = Auth::user()?->isFollowing($this->user ) ?? false;
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

        $authUser = Auth::user();

        if ($this->isFollowing) {
            Follow::where([
                'follower_id' => $authUser->id,
                'following_id' => $this->user->id,
            ])->delete();
            // $authUser->following()->detach($this->user->id); //unfollow
            $this->isFollowing = false;
        } else {
            Follow::create([
                'follower_id' => $authUser->id,
                'following_id' => $this->user->id,
            ]);
            // $authUser->following()->attach($this->user->id); //follow
            $this->isFollowing = true;
        }

        
        // $userToFollow = User::where('id', $userId)->first();

        // if (auth()->user()->isFollowing($userToFollow)) {
        //     auth()->user()->unfollow($userToFollow);
        // } else {
        //     auth()->user()->follow($userToFollow);
        // }

        // $this->timeline($this->username);

    }



    public function like($id)
    {
        // dd(auth()->user()->WithTotalLikes($id));

        $post =  Post::where('unicode', $id)->first();
        $post->likes += 1;
        $post->save();

        UserLike::create(['user_id' => auth()->user()->id, 'post_id' => $post->id]);
        $this->dispatch('user.view-profile', id: $post->id);
    }

    public function dislike($id)
    {

        $post =  Post::where('unicode', $id)->first();
        $post->likes -= 1;
        $post->save();
        UserLike::where(['user_id' => auth()->user()->id, 'post_id' => $post->id])->delete();
        $this->dispatch('user.view-profile', id: $post->id);
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
