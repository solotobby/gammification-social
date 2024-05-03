<?php

namespace App\Livewire\User;

use App\Models\Post;
use App\Models\Timeline as ModelsTimeline;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Timeline extends Component
{
    public $timelines;

    public function timelines(){
        return   Post::where('status', 'LIVE')->orderBy('created_at', 'DESC')->get();//Post::all();
     }


    public function mount(){
        $this->timelines = $this->timelines();
    }

   

    #[Validate('required|string')]
    public $content = '';
    public function post(){

        $timelines = Post::create(['user_id' => auth()->user()->id, 'content' => $this->content]);
        $this->reset('content');

        $this->timelines->push($timelines);

        $this->dispatch('user.timelines');

        session()->flash('success', 'Posted Created Successfully');

    }

    public function render()
    {
        return view('livewire.user.timeline');
    }

}
