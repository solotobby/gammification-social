<?php

namespace App\Livewire\User;

use App\Models\PostVideo;
use Livewire\Component;
use Livewire\Attributes\On;

class VideoPlayer extends Component
{
    public $videoId;

    #[On('openVideoPlayer')]
    public function openVideoPlayer($videoId): void
    {
        $this->redirectRoute('rolls.show', ['video' => $videoId], navigate: true);
    }

    public function mount($videoId = null): void
    {
        if ($videoId) {
            $this->redirectRoute('rolls.show', ['video' => $videoId], navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.user.video-player');
    }
}
