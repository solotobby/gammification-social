<?php

namespace App\Livewire\User;

use App\Models\Post;
use App\Models\Timeline as ModelsTimeline;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Attributes\On;

#[On('user.timeline')]
class Timeline extends Component
{
    public $timelines;
    #[Validate('required|string')]
    public $content = '';

    protected $listeners=['refresh'=>'$refresh'];

    public function timelines(){
        return   Post::where('status', 'LIVE')->orderBy('created_at', 'desc')->get();//Post::all();
     }


    public function mount(){
        $this->timelines = $this->timelines();
    }


    public function post(){

        $timelines = Post::create(['user_id' => auth()->user()->id, 'content' => $this->content]);
        $this->reset('content');

        $this->timelines->push($timelines);

        $this->dispatch('user.timeline');

        session()->flash('success', 'Posted Created Successfully');

    }

    public function render()
    {
        return view('livewire.user.timeline');
    }

}
