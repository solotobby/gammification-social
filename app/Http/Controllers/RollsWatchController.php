<?php
// ═══════════════════════════════════════════════════════════════
// app/Http/Controllers/Api/RollsWatchController.php
//
// Handles the navigator.sendBeacon() call fired when the user
// closes the tab or navigates away while a video is playing.
// This is the ONLY way to reliably capture watch time on exit.
// ═══════════════════════════════════════════════════════════════

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
     * sendBeacon does NOT send cookies by default on some browsers,
     * so we accept the CSRF token in the body as a fallback.
     */
    public function store(Request $request)
    {
        // sendBeacon sends raw JSON body, not form data
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            $data = $request->all();
        }

        $postId      = (int)   ($data['post_id']       ?? 0);
        $watchSecs   = (float) ($data['watch_seconds'] ?? 0);
        $isFirstPlay = (bool)  ($data['is_first_play'] ?? false);

        if (!$postId || !Auth::check()) {
            return response()->json(['ok' => false], 401);
        }

        $video = Post::find($postId)?->video;
        if (!$video) {
            return response()->json(['ok' => false], 404);
        }

        if ($isFirstPlay) {
            $video->increment('play_count');
        }

        if ($watchSecs > 0.5) {
            $video->updateWatchTime($watchSecs);
        }

        return response()->json(['ok' => true]);
    }
}


// ═══════════════════════════════════════════════════════════════
// ROUTES — add these to your routes files
// ═══════════════════════════════════════════════════════════════

/*
─── routes/web.php ───────────────────────────────────────────────
use App\Livewire\User\Rolls;

Route::middleware('auth')->group(function () {
    Route::get('rolls/{video}', Rolls::class)->name('rolls.show');
});


─── routes/api.php ───────────────────────────────────────────────
use App\Http\Controllers\Api\RollsWatchController;

// Uses session auth (same session as web) so sendBeacon works
Route::middleware('web')->group(function () {
    Route::post('rolls/watch', [RollsWatchController::class, 'store'])
         ->name('api.rolls.watch');
});
*/


// ═══════════════════════════════════════════════════════════════
// MAKE SURE YOUR POST MODEL HAS THESE RELATIONSHIPS
// app/Models/Post.php
// ═══════════════════════════════════════════════════════════════

/*
public function video(): \Illuminate\Database\Eloquent\Relations\HasOne
{
    return $this->hasOne(\App\Models\PostVideo::class);
}

public function likes(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(\App\Models\UserLike::class);
}

public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(\App\Models\UserComment::class);
}

public function views(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(\App\Models\UserView::class);
}
*/


// ═══════════════════════════════════════════════════════════════
// ADD csrf-token META TAG to layouts/rolls.blade.php <head>
// ═══════════════════════════════════════════════════════════════

/*
<meta name="csrf-token" content="{{ csrf_token() }}">
*/