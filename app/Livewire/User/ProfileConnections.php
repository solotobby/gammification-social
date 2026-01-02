<?php

namespace App\Livewire\User;

use Livewire\WithPagination;
use App\Models\User;
use App\Models\Follow;
use Livewire\Component;

class ProfileConnections extends Component
{

    use WithPagination;

    // public User $user;
    public $username;
    public string $activeTab = 'followers'; // default tab
    protected $paginationTheme = 'bootstrap';

    public function mount($username)
    {
        $this->username = $username;
        // Optional: check query string for tab
        if (request()->query('tab') === 'following') {
            $this->activeTab = 'following';
        }
    }


    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    // public function toggleFollow($personId)
    // {
    //     $authUser = auth()->user();
    //     $targetUser = User::findOrFail($personId);

    //     if ($authUser->isFollowing($targetUser->id)) {
    //         // Unfollow
    //         Follow::where([
    //             'follower_id' => $authUser->id,
    //             'following_id' => $targetUser->id,
    //         ])->delete();

    //         $authUser->decrement('following');
    //         $targetUser->decrement('followers');
    //     } else {
    //         // Follow
    //         $authUser->followingRelation()->create([
    //             'following_id' => $targetUser->id,
    //         ]);

    //         $authUser->increment('following');
    //         $targetUser->increment('followers');

    //         // Optional: notify
    //         $targetUser->notify(new \App\Notifications\GeneralNotification([
    //             'title' => $authUser->name . ' followed you',
    //             'message' => $authUser->name . ' followed you',
    //             'icon' => 'fa-user-plus text-primary',
    //             'url' => url('profile/' . $authUser->username),
    //         ]));
    //     }

    //     // Reset pagination to refresh lists
    //     $this->resetPage();
    // }



    public function render()
    {
        

        $user = User::where('username', $this->username)->first();
    
        $connections = $this->activeTab === 'followers'
        ? $user->followers()->paginate(12)
        : $user->following()->paginate(12);


        // if ($this->activeTab === 'followers') {
        //     $connections = $user
        //         ->followersRelation()
        //         ->with('followers')
        //         ->latest()
        //         ->paginate(20);
        // } else {
        //     $connections = $user
        //         ->followingRelation()
        //         ->with('following')
        //         ->latest()
        //         ->paginate(20);
        // }


        return view('livewire.user.profile-connections', ['connections' => $connections, 'user' => $user]);
    }
}
