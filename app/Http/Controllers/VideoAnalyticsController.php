<?php

namespace App\Http\Controllers;

use App\Models\PostVideo;
use Illuminate\Http\Request;

class VideoAnalyticsController extends Controller
{
    

    public function trackWatchTime(Request $request, PostVideo $video)
    {
        $validated = $request->validate([
            'watch_time' => 'required|numeric|min:0',
        ]);

        try {
            // Update video analytics
            $video->updateWatchTime($validated['watch_time']);

            return response()->json([
                'success' => true,
                'avg_watch_time' => $video->avg_watch_time
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to track watch time.', 'error' => $e->getMessage()], 500);
        }

        
    }


    public function recordPlay(Request $request, PostVideo $video)
    {
        try {

            if (!$request->user()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            $video->incrementPlays();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to record play.', 'error' => $e->getMessage()], 500);
        }
    }
}
