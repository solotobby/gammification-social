<?php

namespace App\Support;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Broadcast;

class SafeBroadcaster
{
    public static function emit(ShouldBroadcast $event, bool $toOthers = false): void
    {
        if (config('broadcasting.default', 'null') === 'null') {
            return;
        }

        try {
            $pending = broadcast($event);

            if ($toOthers) {
                $pending->toOthers();
            }

            // PendingBroadcast dispatches in __destruct(); unset so failures stay in this try block.
            unset($pending);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
