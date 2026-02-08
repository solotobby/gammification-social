<?php

namespace App\Livewire\User;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Search extends Component
{

    public string $query = '';
    public $result = [];

    public function search()
    {
       
        if (strlen($this->query) < 2) {
            $this->reset('result');
            return;
        }

        $this->result = User::where('status', 'ACTIVE')
            ->where(
                fn($q) =>
                $q->where('name', 'like', "%{$this->query}%")
                    ->orWhere('username', 'like', "%{$this->query}%")
            )
            ->limit(10)
            ->get();

            // dd($this->result);
    }



    public function updated()
    {

        if (strlen($this->query) < 2) {
            $this->reset('result');
            return;
        }

        $this->result = User::query()
            ->where('status', 'ACTIVE')
            ->where(function ($q) {
                $q->where('name', 'like', "%{$this->query}%")
                    ->orWhere('username', 'like', "%{$this->query}%");
            })
            ->limit(10)
            ->get();
    }

    // public array $results = [
    //     'users' => [],
    //     'posts' => [],
    // ];

    // public function mount()
    // {
    //      $this->result = User::query()
    //         ->where('status', 'ACTIVE')
    //         ->where(function ($q) {
    //             $q->where('name', 'like', "%{$this->query}%")
    //               ->orWhere('username', 'like', "%{$this->query}%");
    //         })
    //         ->limit(10)
    //         ->get();
    // }

    // public function updatedQuery()
    // {
    //     if (strlen($this->query) < 2) {
    //         $this->reset('results');
    //         return;
    //     }

    //     dd($this->query);

    //    $this->result = User::query()
    //         ->where('status', 'ACTIVE')
    //         ->where(function ($q) {
    //             $q->where('name', 'like', "%{$this->query}%")
    //               ->orWhere('username', 'like', "%{$this->query}%");
    //         })
    //         ->limit(10)
    //         ->get();

    //     // $this->search();
    // }

    // protected function search()
    // {

    //     $user = Auth::user();

    //     // 🔍 USERS
    //     $this->results['users'] = User::query()
    //         ->where('status', 'ACTIVE')
    //         ->where(function ($q) {
    //             $q->where('name', 'like', "%{$this->query}%")
    //               ->orWhere('username', 'like', "%{$this->query}%");
    //         })
    //         ->limit(10)
    //         ->get();

    //     // 🔍 POSTS
    //     $this->results['posts'] = Post::query()
    //         ->where('status', 'LIVE')
    //         ->whereHas('user', function ($q) use ($user) {
    //             // Hide shadow-banned users except self
    //             $q->where(function ($q2) use ($user) {
    //                 $q2->where('status', 'ACTIVE');

    //                 if ($user) {
    //                     $q2->orWhere('id', $user->id);
    //                 }
    //             });
    //         })
    //         ->where('content', 'like', "%{$this->query}%")
    //         ->latest()
    //         ->limit(10)
    //         ->get();



    // }

    public function render()
    {
        return view('livewire.user.search');
    }
}
