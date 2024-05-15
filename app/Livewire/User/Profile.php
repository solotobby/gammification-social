<?php

namespace App\Livewire\User;

use App\Models\Post;
use Livewire\Component;


class Profile extends Component
{

    public $timelines;

    public function timelines(){
        return   Post::where(['status'=>'LIVE', 'user_id' => auth()->user()->id])->orderBy('created_at', 'desc')->get();//Post::all();
    }

    public function mount(){
        $this->timelines = $this->timelines();
    }

    public function render()
    {
        return view('livewire.user.profile');
    }

}
