<?php

namespace App\Livewire\User;

use App\Models\Post;
use Livewire\Component;

class Analytics extends Component
{
    public $post;
    public function mount(){
        
        $this->post = Post::where('user_id', auth()->user()->id)->get();
        
    }
    public function render()
    {
        return view('livewire.user.analytics');
    }
}
