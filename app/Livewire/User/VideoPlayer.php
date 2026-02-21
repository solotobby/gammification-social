<?php

namespace App\Livewire\User;

use App\Models\PostVideo;
use Livewire\Component;
use Livewire\Attributes\On;

class VideoPlayer extends Component
{
    public $isOpen = false;
    public $currentVideoId = null;
    public $videos = [];
    public $currentIndex = 0;
     public $videoId;

    // protected $listeners = ['openVideoPlayer'];

    #[On('openVideoPlayer')]

    //  public function mount($videoId)
    // {
    //     $this->videoId = $videoId;
    //     $this->loadVideos($videoId);
    // }

     public function mount($videoId)
    {
       dd("Mounting VideoPlayer with video ID: $videoId");
        $this->videoId = $videoId;
        // $this->loadVideos($videoId);
    }


    // public function openVideoPlayer($videoId)
    // {
    //     $this->currentVideoId = $videoId;
    //     $this->isOpen = true;

    //     // logger('Video Player Opened', ['id' => $videoId]);

    //     $this->loadVideos($videoId);

    // }

    private function loadVideos($startVideoId)
    {
        // Get all completed videos
        $allVideos = PostVideo::with(['post.user', 'user'])
            ->where('processing_status', 'completed')
            ->latest()
            ->get();

        $this->videos = $allVideos->toArray();
        
        // Find the current video index
        $this->currentIndex = $allVideos->search(function ($video) use ($startVideoId) {
            return $video->id == $startVideoId;
        });

        if ($this->currentIndex === false) {
            $this->currentIndex = 0;
        }
    }

    public function closePlayer()
    {
        $this->isOpen = false;
        $this->currentVideoId = null;
        $this->videos = [];
        $this->currentIndex = 0;
        
       $this->dispatch('videoPlayerClosed');
    }

    public function render()
    {
        return view('livewire.user.video-player');
    }
}
