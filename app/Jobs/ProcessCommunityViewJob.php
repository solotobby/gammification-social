<?php

namespace App\Jobs;

use App\Models\CommunityPost;
use App\Models\User;
use App\Services\CommunityPostEngagementService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessCommunityViewJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public string $communityPostId,
        public string $userId,
        public ?string $ipAddress = null,
    ) {}

    public function handle(CommunityPostEngagementService $engagementService): void
    {
        $user = User::find($this->userId);
        $post = CommunityPost::find($this->communityPostId);

        if (! $user || ! $post) {
            return;
        }

        try {
            $engagementService->recordView($post, $user, $this->ipAddress);
        } catch (\Throwable $e) {
            Log::error('ProcessCommunityViewJob failed', [
                'community_post_id' => $this->communityPostId,
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
