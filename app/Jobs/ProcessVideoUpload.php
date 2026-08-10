<?php

namespace App\Jobs;

use App\Services\VideoUploadService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProcessVideoUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600;

    public int $tries = 2;

    public function __construct(
        public string $jobId,
        public string $userId,
        public string $level,
    ) {}

    public function handle(VideoUploadService $videos): void
    {
        $cacheKey = $this->cacheKey();

        try {
            Cache::put($cacheKey, [
                'status'   => 'processing',
                'progress' => 25,
            ], config('media.upload_cache_ttl', 3600));

            $result = $videos->processStaged($this->jobId, $this->level, $this->userId);

            Cache::put($cacheKey, [
                'status'   => 'completed',
                'progress' => 100,
                'result'   => $result,
            ], config('media.upload_cache_ttl', 3600));
        } catch (\Throwable $e) {
            Log::error('ProcessVideoUpload failed', [
                'job_id' => $this->jobId,
                'user_id' => $this->userId,
                'message' => $e->getMessage(),
            ]);

            Cache::put($cacheKey, [
                'status'   => 'failed',
                'progress' => 0,
                'error'    => $e->getMessage(),
            ], config('media.upload_cache_ttl', 3600));

            throw $e;
        }
    }

    public function cacheKey(): string
    {
        return "video_upload:{$this->jobId}";
    }
}
