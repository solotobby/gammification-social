<?php

namespace App\Livewire\User;

use App\Models\Post;
use App\Services\LikeService;
use Livewire\Component;

class PostContent extends Component
{


    public Post $post;
    public $likesCount;
    public $likedByMe;

    public $showPlayer = false;
    public $activeVideoId = null;

    public int $commentCount = 0;

    protected $listeners = [
        'commentAdded' => 'incrementCommentCount',
    ];


    public function mount(Post $post)
    {

        $this->post = $post;



        $this->likedByMe = $post->isLikedBy(auth()->user());
        $this->likesCount = sumCounter(
            $post->likes,
            $post->likes_external
        );

        $this->commentCount = $post->comments + $post->comment_external;

        // dd($this->commentCount);

    }

    public function incrementCommentCount()
    {
        $this->commentCount++;
    }

    public function toggleLike()
    {
        // 🚀 Optimistic UI
        if ($this->likedByMe) {
            $this->likesCount--;
        } else {
            $this->likesCount++;
        }

        $this->likedByMe = ! $this->likedByMe;

        //passed to the LikeService + Job
        app(LikeService::class)->toggle(
            $this->post->unicode,
            auth()->user()
        );
    }

    public function openVideoPlayer($videoId)
    {
        $this->activeVideoId = $videoId;
        $this->showPlayer = true;
        // dd("Opening video player for video ID: $videoId");

        // dd("Opening video player for video ID: $videoId");
        // Emit event to open video player with this video
        //    $this->dispatch('openVideoPlayer', videoId: $videoId);

        //    $this->dispatch('openVideoPlayer', videoId: $videoId)
        //         ->to(\App\Livewire\User\VideoPlayer::class);
    }

    public function closeVideoPlayer()
    {
        $this->showPlayer = false;
        $this->activeVideoId = null;
    }






    public function render()
    {
        return view('livewire.user.post-content');
    }
}
