<?php

namespace App\Livewire\User;

use App\Models\Post;
use Livewire\Component;

class PostAnalytics extends Component
{

    public $id, $post;

    public function mount($id){
        $this->id = $id;
        $this->post = Post::find($this->id);
    }
    public function render()
    {
        return view('livewire.user.post-analytics');
    }
}
