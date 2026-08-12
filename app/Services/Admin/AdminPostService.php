<?php

namespace App\Services\Admin;

use App\Models\Post;
use App\Services\AdminAuditService;
use App\Services\PostDeletionService;
use App\Services\PostEarningsService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminPostService
{
    public function __construct(
        protected AdminAuditService $audit,
        protected PostDeletionService $deletion,
        protected PostEarningsService $earnings,
    ) {}

    public function dashboardStats(): array
    {
        return [
            'total' => Post::query()->count(),
            'live' => Post::query()->where('status', 'LIVE')->count(),
            'hidden' => Post::query()->where('status', 'HIDDEN')->count(),
            'shadow' => Post::query()->where('status', 'SHADOW_BANNED')->count(),
            'reported' => Post::query()->whereHas('reports', fn ($q) => $q->pending())->count(),
        ];
    }

    public function list(?string $search = null, ?string $status = null, ?string $media = null, bool $reportedOnly = false): LengthAwarePaginator
    {
        $query = Post::query()
            ->with(['user:id,name,username,email,avatar,status'])
            ->withCount(['postComments as comment_rows', 'images', 'reports'])
            ->withExists('video')
            ->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhere('unicode', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($user) use ($search) {
                        $user->where('username', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($media === 'images') {
            $query->where('has_images', true);
        } elseif ($media === 'video') {
            $query->where('has_video', true);
        } elseif ($media === 'text') {
            $query->where('has_images', false)->where('has_video', false);
        }

        if ($reportedOnly) {
            $query->whereHas('reports', fn ($q) => $q->pending());
        }

        return $query->paginate(20)->withQueryString();
    }

    public function show(Post $post): Post
    {
        return $post->load([
            'user:id,name,username,email,avatar,status',
            'images',
            'video',
            'trends:id,name',
            'reports' => fn ($q) => $q->with('user:id,name,username,email')->latest()->limit(25),
        ])->loadCount(['postComments', 'reports']);
    }

    public function estimatedEarnings(Post $post): float
    {
        return (float) ($this->earnings->forPosts(collect([$post->id]))[$post->id] ?? 0);
    }

    public function hide(Post $post, ?string $reason = null): Post
    {
        $previous = $post->status;

        $post->update(['status' => 'HIDDEN']);

        $this->audit->log('post.hidden', $post, [
            'previous_status' => $previous,
            'reason' => $reason,
        ]);

        return $post->fresh();
    }

    public function unhide(Post $post): Post
    {
        $previous = $post->status;
        $ownerStatus = $post->user?->status;
        $restored = $ownerStatus === 'SHADOW_BANNED' ? 'SHADOW_BANNED' : 'LIVE';

        $post->update(['status' => $restored]);

        $this->audit->log('post.unhidden', $post, [
            'previous_status' => $previous,
            'restored_status' => $restored,
        ]);

        return $post->fresh();
    }

    public function delete(Post $post, ?string $reason = null): array
    {
        $meta = [
            'post_id' => $post->id,
            'owner_id' => $post->user_id,
            'status' => $post->status,
            'reason' => $reason,
            'excerpt' => mb_substr(strip_tags((string) $post->content), 0, 160),
        ];

        $result = $this->deletion->delete($post);

        $this->audit->log('post.deleted', null, array_merge($meta, $result));

        return $result;
    }
}
