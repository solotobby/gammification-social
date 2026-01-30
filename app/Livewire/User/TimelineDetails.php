<?php

namespace App\Livewire\User;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TimelineDetails extends Component
{
    public Post $post;
    public $user;


    public function mount(Post $post){
        $this->post = $post;
        $this->user = $post->user;
        
    }

    public function render()
    {
        return view('livewire.user.timeline-details');
    }
}
