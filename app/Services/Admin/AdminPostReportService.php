<?php

namespace App\Services\Admin;

use App\Models\Post;
use App\Models\PostReport;
use App\Models\User;
use App\Services\AdminAuditService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class AdminPostReportService
{
    public function __construct(
        protected AdminAuditService $audit,
        protected AdminPostService $posts,
        protected AdminUserService $users,
    ) {}

    public function dashboardStats(): array
    {
        return [
            'pending_reports' => PostReport::query()->pending()->count(),
            'pending_posts' => Post::query()->whereHas('reports', fn ($q) => $q->pending())->count(),
            'resolved_today' => PostReport::query()
                ->where('status', PostReport::STATUS_RESOLVED)
                ->whereDate('reviewed_at', today())
                ->count(),
            'total_resolved' => PostReport::query()->where('status', PostReport::STATUS_RESOLVED)->count(),
        ];
    }

    public function listQueue(?string $search = null): LengthAwarePaginator
    {
        $query = Post::query()
            ->with(['user:id,name,username,email,avatar,status'])
            ->withCount([
                'reports as pending_reports_count' => fn ($q) => $q->pending(),
                'reports as total_reports_count',
            ])
            ->withMax([
                'reports as latest_report_at' => fn ($q) => $q->pending(),
            ], 'created_at')
            ->whereHas('reports', fn ($q) => $q->pending())
            ->orderByDesc('latest_report_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($user) use ($search) {
                        $user->where('username', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('reports.user', function ($reporter) use ($search) {
                        $reporter->where('username', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        return $query->paginate(20)->withQueryString();
    }

    public function show(Post $post): array
    {
        $post = $this->posts->show($post);

        return [
            'post' => $post,
            'pendingReports' => $post->reports()
                ->pending()
                ->with('user:id,name,username,email,avatar')
                ->latest()
                ->get(),
            'resolvedReports' => $post->reports()
                ->where('status', PostReport::STATUS_RESOLVED)
                ->with(['user:id,name,username,email', 'reviewer:id,name,username'])
                ->latest('reviewed_at')
                ->limit(20)
                ->get(),
            'estimatedEarnings' => $this->posts->estimatedEarnings($post),
        ];
    }

    public function dismissReport(PostReport $report, ?string $note = null): void
    {
        if (! $report->isPending()) {
            return;
        }

        $this->resolveReports(collect([$report]), 'dismissed', $note);

        $this->audit->log('post_report.dismissed', $report->post, [
            'report_id' => $report->id,
            'note' => $note,
        ]);
    }

    public function dismissAllForPost(Post $post, ?string $note = null): int
    {
        $reports = $post->reports()->pending()->get();

        if ($reports->isEmpty()) {
            return 0;
        }

        $this->resolveReports($reports, 'dismissed', $note);

        $this->audit->log('post_report.dismissed_all', $post, [
            'count' => $reports->count(),
            'note' => $note,
        ]);

        return $reports->count();
    }

    public function hidePost(Post $post, ?string $reason = null): void
    {
        $this->posts->hide($post, $reason);
        $count = $this->resolvePendingForPost($post, 'post_hidden', $reason);

        $this->audit->log('post_report.resolved_hide', $post, [
            'reports_resolved' => $count,
            'reason' => $reason,
        ]);
    }

    public function deletePost(Post $post, ?string $reason = null): array
    {
        $count = $post->reports()->pending()->count();
        $result = $this->posts->delete($post, $reason);

        $this->audit->log('post_report.resolved_delete', null, [
            'post_id' => $result['post_id'] ?? null,
            'reports_pending_at_delete' => $count,
            'reason' => $reason,
        ]);

        return $result;
    }

    public function actionAuthor(Post $post, string $status, ?string $reason = null, bool $hidePost = true): User
    {
        if (! in_array($status, ['ACTIVE', 'SHADOW_BANNED', 'BLOCKED'], true)) {
            throw new \InvalidArgumentException('Invalid author status.');
        }

        $user = $this->users->changeStatus($post->user_id, $status);

        if ($hidePost && $status !== 'ACTIVE' && $post->status !== 'HIDDEN') {
            $this->posts->hide($post, $reason ?: 'Author '.$status);
        }

        $resolution = match ($status) {
            'SHADOW_BANNED' => 'author_shadow_banned',
            'BLOCKED' => 'author_blocked',
            default => 'author_restored',
        };

        $count = $this->resolvePendingForPost($post, $resolution, $reason);

        $this->audit->log('post_report.author_actioned', $post, [
            'author_id' => $user->id,
            'status' => $status,
            'reports_resolved' => $count,
            'reason' => $reason,
            'post_hidden' => $hidePost && $status !== 'ACTIVE',
        ]);

        return $user;
    }

    protected function resolvePendingForPost(Post $post, string $resolution, ?string $note = null): int
    {
        $reports = $post->reports()->pending()->get();

        if ($reports->isEmpty()) {
            return 0;
        }

        $this->resolveReports($reports, $resolution, $note);

        return $reports->count();
    }

    /**
     * @param  Collection<int, PostReport>  $reports
     */
    protected function resolveReports(Collection $reports, string $resolution, ?string $note = null): void
    {
        $now = now();
        $adminId = Auth::id();

        foreach ($reports as $report) {
            $report->update([
                'status' => PostReport::STATUS_RESOLVED,
                'resolution' => $resolution,
                'reviewed_by' => $adminId,
                'reviewed_at' => $now,
            ]);
        }
    }
}
