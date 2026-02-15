<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\PostImages;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessMediaUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $postId,
        public string $path,
        public string $type
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $options = [
            'folder' => 'payhankey/posts',
            'resource_type' => $this->type,
        ];

        if ($this->type === 'video') {
            $options += [
                'chunk_size' => 6000000,
                'eager_async' => true,
                'eager' => [
                    ['format'=>'mp4','quality'=>'auto','width'=>720],
                    ['format'=>'mp4','quality'=>'auto','width'=>1080],
                ],
            ];
        }

          $upload = cloudinary()->upload($this->path, $options);



          PostImages::create([
            'post_id' => $this->postId,
            'type'    => $this->type,
            'path'    => $upload->getSecurePath(),
            'status'  => 'READY',
        ]);

        Post::where('id',$this->postId)
            ->update(['status'=>'LIVE']);

    }
}
