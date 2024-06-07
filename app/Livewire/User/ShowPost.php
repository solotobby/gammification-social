<?php

namespace App\Livewire\User;

use App\Models\Comment;
use App\Models\Post;
use App\Models\UserComment;
use App\Models\UserLike;
use App\Models\UserView;
use Illuminate\Support\Facades\Auth;
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

    protected $listeners = ['refreshComments' => 'refreshComments'];

    public function mount($query){
        
       $this->timeline($query);
    }

    public function timeline($query){

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

        // Load initial comments
        // $this->refreshComments();

    }

    public function loadMore()
    {
        $additionalComments = $this->timeline->postComments()
            ->orderBy('created_at', 'asc')
            ->paginate($this->perPage, ['*'], 'page', $this->page);

        $this->comments = array_merge($this->comments, $additionalComments->items());
        $this->page++;
        
    }

    public function toggleLike($postId){

        $post = Post::where('unicode', $postId)->first();
        
        if ($post->isLikedBy(Auth::user())) {
            $post->likes()->where('user_id', Auth::id())->delete();
            $post->decrement('likes');
        } else {
            $post->likes()->create(['user_id' => Auth::id()]);
            $post->increment('likes');
        }

        $this->timeline($post->id);

        // $this->dispatch('user.timeline');
     }

   

     public function comment(){
        Comment::create(['user_id' => auth()->user()->id, 'post_id' =>$this->postQuery, 'message' =>  $this->message]);
        $pst = Post::with(['postComments'])->where(['id' => $this->postQuery])->first();
        $pst->comments += 1;
        $pst->save();

        $checkUniqueComment = UserComment::where(['user_id' => auth()->user()->id, 'post_id' => $this->postQuery])->first();
        
        if(!$checkUniqueComment){
            if(auth()->user()->id != $pst->user_id){
                UserComment::create(['user_id' => auth()->user()->id, 'post_id' => $this->postQuery]);
            }
        }

        $this->reset('message');
        // $this->timeline->push($pst);
        $this->dispatch('refreshComments');

    }

    public function refreshComments()
    {
        // Load initial comments
        $this->timeline = Post::with(['postComments'])->where('id', $this->postQuery)->first();
       
    }

    public function render()
    {
        // $comments = $this->timeline->postComments()->paginate($this->perPage, ['*'], 'page', $this->page);

        return view('livewire.user.show-post');

        // return view('livewire.user.show-post');
    }
}
