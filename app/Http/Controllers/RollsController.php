<?php

namespace App\Http\Controllers;

use App\Models\PostVideo;

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
}
