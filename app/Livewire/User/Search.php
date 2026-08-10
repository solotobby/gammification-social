<?php

namespace App\Livewire\User;

use App\Models\Follow;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;

class Search extends Component
{
    use WithPagination;

    public string $query = '';

    protected $queryString = [
        'query' => ['except' => ''],
    ];

    public function updatingQuery(): void
    {
        $this->resetPage();
    }

    public function clearSearch(): void
    {
        $this->query = '';
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
        $trimmed = trim($this->query);
        $followingIds = [];
        $users = null;

        if (strlen($trimmed) >= 2) {
            $users = User::query()
                ->where('status', 'ACTIVE')
                ->where('id', '!=', Auth::id())
                ->where(function ($q) use ($trimmed) {
                    $q->where('name', 'like', "%{$trimmed}%")
                        ->orWhere('username', 'like', "%{$trimmed}%");
                })
                ->orderByRaw(
                    'CASE WHEN username LIKE ? THEN 0 WHEN name LIKE ? THEN 1 ELSE 2 END',
                    ["{$trimmed}%", "{$trimmed}%"]
                )
                ->orderByDesc('followers')
                ->paginate(20);

            if (Auth::check() && $users->isNotEmpty()) {
                $followingIds = Follow::query()
                    ->where('follower_id', Auth::id())
                    ->whereIn('following_id', $users->pluck('id'))
                    ->pluck('following_id')
                    ->all();
            }
        }

        return view('livewire.user.search', [
            'users' => $users,
            'followingIds' => $followingIds,
            'trimmedQuery' => $trimmed,
        ]);
    }
}
