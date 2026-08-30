<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\CommentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessCommentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public string $postId,
        public string $userId,
        public string $message,
    ) {}

    public function handle(CommentService $commentService): void
    {
        $user = User::find($this->userId);

        if (! $user) {
            return;
        }

        try {
            $commentService->addComment($this->postId, $user, $this->message);
        } catch (\Throwable $e) {
            Log::error('ProcessCommentJob failed', [
                'post_id' => $this->postId,
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
