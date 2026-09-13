<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\Admin\AdminPostService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct(private AdminPostService $posts) {}

    public function index(Request $request)
    {
        $search = $request->string('q')->trim()->toString() ?: null;
        $status = $request->string('status')->trim()->toString() ?: null;
        $media = $request->string('media')->trim()->toString() ?: null;
        $monetization = $request->string('monetization')->trim()->toString() ?: null;
        $reportedOnly = $request->boolean('reported');

        return view('admin.posts.index', [
            'posts' => $this->posts->list($search, $status, $media, $reportedOnly, $monetization),
            'stats' => $this->posts->dashboardStats(),
            'search' => $search ?? '',
            'status' => $status ?? '',
            'media' => $media ?? '',
            'monetization' => $monetization ?? '',
            'reportedOnly' => $reportedOnly,
        ]);
    }

    public function show(Post $post)
    {
        $post = $this->posts->show($post);

        return view('admin.posts.show', [
            'post' => $post,
            'estimatedEarnings' => $this->posts->estimatedEarnings($post),
        ]);
    }

    public function updateMonetization(Request $request, Post $post)
    {
        $validated = $request->validate([
            'is_monetized' => 'required|boolean',
            'monetization_note' => 'nullable|string|max:255',
        ]);

        $this->posts->updateMonetization(
            $post,
            (bool) $validated['is_monetized'],
            $validated['monetization_note'] ?? null
        );

        $statusLabel = $validated['is_monetized'] ? 'eligible for monetization' : 'disqualified from monetization';

        return back()->with('success', "Post marked as {$statusLabel}.");
    }

    public function reEvaluateQuality(Post $post)
    {
        $updated = $this->posts->reEvaluateQuality($post);

        $statusLabel = $updated->is_monetized ? 'eligible for monetization' : 'disqualified from monetization';

        return back()->with('success', "Post quality re-evaluated: now {$statusLabel}.");
    }

    public function hide(Request $request, Post $post)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $this->posts->hide($post, $validated['reason'] ?? null);

        return back()->with('success', 'Post hidden from public feeds.');
    }

    public function unhide(Post $post)
    {
        $this->posts->unhide($post);

        return back()->with('success', 'Post restored to the feed.');
    }

    public function destroy(Request $request, Post $post)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $this->posts->delete($post, $validated['reason'] ?? null);

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Post deleted. Related monetization was removed.');
    }
}
