<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RollsWatchController extends Controller
{
    /**
     * POST /api/rolls/watch
     *
     * Called by navigator.sendBeacon() — the body is raw JSON.
     */
    public function store(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if (! $data) {
            $data = $request->all();
        }

        $postId = (string) ($data['post_id'] ?? '');
        $watchSecs = (float) ($data['watch_seconds'] ?? 0);
        $isFirstPlay = (bool) ($data['is_first_play'] ?? false);

        if ($postId === '' || ! Auth::check()) {
            return response()->json(['ok' => false], 401);
        }

        $video = Post::find($postId)?->video;
        if (! $video) {
            return response()->json(['ok' => false], 404);
        }

        // Prefer client-side recordPlay on activate; accept beacon fallback.
        if ($isFirstPlay) {
            $video->incrementPlays();
        }

        if ($watchSecs >= 0.25) {
            $video->updateWatchTime($watchSecs);
        }

        return response()->json(['ok' => true]);
    }
}
