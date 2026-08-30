<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\ViewService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessViewJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public string $postId,
        public string $userId,
    ) {}

    public function handle(ViewService $viewService): void
    {
        $post = Post::find($this->postId);

        if (! $post) {
            return;
        }

        try {
            $viewService->recordView($post, $this->userId);
        } catch (\Throwable $e) {
            Log::error('ProcessViewJob failed', [
                'post_id' => $this->postId,
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
