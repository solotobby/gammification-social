<?php

namespace App\Livewire\User;

use App\Models\Hashtag as ModelsHashtag;
use Livewire\Component;

class Hashtag extends Component
{

    public $tag; 

    public function mount($tag){
        $this->tag = $tag;
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
                'hashtags'
            ])
            ->latest()
            ->paginate(15);

        return view('livewire.user.hashtag', [
                'hashtag'=>$hashtag,
                'posts'=>$posts
            ]);
    }
}
