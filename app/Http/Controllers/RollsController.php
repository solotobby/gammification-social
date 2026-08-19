<?php

namespace App\Http\Controllers;

use App\Models\PostVideo;
use App\Models\ViewsExternal;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RollsController extends Controller
{
    public function random()
    {
        $video = PostVideo::randomPlayable();

        if (! $video) {
            abort(404, 'No rolls available yet.');
        }

        return redirect()->route('rolls.show', ['video' => $video->id]);
    }

    /**
     * Public standalone watch page — no auth required.
     * One video only (not the swipe feed).
     */
    public function publicShow(PostVideo $video): View
    {
        $video->loadMissing([
            'post.user:id,name,username,avatar',
            'user:id,name,username,avatar',
        ]);

        if (
            $video->processing_status !== 'completed'
            || blank($video->path)
            || ! $video->post
            || $video->post->status !== 'LIVE'
        ) {
            abort(404, 'This roll is unavailable.');
        }

        $this->recordPublicView($video);

        $qualities = is_array($video->quality_versions) ? $video->quality_versions : [];
        $srcHigh = $qualities['high'] ?? $video->path;
        $srcMedium = $qualities['medium'] ?? $srcHigh;
        $srcLow = $qualities['low'] ?? $srcMedium;

        $creator = $video->post->user ?? $video->user;
        $caption = trim(strip_tags($video->post->content ?? ''));
        $shareUrl = route('rolls.public', ['video' => $video->id]);
        $level = $creator ? userLevel($creator->id) : 'Basic';

        return view('general.roll-watch', [
            'video' => $video,
            'post' => $video->post,
            'creator' => $creator,
            'caption' => $caption,
            'shareUrl' => $shareUrl,
            'level' => $level,
            'srcHigh' => $srcHigh,
            'srcMedium' => $srcMedium,
            'srcLow' => $srcLow,
            'poster' => $video->thumbnail_path,
            'isOwner' => Auth::check() && Auth::id() === ($video->post->user_id ?? null),
        ]);
    }

    private function recordPublicView(PostVideo $video): void
    {
        $post = $video->post;
        if (! $post) {
            return;
        }

        // Guests count as external views; signed-in users keep in-app analytics.
        if (Auth::check()) {
            return;
        }

        $post->increment('views_external');

        try {
            $location = ipLocation();
            $ip = $location['ip'] ?? request()->ip();
            $city = $location['city'] ?? null;

            $exists = ViewsExternal::query()
                ->where('post_id', $post->id)
                ->where('ip', $ip)
                ->when($city, fn ($q) => $q->where('city', $city))
                ->exists();

            if (! $exists) {
                ViewsExternal::create([
                    'post_id' => $post->id,
                    'ip' => $ip,
                    'city' => $city,
                ]);
            }
        } catch (\Throwable) {
            // Ignore geolocation failures — view counter already incremented.
        }
    }
}
