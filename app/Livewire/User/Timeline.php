<?php

namespace App\Livewire\User;

use App\Models\Post;
use App\Models\Timeline as ModelsTimeline;
use App\Models\UserLike;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Attributes\On;

#[On('user.timeline')]
class Timeline extends Component
{
    public $count = 0;
    public $timelines, $id;

    public $postId;
    #[Validate('required|string')]
    public $content = '';

    protected $listeners=['refresh'=>'$refresh'];

    public function timelines(){
        return Post::where('status', 'LIVE')->orderBy('created_at', 'desc')->get();//Post::all();
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

    public function like($id){
        
       $post =  Post::find($id);
       $post->likes += 1;
       $post->save(); 
       UserLike::create(['user_id' => auth()->user()->id, 'post_id' => $post->id]);
       $this->dispatch('user.timeline');
       
    }

    public function dislike($id){
    
        $post =  Post::find($id);
        $post->likes -= 1;
        $post->save(); 
        UserLike::where(['user_id' => auth()->user()->id, 'post_id' => $post->id])->delete();
        $this->dispatch('user.timeline');
        
    }

    public function showTimeline($id){
        
        $this->postId = $id;
        return redirect('show/'.$this->postId);//->route('show', ['query' => $this->postId]);
       
    }

    public function render()
    {
        return view('livewire.user.timeline');
    }

}
