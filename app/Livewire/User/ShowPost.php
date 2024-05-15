<?php

namespace App\Livewire\User;

use App\Models\Post;
use Livewire\Component;

class ShowPost extends Component
{
    public $postid;

    public $timeline;

    public function mount($id){
        $this->postid = $id;
        Post::where('id', $this->postid)->first();
        // $this->timeline = $this->show($this->id);
    }

    
    public function render()
    {
        return view('livewire.user.show-post');
    }
}
