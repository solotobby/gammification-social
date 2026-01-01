<?php

namespace App\Livewire\User;

use Livewire\Component;

class NavbarNotifications extends Component
{

     protected $listeners = ['refreshNotifications' => '$refresh'];

    public function markAsRead($id)
    {
        auth()->user()
            ->notifications()
            ->where('id', $id)
            ->update(['read_at' => now()]);
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
    }


    public function render()
    {
        return view('livewire.user.navbar-notifications', [
            'notifications' => auth()->user()->notifications()->latest()->take(5)->get(),
            'unreadCount'   => auth()->user()->unreadNotifications->count(),
        ]);
    }
}
