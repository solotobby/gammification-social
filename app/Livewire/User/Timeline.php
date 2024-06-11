<?php

namespace App\Livewire\User;

use App\Models\Post;
use App\Models\Timeline as ModelsTimeline;
use App\Models\UserLike;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Attributes\On;

#[On('user.timeline')]
class Timeline extends Component
{

    public $count = 0;

    public $timelines, $id, $long, $highestEngagement; 

    public $isLiked;
    
    // use WithPagination;

    public $perPage = 2;

    public $postId;
    #[Validate('required|string')]
    public $content = '';

    public $message = '';

    // protected $listeners=['refresh'=>'$refresh'];
    protected $listeners = ['refreshTimeline' => 'refreshTimeline'];

    public function timelines(){

        return Post::with('likes')
                ->where('status', 'LIVE')
                ->orderBy('created_at', 'desc')
                // ->take($this->perPage)
                ->get();
    }


    // public function mount(){
    //     $this->timelines = $this->timelines();
    // }

     public function toggleLike($postId){

        $post = Post::where('unicode', $postId)->first();

        if ($post->isLikedBy(Auth::user())) {
            $post->likes()->where('user_id', Auth::id())->delete();
            $post->decrement('likes');
        } else {
            $post->likes()->create(['user_id' => Auth::id()]);
            $post->increment('likes');
        }

        $this->timelines();

        $this->dispatch('user.timeline');
     }

     public function loadMore(){
        $this->perPage += 2;
     }

    


    public function post(){

        $content = $this->convertUrlsToLinks($this->content);
        // $content = nl2br(e($content));

        $timelines = Post::create(['user_id' => auth()->user()->id, 'content' => $content, 'unicode' => time()]);
        $this->reset('content');

        $this->timelines->push($timelines);

        $this->dispatch('refreshTimeline');

        // session()->flash('success', 'Posted Created Successfully');

    }

    public function refreshTimeline()
    {
        $this->timelines = $this->timelines();
    }

    private function convertUrlsToLinks($text)
    {
        $pattern = '/\b(?:https?:\/\/|www\.)\S+\b/';
        $replacement = '<a href="$0" target="_blank" rel="noopener noreferrer">$0</a>';
        return preg_replace($pattern, $replacement, $text);
    }

    

    public function render()
    {
        $timelines= Post::with('likes')
                ->where('status', 'LIVE')
                ->orderBy('created_at', 'desc')
                ->take($this->perPage)->get();

        return view('livewire.user.timeline', ['timelines' => $timelines]);
    }

}
