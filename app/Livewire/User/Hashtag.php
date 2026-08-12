<?php

namespace App\Livewire\User;

use App\Models\Hashtag as ModelsHashtag;
use App\Services\PostEarningsService;
use Livewire\Attributes\On;
use Livewire\Component;

class Hashtag extends Component
{

    public $tag; 

    public function mount($tag){
        $this->tag = $tag;
    }

    #[On('postDeleted')]
    public function handlePostDeleted(string $postId): void
    {
        // Render re-queries posts; this handler forces a refresh after delete.
    }

    #[On('post-action-toast')]
    public function handlePostActionToast(string $message): void
    {
        session()->flash('success', $message);
    }

    public function render()
    {
       
         $hashtag =  ModelsHashtag::where(
            'name',
            $this->tag
        )
        ->firstOrFail();



        $posts = $hashtag
            ->posts()
            ->with([
                'user',
                'hashtags',
                'images',
                'video',
            ])
            ->latest()
            ->paginate(15);

        $earnings = app(PostEarningsService::class)->forPosts(
            $posts->getCollection()->pluck('id')
        );

        return view('livewire.user.hashtag', [
                'hashtag'=>$hashtag,
                'posts'=>$posts,
                'earnings' => $earnings,
            ]);
    }
}
