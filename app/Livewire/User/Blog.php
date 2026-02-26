<?php

namespace App\Livewire\User;

use App\Models\Blog as ModelsBlog;
use Livewire\Component;

class Blog extends Component
{
    public function render()
    {
        $blogs = ModelsBlog::where('status', 'PUBLISHED')->orderBy('created_at', 'DESC')->paginate(10);
        return view('livewire.user.blog', ['blogs' => $blogs]);
    }
}
