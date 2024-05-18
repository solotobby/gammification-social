<?php

namespace App\Livewire\User;

use App\Models\Post;
use App\Models\User;
use App\Models\UserLike;
use Livewire\Component;


class Profile extends Component
{

    public $timelines, $user;

    // public function timelines(){
    //     return   Post::where(['status'=>'LIVE', 'user_id' => auth()->user()->id])->orderBy('created_at', 'desc')->get();//Post::all();
    // }

    public function mount(){
        $this->timelines = auth()->user()->posts()->where(['status'=>'LIVE', 'user_id' => auth()->user()->id])->orderBy('created_at', 'desc')->get();//$this->timelines();
        $this->user = User::withPostStats(auth()->user()->id)->first();
        // $this->user = User::withTotalLikes()->find(auth()->user()->id);
    }


    public function like($id){
        // dd(auth()->user()->WithTotalLikes($id));
        $post =  Post::where('unicode', $id)->first();
        $post->likes += 1;
        $post->save(); 
        UserLike::create(['user_id' => auth()->user()->id, 'post_id' => $post->id]);
        $this->dispatch('user.timeline');
        
     }
 
     public function dislike($id){
     
         $post =  Post::where('unicode', $id)->first();
         $post->likes -= 1;
         $post->save(); 
         UserLike::where(['user_id' => auth()->user()->id, 'post_id' => $post->id])->delete();
         $this->dispatch('user.timeline');
         
     }


    public function render()
    {
        return view('livewire.user.profile');
    }

}
