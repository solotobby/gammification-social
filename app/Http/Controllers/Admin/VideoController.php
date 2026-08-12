<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PostVideo;
use App\Services\Admin\AdminVideoService;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function __construct(private AdminVideoService $videos) {}

    public function index(Request $request)
    {
        $search = $request->string('q')->trim()->toString() ?: null;
        $status = $request->string('status')->trim()->toString() ?: null;
        $filter = $request->string('filter')->trim()->toString() ?: null;

        return view('admin.videos.index', [
            'videos' => $this->videos->list($search, $status, $filter),
            'stats' => $this->videos->dashboardStats(),
            'search' => $search ?? '',
            'status' => $status ?? '',
            'filter' => $filter ?? '',
        ]);
    }

    public function show(PostVideo $video)
    {
        return view('admin.videos.show', [
            'video' => $this->videos->show($video),
        ]);
    }

    public function markFailed(Request $request, PostVideo $video)
    {
        $validated = $request->validate(['reason' => 'nullable|string|max:500']);
        $this->videos->markFailed($video, $validated['reason'] ?? null);

        return back()->with('success', 'Video marked as failed.');
    }

    public function markCompleted(Request $request, PostVideo $video)
    {
        $validated = $request->validate(['reason' => 'nullable|string|max:500']);
        $this->videos->markCompleted($video, $validated['reason'] ?? null);

        return back()->with('success', 'Video marked as completed.');
    }

    public function hide(Request $request, PostVideo $video)
    {
        $validated = $request->validate(['reason' => 'nullable|string|max:500']);
        $this->videos->hidePost($video, $validated['reason'] ?? null);

        return back()->with('success', 'Video post hidden from feeds and rolls.');
    }

    public function destroy(Request $request, PostVideo $video)
    {
        $validated = $request->validate(['reason' => 'nullable|string|max:500']);
        $this->videos->deletePost($video, $validated['reason'] ?? null);

        return redirect()
            ->route('admin.videos.index')
            ->with('success', 'Video post deleted and monetization removed.');
    }
}
