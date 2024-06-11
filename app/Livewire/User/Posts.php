<?php

namespace App\Livewire\User;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Posts extends Component
{
    public $perpage = 10;

    public $postId;
    #[Validate('required|string')]
    public $content = '';

    public function loadMore(){
        $this->perpage += 10;
    }

    public function post(){

        $content = $this->convertUrlsToLinks($this->content);
        // $content = nl2br(e($content));

        $timelines = Post::create(['user_id' => auth()->user()->id, 'content' => $content, 'unicode' => time()]);
        $this->reset('content');

        // $this->timelines->push($timelines);

        // $this->dispatch('refreshTimeline');

        // session()->flash('success', 'Posted Created Successfully');

    }

    private function convertUrlsToLinks($text)
    {
        $pattern = '/\b(?:https?:\/\/|www\.)\S+\b/';
        $replacement = '<a href="$0" target="_blank" rel="noopener noreferrer">$0</a>';
        return preg_replace($pattern, $replacement, $text);
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

        // $this->timelines();

        // $this->dispatch('user.timeline');
     }



    public function render()
    {
        $posts = Post::take($this->perpage)
        ->orderBy('created_at', 'desc')
        ->get();
        return view('livewire.user.posts', ['posts' => $posts]);
    }
}
