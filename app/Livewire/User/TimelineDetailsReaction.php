<?php

namespace App\Livewire\User;

use App\Jobs\ProcessLikeJob;
use App\Models\Post;
use App\Services\LikeService;
use Livewire\Component;
use Symfony\Component\Process\Process;

class TimelineDetailsReaction extends Component
{

    public Post $post;
    
    public bool $likedByMe = false;

    public $likesCount = 0;
    public $commentsCount = 0;
    public $viewsCount = 0;
    public $comments ;

    public $user;

    protected $listeners = [
        'commentAdded' => 'refreshCommentsCount',
    ];

    public function mount(Post $post){
        $this->post = $post;
        $this->user = $post->user;


        $this->likedByMe     = $post->isLikedBy(auth()->user());
        $this->likesCount    =  $post->likes;
        $this->commentsCount =  $post->comments + $post->comment_external;
        $this->viewsCount    =  $post->views + $post->views_external;

    }

    public function toggleLike()
    {
        // Optimistic UI
        $this->likedByMe = ! $this->likedByMe;
        $this->likesCount += $this->likedByMe ? 1 : -1;

        // Call the service to toggle like in the database
        ProcessLikeJob::dispatch(
            $this->post->unicode,
            auth()->id()
        );

        //passed to the LikeService + Job
        // app(LikeService::class)->toggle(
        //     $this->post->unicode,
        //     auth()->user()
        // );

    }

    public function refreshCommentsCount()
    {
        $this->commentsCount++;
    }




    public function render()
    {
        return view('livewire.user.timeline-details-reaction');
    }
}
