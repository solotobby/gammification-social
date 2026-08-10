<?php

namespace App\Livewire\User;

use App\Models\Post;
use Livewire\Component;

class PostAnalytics extends Component
{
    public $id;

    public $post;

    public function mount($id)
    {
        $this->post = Post::findOrFail($id);

        if ($this->post->user_id !== auth()->id()) {
            abort(403);
        }
    }

    public function render()
    {
        return view('livewire.user.post-analytics');
    }
}
