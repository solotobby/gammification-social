<?php

namespace App\Livewire\User;

use App\Models\Comment;
use App\Models\Post;
use App\Models\UserLike;
use App\Models\UserView;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;

#[On('user.show-post')]

class ShowPost extends Component
{
    public $postQuery;

    public $timeline;

    public $id;

    public $comments = [];
    public $perPage = 5;
    public $page = 1;

    #[Validate('required|string')]
    public $message = '';

    protected $listeners = ['commentAdded' => '$refresh'];

    public function mount($query){
        $this->postQuery = $query;
        $this->timeline = Post::with(['postComments'])->where('id', $this->postQuery)->first();
        // $this->comments;
        // Load initial comments
        $this->loadMore();

        $regView = UserView::where(['user_id' => auth()->user()->id, 'post_id' => $this->timeline->id])->first();
        if(!$regView){
            UserView::create(['user_id' => auth()->user()->id, 'post_id' => $this->timeline->id]);
            $this->timeline->views += 1;
            $this->timeline->save(); 
        }
       
    }

    public function loadMore()
    {
        $additionalComments = $this->timeline->postComments()
            ->orderBy('created_at', 'asc')
            ->paginate($this->perPage, ['*'], 'page', $this->page);

        $this->comments = array_merge($this->comments, $additionalComments->items());
        $this->page++;
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

     public function comment(){
        Comment::create(['user_id' => auth()->user()->id, 'post_id' =>$this->postQuery, 'message' =>  $this->message]);
        $pst = Post::where(['id' => $this->postQuery])->first();
        $pst->comments += 1;
        $pst->save();

        $this->reset('message');
        // $this->timelines->push($timelines);
        $this->dispatch('commentAdded');
        // $this->redirect(url('show/'.$this->postQuery));
    }

    public function render()
    {
        return view('livewire.user.show-post', [
            'post' => $this->timeline,
            // 'comments' => $this->comments
        ]);
    }
}
