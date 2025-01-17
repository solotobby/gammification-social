<?php

namespace App\Livewire\User;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use Livewire\Component;

class Posts extends Component
{
    use WithFileUploads;


    public $perpage = 10;

    public $postId;
    #[Validate('required|string')]
    public $content = '';
    
    public $images = [];
    public $imagePreviews = [];

    protected $rules = [
        'content' => 'required|string',
        'images.*' => 'nullable|image|max:1024', // 1MB Max per image
    ];

   

    public function updatedImages()
    {
        $this->validate([
            'images.*' => 'image|max:1024', // 1MB Max per image
        ]);

        $this->imagePreviews = [];
        foreach ($this->images as $image) {
            $this->imagePreviews[] = $image->temporaryUrl();
        }
    }


    public function loadMore(){
        $this->perpage += 10;
    }

    public function post(){
        $content = $this->convertUrlsToLinks($this->content);
        $getContent = Post::where(['user_id' => auth()->user()->id])->pluck('content')->toArray();

        // dd($content);
        
        if (isSimilar($content, $getContent, 4)) {
            session()->flash('info', 'This content is too similar to existing content, therefore it not be posted.');
            $this->reset('content');
            // dd("This content is too similar to existing content and will not be posted.");
        } else {
            $timelines = Post::create(['user_id' => auth()->user()->id, 'content' => $content, 'unicode' => time()]);
            $this->reset('content');
        }


        // if(!$getContent){
        //     // $content = $this->convertUrlsToLinks($this->content);
        //     

        // }
        // $this->reset('content');
        
        
        // $this->dispatch('refreshTimeline');

        // session()->flash('success', 'Posted Created Successfully');

    }

    private function isSimilar($newData, $existingData, $threshold = 5) {
        $normalizedNewData = normalizeText($newData);
        
        foreach ($existingData as $data) {
            $normalizedData = normalizeText($data);
            $levenshteinDistance = levenshtein($normalizedNewData, $normalizedData);
            
            if ($levenshteinDistance <= $threshold) {
                return true;
            }
        }
        
        return false;
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
        ->where('status', 'LIVE')
        ->orderBy('created_at', 'desc')
        ->get();
        // Group posts by user_id
        $groupedPosts = $posts->groupBy('user_id');

        // Flatten the grouped collection in an interleaved manner
        $interleavedPosts = new Collection();
        while ($groupedPosts->isNotEmpty()) {
            foreach ($groupedPosts as $userId => $userPosts) {
                if ($userPosts->isNotEmpty()) {
                    $interleavedPosts->push($userPosts->shift());
                    if ($userPosts->isEmpty()) {
                        $groupedPosts->forget($userId);
                    }
                }
            }
        }

        return view('livewire.user.posts', ['posts' => $interleavedPosts]);
    }
}
