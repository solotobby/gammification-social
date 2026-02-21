<?php


namespace App\Services;

use App\Models\PostVideo;

use Illuminate\Support\Facades\Cache;

class VideoPlayerService
{
    public static function getVideosFromPoint($videoId, $context = 'global', $userId=null){

        $startVideo = PostVideo::with(['post.user', 'user'])
        ->where('id', $videoId)
            ->where('processing_status', 'completed')
            ->first();

        if (!$startVideo) {
            return [];
        }

        // Build query based on context
        $query = PostVideo::with(['post.user', 'user'])
            ->where('processing_status', 'completed');

        // Apply context-specific filters
        switch ($context) {
            case 'user':
                // Videos from same user
                $query->where('user_id', $startVideo->user_id);
                break;
            
            case 'following':
                // Videos from users that current user follows
                if (auth()->check()) {
                    $followingIds = auth()->user()->following()->pluck('following_id')->toArray();
                    $query->whereIn('user_id', $followingIds);
                }
                break;
            
            case 'trending':
                // Trending videos (most viewed in last 7 days)
                $query->where('created_at', '>=', now()->subDays(7))
                      ->orderBy('view_count', 'desc');
                break;
            
            case 'global':
            default:
                // All videos
                $query->latest();
                break;
        }

        // Get videos including and after the starting video
        $allVideos = $query->latest()->get();

        // Find the index of the starting video
        $startIndex = $allVideos->search(function ($video) use ($videoId) {
            return $video->id == $videoId;
        });

        // Reorder to start from the clicked video
        if ($startIndex !== false) {
            $before = $allVideos->slice(0, $startIndex);
            $after = $allVideos->slice($startIndex);
            $allVideos = $after->concat($before);
        }

        return $allVideos->take(50)->toArray(); // Limit to 50 videos initially

    }

    public static function loadMoreVideos($lastVideoId, $context = 'global', $userId = null){
        $query = PostVideo::with(['post.user', 'user'])
            ->where('processing_status', 'completed')
            ->where('id', '<', $lastVideoId);

        // Apply context filters
        switch ($context) {
            case 'user':
                if ($userId) {
                    $query->where('user_id', $userId);
                }
                break;
            
            case 'following':
                if (auth()->check()) {
                    $followingIds = auth()->user()->following()->pluck('following_id')->toArray();
                    $query->whereIn('user_id', $followingIds);
                }
                break;
            
            case 'trending':
                $query->where('created_at', '>=', now()->subDays(7))
                      ->orderBy('view_count', 'desc');
                break;
        }

        return $query->latest()
            ->limit(10)
            ->get()
            ->toArray();

    }
}