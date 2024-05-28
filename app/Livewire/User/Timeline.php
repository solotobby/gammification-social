<?php

namespace App\Livewire\User;

use App\Models\Post;
use App\Models\Timeline as ModelsTimeline;
use App\Models\UserLike;
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

    public $postId;
    #[Validate('required|string')]
    public $content = '';

    public $message = '';

    protected $listeners=['refresh'=>'$refresh'];

    public function timelines(){
       return Post::with('likes')->where('status', 'LIVE')->orderBy('created_at', 'desc')->get();//Post::all();

    }


    public function mount(){
        $this->timelines = $this->timelines();
    }

    

    // public function like($id){
        
    //     $post =  Post::where('unicode', $id)->first();
    //     $post->likes += 1;
    //     $post->save(); 
    //     UserLike::create(['user_id' => auth()->user()->id, 'post_id' => $post->id]);
    //     $this->dispatch('user.timeline');
        
    //  }
 
    //  public function dislike($id){
     
    //      $post =  Post::where('unicode', $id)->first();
    //      $post->likes -= 1;
    //      $post->save(); 
    //      UserLike::where(['user_id' => auth()->user()->id, 'post_id' => $post->id])->delete();
    //      $this->dispatch('user.timeline');
         
    //  }

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


    public function post(){

        // dd($this->convertUrlsToLinks($this->content));

        $timelines = Post::create(['user_id' => auth()->user()->id, 'content' => $this->convertUrlsToLinks($this->content), 'unicode' => time()]);
        $this->reset('content');

        $this->timelines->push($timelines);

        $this->dispatch('user.timeline');

        session()->flash('success', 'Posted Created Successfully');

    }

    private function convertUrlsToLinks($text)
    {

        // $pattern = '/\b(?:https?:\/\/|www\.|[\w-]+\.[\w-]+\.[\w-]+)\S*\b/i';
        // $replacement = '<a href="$0" target="_blank" rel="noopener noreferrer">$0</a>';
        // $text = preg_replace($pattern, $replacement, $text);

        // // Add "http://" prefix if missing
        // return preg_replace_callback(
        //     '/<a href="([^"]+)"/i',
        //     function ($matches) {
        //         $url = $matches[1];
        //         if (!preg_match('/^https?:\/\//i', $url)) {
        //             $url = 'http://' . $url;
        //         }
        //         return '<a href="' . $url . '"';
        //     },
        //     $text
        // );

        $pattern = '/\b(?:https?:\/\/|www\.)\S+\b/';
        $replacement = '<a href="$0" target="_blank" rel="noopener noreferrer">$0</a>';
        return preg_replace($pattern, $replacement, $text);
    }

    // public function comment(){
    //     dd($this->message);
    // }

    // public function showTimeline($id){
        
    //     $this->postId = $id;
    //     return redirect('show/'.$this->postId);//->route('show', ['query' => $this->postId]);
       
    // }

    public function render()
    {
        return view('livewire.user.timeline');
    }

}
