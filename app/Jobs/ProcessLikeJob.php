<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\LikeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessLikeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    public string $unicode;
    public string $userId;


    public function __construct($unicode, $userId)
    {
        $this->unicode = $unicode;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(LikeService $likeService): void
    {
        $user = User::find($this->userId);

        if(!$user){
            return;
        }

        $likeService->toggle($this->unicode, $user);
    }
}
