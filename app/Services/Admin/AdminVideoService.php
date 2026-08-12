<?php

namespace App\Services\Admin;

use App\Models\PostVideo;
use App\Services\AdminAuditService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminVideoService
{
    public function __construct(
        protected AdminAuditService $audit,
        protected AdminPostService $posts,
    ) {}

    public function dashboardStats(): array
    {
        $stuckBefore = now()->subHours(6);

        return [
            'total' => PostVideo::query()->count(),
            'completed' => PostVideo::query()->where('processing_status', 'completed')->count(),
            'processing' => PostVideo::query()->where('processing_status', 'processing')->count(),
            'failed' => PostVideo::query()->where('processing_status', 'failed')->count(),
            'stuck' => PostVideo::query()
                ->where('processing_status', 'processing')
                ->where('updated_at', '<', $stuckBefore)
                ->count(),
            'anomalies' => $this->anomalyQuery()->count(),
        ];
    }

    public function list(?string $search = null, ?string $status = null, ?string $filter = null): LengthAwarePaginator
    {
        $query = PostVideo::query()
            ->with([
                'user:id,name,username,email,avatar,status',
                'post:id,user_id,content,status,created_at,has_video',
            ])
            ->latest();

        if ($status) {
            $query->where('processing_status', $status);
        }

        if ($filter === 'stuck') {
            $query->where('processing_status', 'processing')
                ->where('updated_at', '<', now()->subHours(6));
        } elseif ($filter === 'anomalies') {
            $query = $this->anomalyQuery($query);
        } elseif ($filter === 'failed') {
            $query->where('processing_status', 'failed');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhere('public_id', 'like', "%{$search}%")
                    ->orWhere('post_id', $search)
                    ->orWhereHas('user', function ($user) use ($search) {
                        $user->where('username', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    });
            });
        }

        return $query->paginate(20)->withQueryString();
    }

    public function show(PostVideo $video): PostVideo
    {
        return $video->load([
            'user:id,name,username,email,avatar,status',
            'post.images',
            'post.user:id,name,username,email,status',
        ]);
    }

    public function markFailed(PostVideo $video, ?string $reason = null): PostVideo
    {
        $previous = $video->processing_status;
        $video->update(['processing_status' => 'failed']);

        $this->audit->log('video.marked_failed', $video->post, [
            'video_id' => $video->id,
            'previous_status' => $previous,
            'reason' => $reason,
        ]);

        return $video->fresh();
    }

    public function markCompleted(PostVideo $video, ?string $reason = null): PostVideo
    {
        $previous = $video->processing_status;
        $video->update(['processing_status' => 'completed']);

        $this->audit->log('video.marked_completed', $video->post, [
            'video_id' => $video->id,
            'previous_status' => $previous,
            'reason' => $reason,
        ]);

        return $video->fresh();
    }

    public function hidePost(PostVideo $video, ?string $reason = null): void
    {
        if (! $video->post) {
            return;
        }

        $this->posts->hide($video->post, $reason ?: 'Video takedown');
        $this->audit->log('video.takedown_hide', $video->post, [
            'video_id' => $video->id,
            'reason' => $reason,
        ]);
    }

    public function deletePost(PostVideo $video, ?string $reason = null): array
    {
        if (! $video->post) {
            return [];
        }

        $result = $this->posts->delete($video->post, $reason ?: 'Video takedown delete');

        $this->audit->log('video.takedown_delete', null, array_merge($result, [
            'video_id' => $video->id,
            'reason' => $reason,
        ]));

        return $result;
    }

    protected function anomalyQuery($query = null)
    {
        $query ??= PostVideo::query();
        $stuckBefore = now()->subHours(6);

        return $query->where(function ($q) use ($stuckBefore) {
            $q->where('processing_status', 'failed')
                ->orWhere(function ($stuck) use ($stuckBefore) {
                    $stuck->where('processing_status', 'processing')
                        ->where('updated_at', '<', $stuckBefore);
                })
                ->orWhere(function ($missingPath) {
                    $missingPath->where('processing_status', 'completed')
                        ->where(function ($path) {
                            $path->whereNull('path')->orWhere('path', '');
                        });
                })
                ->orWhere(function ($hiddenLiveVideo) {
                    $hiddenLiveVideo->where('processing_status', 'completed')
                        ->whereHas('post', fn ($post) => $post->whereIn('status', ['HIDDEN', 'SHADOW_BANNED']));
                })
                ->orWhere(function ($highPlaysFailed) {
                    $highPlaysFailed->where('processing_status', 'failed')
                        ->where('play_count', '>', 0);
                });
        });
    }
}
