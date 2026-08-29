<?php

namespace App\Livewire\User;

use Livewire\Component;

class NavbarMessages extends Component
{
    public function render()
    {
        return view('livewire.user.navbar-messages', [
            'unreadCount' => messagingUnreadTotal(),
        ]);
    }
}
