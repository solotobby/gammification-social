<?php

namespace App\Livewire\User;

use App\Models\Follow;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProfileConnections extends Component
{
    use WithPagination;

    public string $username;

    #[Url(history: true, as: 'tab')]
    public string $activeTab = 'followers';

    public int $perPage = 20;

    public function mount(string $username): void
    {
        $this->username = $username;

        if (! in_array($this->activeTab, ['followers', 'following'], true)) {
            $this->activeTab = 'followers';
        }
    }

    public function updatingActiveTab(): void
    {
        $this->resetPage();
    }

    public function switchTab(string $tab): void
    {
        if (! in_array($tab, ['followers', 'following'], true)) {
            return;
        }

        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function toggleFollow(string $userId): void
    {
        if (! Auth::check()) {
            return;
        }

        $authUser = Auth::user();

        if ($authUser->id === $userId) {
            return;
        }

        $targetUser = User::find($userId);

        if (! $targetUser) {
            return;
        }

        if ($authUser->isFollowing($targetUser)) {
            Follow::where([
                'follower_id' => $authUser->id,
                'following_id' => $targetUser->id,
            ])->delete();

            if ($authUser->following > 0) {
                $authUser->decrement('following');
            }

            if ($targetUser->followers > 0) {
                $targetUser->decrement('followers');
            }
        } else {
            $created = Follow::firstOrCreate([
                'follower_id' => $authUser->id,
                'following_id' => $targetUser->id,
            ]);

            if ($created->wasRecentlyCreated) {
                $authUser->increment('following');
                $targetUser->increment('followers');

                $targetUser->notify(new GeneralNotification([
                    'title' => displayName($authUser->name) . ' followed you',
                    'message' => displayName($authUser->name) . ' followed you',
                    'icon' => 'fa-user-plus text-primary',
                    'url' => url('profile/' . $authUser->username),
                ]));
            }
        }

        $this->clearUserFeedCache($authUser->id);
        $this->clearUserFeedCache($targetUser->id);
        $this->dispatch('refreshFeed');
    }

    private function clearUserFeedCache(string $userId): void
    {
        $indexKey = "feed:keys:user:{$userId}";
        $keys = Cache::get($indexKey, []);

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Cache::forget($indexKey);
    }

    public function render()
    {
        $user = User::where('username', $this->username)->firstOrFail();

        $relation = $this->activeTab === 'followers'
            ? $user->followers()
            : $user->following();

        $connections = $relation
            ->select(['users.id', 'users.name', 'users.username', 'users.avatar', 'users.followers', 'users.following'])
            ->with(['profile:id,user_id,about'])
            ->orderByPivot('created_at', 'desc')
            ->paginate($this->perPage);

        $followingIds = Auth::check()
            ? Follow::where('follower_id', Auth::id())->pluck('following_id')->flip()->all()
            : [];

        return view('livewire.user.profile-connections', [
            'connections' => $connections,
            'user' => $user,
            'followingIds' => $followingIds,
            'isOwner' => Auth::id() === $user->id,
        ]);
    }
}
