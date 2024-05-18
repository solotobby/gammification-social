<?php

namespace App\Livewire\User;

use App\Models\Post;
use App\Models\UserLike;
use App\Models\UserView;
use Livewire\Component;

class ShowPost extends Component
{
    public $postQuery;

    public $timeline;

    public $id;

    public function mount($query){
        $this->postQuery = $query;
        $this->timeline = Post::where('id', $this->postQuery)->first();
        
       $regView = UserView::where(['user_id' => auth()->user()->id, 'post_id' => $this->timeline->id])->first();
        if(!$regView){
            UserView::create(['user_id' => auth()->user()->id, 'post_id' => $this->timeline->id]);
            $this->timeline->views += 1;
            $this->timeline->save(); 
        }
        // $this->viewCount($this->postQuery);
    }

    public function viewCount($id){
        $post = Post::where('id', $id)->first();
        $post->views += 1;
        $post->save(); 
        return $post;
    }

    public function like($id){
        
        $post =  Post::where('unicode', $id)->first();
        $post->likes += 1;
        $post->save(); 
        UserLike::create(['user_id' => auth()->user()->id, 'post_id' => $post->id]);
        $this->dispatch('user.show-post', $post->id);
        
     }
 
     public function dislike($id){
     
         $post =  Post::where('unicode', $id)->first();
         $post->likes -= 1;
         $post->save(); 
         UserLike::where(['user_id' => auth()->user()->id, 'post_id' => $post->id])->delete();
         $this->dispatch('user.show-post');
         
     }



    
    public function render()
    {
        return view('livewire.user.show-post', ['post' => Post::where('id', $this->postQuery)->first()]);
    }
}
