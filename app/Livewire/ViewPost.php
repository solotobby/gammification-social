<?php

namespace App\Livewire;

use Livewire\Component;

class ViewPost extends Component
{

    public $userName, $postId;

    public function mount($username, $id){
        $this->userName = $username;
        $this->postId = $id; 

        // dd($this->userName);
    }
    public function render()
    {
        return view('livewire.view-post');
    }
}
