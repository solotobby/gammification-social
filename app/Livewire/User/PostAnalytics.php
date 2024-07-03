<?php

namespace App\Livewire\User;

use App\Models\Post;
use Livewire\Component;

class PostAnalytics extends Component
{

    public $id, $post, $unpaidViews, $unpaidLikes, $unpaidComments, $unpaidExternalViews, $unpaidExternalComments;

    public function mount($id){
        $this->id = $id;
        $this->post = Post::find($this->id);

        //unpaid likes
        $this->unpaidLikes = $this->post->unpaidLikes();
        //paid likes


        //unpaid views
        $this->unpaidViews = $this->post->unpaidViews();
        //paid views

        //unpaid comments
        $this->unpaidComments = $this->post->unpaidComments();
        $this->unpaidExternalComments = $this->post->unpaidExternalComments();



        //unpaid external Views
        $this->unpaidExternalViews = $this->post->unpaidExternalViews();

    }
    public function render()
    {
        return view('livewire.user.post-analytics');
    }
}
