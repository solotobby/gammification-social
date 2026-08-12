<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostReport;
use App\Services\Admin\AdminPostReportService;
use Illuminate\Http\Request;

class PostReportController extends Controller
{
    public function __construct(private AdminPostReportService $reports) {}

    public function index(Request $request)
    {
        $search = $request->string('q')->trim()->toString() ?: null;

        return view('admin.reports.index', [
            'posts' => $this->reports->listQueue($search),
            'stats' => $this->reports->dashboardStats(),
            'search' => $search ?? '',
        ]);
    }

    public function show(Post $post)
    {
        return view('admin.reports.show', $this->reports->show($post));
    }

    public function dismiss(Request $request, PostReport $report)
    {
        $validated = $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        $this->reports->dismissReport($report, $validated['note'] ?? null);

        return back()->with('success', 'Report dismissed.');
    }

    public function dismissAll(Request $request, Post $post)
    {
        $validated = $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        $count = $this->reports->dismissAllForPost($post, $validated['note'] ?? null);

        return back()->with('success', $count
            ? "Dismissed {$count} report(s)."
            : 'No pending reports to dismiss.');
    }

    public function hidePost(Request $request, Post $post)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $this->reports->hidePost($post, $validated['reason'] ?? null);

        return back()->with('success', 'Post hidden and reports resolved.');
    }

    public function destroyPost(Request $request, Post $post)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $this->reports->deletePost($post, $validated['reason'] ?? null);

        return redirect()
            ->route('admin.reports.index')
            ->with('success', 'Post deleted. Related monetization and reports were removed.');
    }

    public function actionAuthor(Request $request, Post $post)
    {
        $validated = $request->validate([
            'status' => 'required|in:ACTIVE,SHADOW_BANNED,BLOCKED',
            'reason' => 'nullable|string|max:500',
            'hide_post' => 'nullable|boolean',
        ]);

        $hidePost = $request->boolean('hide_post', true);

        $user = $this->reports->actionAuthor(
            $post,
            $validated['status'],
            $validated['reason'] ?? null,
            $hidePost
        );

        $label = str_replace('_', ' ', strtolower($user->status));

        return back()->with('success', "Author marked as {$label}. Pending reports resolved.");
    }
}
