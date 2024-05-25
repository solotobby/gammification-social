<?php

namespace App\Livewire\User;

use App\Models\Post;
use App\Models\User;
use App\Models\UserLike;
use Livewire\Component;
use Livewire\Attributes\On; 

class ViewProfile extends Component
{

    public $id, $timelines, $highestEngagement;

    public User $user;

    #[On('view-profile.{user.id}')] 

    public function mount($id){
        $this->id = $id;
        // dd($this->id);
        $this->user = User::withPostStats($this->id)->first();
        $this->timelines = $this->user->posts()->where(['status'=>'LIVE', 'user_id' =>  $this->id])->orderBy('created_at', 'desc')->get();//$this->timelines();
       
        
    }

    

    public function like($id){
        // dd(auth()->user()->WithTotalLikes($id));
        $post =  Post::where('unicode', $id)->first();
        $post->likes += 1;
        $post->save(); 
        
        UserLike::create(['user_id' => auth()->user()->id, 'post_id' => $post->id]);
        $this->dispatch('user.view-profile', $post);
        
     }
 
     public function dislike($id){
     
         $post =  Post::where('unicode', $id)->first();
         $post->likes -= 1;
         $post->save(); 
         UserLike::where(['user_id' => auth()->user()->id, 'post_id' => $post->id])->delete();
         $this->dispatch('user.view-profile', id:$post->id);
         
     }

    public function render()
    {
        return view('livewire.user.view-profile');
    }
}
