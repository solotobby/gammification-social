<?php

namespace App\Livewire\User;

use App\Models\PostVideo;
use App\Services\VideoPlayerService;
use Livewire\Component;

class VideoRolls extends Component
{

    public $videoId;
    public $context = 'global'; // global, user, following, trending
    public $contextUserId = null;
    public $videos = [];
    public $currentIndex = 0;

    protected $queryString = ['videoId', 'context'];

    public function mount($videoId, $context = 'global', $userId = null)
    {
        $this->videoId = $videoId;
        $this->context = $context;
        $this->contextUserId = $userId;

         // Load videos using the service
        $this->loadInitialVideos();

        // $this->videos = VideoPlayerService::getVideosFromPoint($videoId, $context, $userId);

        // $this->currentIndex = 0;

        // Load videos based on context
        // $this->loadVideos();
    }

    private function loadInitialVideos()
    {
        $this->videos = VideoPlayerService::getVideosFromPoint(
            $this->videoId, 
            $this->context
        );
        
        // Find the index of the current video
        $this->currentIndex = 0;
        foreach ($this->videos as $index => $video) {
            if ($video['id'] == $this->videoId) {
                $this->currentIndex = $index;
                break;
            }
        }
        
        // Emit event to load the video
        $this->dispatch('videoLoaded', videoId: $this->videoId);

    }

    public function nextVideo()
    {
        if ($this->currentIndex < count($this->videos) - 1) {
            $this->currentIndex++;
            $this->videoId = $this->videos[$this->currentIndex]['id'];
            $this->updateUrl();
             $this->dispatch('videoChanged', videoId: $this->videoId);
        } else {
            // Load more videos
            $this->loadMoreVideos();
        }

        // if ($this->currentIndex < count($this->videos) - 1) {
        //     $this->currentIndex++;
        //     $this->videoId = $this->videos[$this->currentIndex]['id'];
        //     $this->updateUrl();
        // } else {
        //     // Load more videos
        //     $this->loadMoreVideos();
        // }
    }

    public function previousVideo()
    {

        if ($this->currentIndex > 0) {
            $this->currentIndex--;
            $this->videoId = $this->videos[$this->currentIndex]['id'];
            $this->updateUrl();
           $this->dispatch('videoChanged', videoId: $this->videoId);
        }

        // if ($this->currentIndex > 0) {
        //     $this->currentIndex--;
        //     $this->videoId = $this->videos[$this->currentIndex]['id'];
        //     $this->updateUrl();
        // }
    }

    private function loadMoreVideos()
    {
        if (count($this->videos) > 0) {
            $lastVideo = end($this->videos);
            
            $moreVideos = VideoPlayerService::loadMoreVideos(
                $lastVideo['id'], 
                $this->context, 
                $this->contextUserId
            );

            if (count($moreVideos) > 0) {
                $this->videos = array_merge($this->videos, $moreVideos);
                $this->nextVideo();
            }
        }
    }

    private function updateUrl()
    {
        // Update URL without page reload
        $url = route('rolls.show', [
            'videoId' => $this->videoId,
            'context' => $this->context 
        ]);
       $this->dispatch('updateBrowserUrl', url: $url);
    }

    public function getCurrentVideoProperty()
    {
        if (isset($this->videos[$this->currentIndex])) {
            return $this->videos[$this->currentIndex];
        }
        return null;
    }

    public function recordView()
    {
        if ($this->videoId) {
            $video = PostVideo::find($this->videoId);
            if ($video) {
                $video->incrementViews();
            }
        }
    }

    public function recordPlay()
    {
        if ($this->videoId) {
            $video = PostVideo::find($this->videoId);
            if ($video) {
                $video->incrementPlays();
            }
        }
    }


     /**
     * Format duration in seconds to readable format
     */
    public function formatDuration($seconds)
    {
        if (!$seconds) {
            return '0:00';
        }

        $minutes = floor($seconds / 60);
        $seconds = $seconds % 60;

        if ($minutes < 60) {
            return sprintf('%d:%02d', $minutes, $seconds);
        }

        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;

        return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
    }

    /**
     * Format large numbers (1000 -> 1K, 1000000 -> 1M)
     */
    public function formatCount($count)
    {
        if ($count >= 1000000) {
            return number_format($count / 1000000, 1) . 'M';
        } elseif ($count >= 1000) {
            return number_format($count / 1000, 1) . 'K';
        }
        
        return number_format($count);
    }


    public function render()
    {
        return view('livewire.user.video-rolls', [
            'currentVideo' => $this->currentVideo,
        ])->layout('layouts.rolls-layout');
    }
}
