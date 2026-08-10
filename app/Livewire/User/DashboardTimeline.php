<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\Post;
use App\Models\PostImages;
use App\Models\PostVideo;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Services\VideoUploadService;

#[On('user.timeline')]
#[On('openVideoPlayer')]

class DashboardTimeline extends Component
{
    use WithFileUploads;

    // ── Composer ──────────────────────────────────────────────
    public string $content = '';
    public array  $images  = [];

    // ── Video ─────────────────────────────────────────────────
    public $video = null;

    /**
     * Status lifecycle:
     *   ''          → nothing selected
     *   'ready'     → file staged in Livewire temp, validated OK, awaiting Cloudinary push
     *   'uploading' → Cloudinary push in progress
     *   'done'      → Cloudinary finished, URL stored
     *   'error'     → something failed
     */
    public string  $videoUploadStatus       = '';
    public int     $videoUploadProgress     = 0;
    public ?string $cloudinaryVideoUrl      = null;
    public ?string $cloudinaryVideoPublicId = null;
    public ?string $cloudinaryThumbnailUrl  = null;

    // Metadata extracted from Cloudinary response
    public ?int    $videoDuration   = null;   // seconds
    public ?int    $videoWidth      = null;   // pixels
    public ?int    $videoHeight     = null;   // pixels
    public ?string $videoFormat     = null;   // e.g. "mp4"
    public ?int    $videoFileSize   = null;   // bytes
    public array   $videoQualityVersions = []; // adaptive URLs keyed by quality

    // ── Feed ──────────────────────────────────────────────────
    public Collection $posts;
    public int  $page    = 1;
    public bool $hasMore = true;

    public bool  $isVideoOpen   = false;
    public ?int  $activeVideoId = null;

    // ─────────────────────────────────────────────────────────
    public function mount(): void
    {
        $this->posts = collect();
        $this->loadPosts();
    }

    // ── Feed ─────────────────────────────────────────────────
    public function loadPosts(): void
    {
        $allPosts = Post::with(['user', 'images', 'video'])
            ->where('status', 'LIVE')
            ->latest('created_at')
            ->take($this->perPage() * $this->page * 2)
            ->get();

        $grouped     = $allPosts->groupBy('user_id');
        $interleaved = collect();
        $index       = 0;

        do {
            $added = 0;
            foreach ($grouped as $userPosts) {
                if (isset($userPosts[$index])) {
                    $interleaved->push($userPosts[$index]);
                    $added++;
                }
            }
            $index++;
        } while ($added > 0 && $interleaved->count() < $this->perPage() * $this->page);

        $this->posts   = $interleaved->take($this->perPage() * $this->page);
        $this->hasMore = $allPosts->count() > $this->posts->count();
    }

    protected function perPage(): int { return 20; }

    public function loadNextPage(): void
    {
        if (!$this->hasMore) return;
        $this->page++;
        $this->loadPosts();
    }

    // ── Video Player ──────────────────────────────────────────
    public function openVideoPlayer(int $videoId): void
    {
        $this->activeVideoId = $videoId;
        $this->isVideoOpen   = true;
    }

    public function closeVideoPlayer(): void
    {
        $this->isVideoOpen   = false;
        $this->activeVideoId = null;
    }

    // ── Create Post ───────────────────────────────────────────
    public function createPost(): void
    {
        $level = userLevel();

        $rules = ['content' => 'required|string'];

        if (!in_array($level, ['Creator', 'Influencer'])) {
            $rules['content'] .= '|max:160';
        } else {
            $rules['images']   = 'nullable|array|max:4';
            $rules['images.*'] = 'image|max:2048';
        }

        $this->validate($rules);

        $maxImages = match ($level) {
            'Creator'    => 1,
            'Influencer' => 4,
            default      => 0,
        };

        if ($maxImages === 0 && count($this->images) > 0) {
            session()->flash('error', 'Your account cannot upload images.');
            return;
        }

        if (count($this->images) > $maxImages) {
            session()->flash('error', "Max {$maxImages} image(s) allowed.");
            return;
        }

        $user    = Auth::user();
        $content = $this->convertUrlsToLinks($this->content);

        $existing = Post::where('user_id', $user->id)->pluck('content')->toArray();
        if (isSimilar($content, $existing, 4)) {
            session()->flash('info', 'This content is too similar to an existing post.');
            $this->reset('content');
            return;
        }

        $post = Post::create([
            'user_id'          => $user->id,
            'content'          => $content,
            'unicode'          => rand(1000, 9999) . time(),
            'comment_external' => 0,
            'status'           => $user->status === 'ACTIVE' ? 'LIVE' : 'SHADOW_BANNED',
        ]);

        foreach ($this->images as $image) {
            $url = cloudinary()->upload($image->getRealPath(), [
                'folder' => 'payhankey_post_images',
            ])->getSecurePath();

            PostImages::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
                'path'    => $url,
            ]);
        }

        session()->flash('success', 'Post created!');
        $this->reset('content', 'images');
        $this->dispatch('postPublished');
        $this->loadPosts();
    }

    // ── Image helpers ─────────────────────────────────────────
    #[On('removeImageAt')]
    public function removeImageAt(int $index): void
    {
        if (isset($this->images[$index])) {
            unset($this->images[$index]);
            $this->images = array_values($this->images);
        }
    }

    public function removeImage(int $index): void
    {
        $this->removeImageAt($index);
    }

    // ─────────────────────────────────────────────────────────
    // STEP 1 — File selected → validate only, NO Cloudinary yet
    //
    // wire:model uploads the file to Livewire's local temp storage
    // (chunked, fast). updatedVideo() fires when that completes.
    // We ONLY validate here. Cloudinary upload is triggered by a
    // separate button so it runs in its own HTTP request with no
    // interference from the upload request.
    // ─────────────────────────────────────────────────────────
    public function updatedVideo(): void
    {
        if (!$this->video) return;

        $level = userLevel();
        $maxKB = match ($level) {
            'Creator'    => 162400,   // 200 MB
            'Influencer' => 202400,  // 200 MB
            default      => 0,
        };

        if ($maxKB === 0) {
            $this->videoUploadStatus = 'error';
            $this->addError('video', 'Your account level cannot upload videos.');
            return;
        }

        // Validate file type and size only
        $valid = $this->validate([
            'video' => "required|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/webm|max:{$maxKB}",
        ]);

        if ($valid) {
            // File is good — mark as ready, wait for user to click Upload
            $this->videoUploadStatus = 'ready';
            // Tell Alpine to show the Upload button
            $this->dispatch('videoUploadStatus', status: 'ready', progress: 0);
        }
    }

    // ─────────────────────────────────────────────────────────
    // STEP 2 — User clicks "Upload to Cloudinary"
    //
    // This runs in its own separate Livewire request.
    // set_time_limit(0) removes the PHP execution timeout so
    // Cloudinary has as long as it needs.
    // ─────────────────────────────────────────────────────────
    public function uploadToCloudinary(): void
    {
        if (! $this->video || $this->videoUploadStatus !== 'ready') {
            return;
        }

        $level = userLevel();

        if (! in_array($level, ['Creator', 'Influencer'])) {
            $this->videoUploadStatus = 'error';
            session()->flash('error', 'Permission denied.');
            return;
        }

        $this->videoUploadStatus = 'uploading';
        $this->videoUploadProgress = 10;
        $this->dispatch('videoUploadStatus', status: 'uploading', progress: 10);

        try {
            $result = app(VideoUploadService::class)->upload(
                $this->video->getRealPath(),
                $level
            );

            $this->cloudinaryVideoUrl = $result['url'];
            $this->cloudinaryVideoPublicId = $result['public_id'];
            $this->cloudinaryThumbnailUrl = $result['thumbnail'];
            $this->videoDuration = $result['duration'];
            $this->videoWidth = $result['width'];
            $this->videoHeight = $result['height'];
            $this->videoFormat = $result['format'];
            $this->videoFileSize = $result['file_size'];
            $this->videoQualityVersions = $result['quality_versions'];

            $this->videoUploadProgress = 100;
            $this->videoUploadStatus = 'done';
            $this->dispatch('videoUploadStatus', status: 'done', progress: 100);
        } catch (\Exception $e) {
            $this->videoUploadStatus = 'error';
            $this->videoUploadProgress = 0;
            $this->dispatch('videoUploadStatus', status: 'error', progress: 0);
            session()->flash('error', 'Video upload failed: '.$e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────
    // STEP 3 — Publish the post (video already on Cloudinary)
    // ─────────────────────────────────────────────────────────
    public function publishVideo(): void
    {
        $level = userLevel();

        if (!in_array($level, ['Creator', 'Influencer'])) {
            session()->flash('error', 'Permission denied.');
            return;
        }

        if ($this->videoUploadStatus !== 'done' || !$this->cloudinaryVideoUrl) {
            session()->flash('error', 'Please upload your video first.');
            return;
        }

        $this->validate(['content' => 'required|string']);

        $user = Auth::user();

        $post = Post::create([
            'user_id'          => $user->id,
            'content'          => $this->convertUrlsToLinks($this->content),
            'unicode'          => rand(1000, 9999) . time(),
            'comment_external' => 0,
            'status'           => $user->status === 'ACTIVE' ? 'LIVE' : 'SHADOW_BANNED',
            'has_video' => true,
        ]);

        PostVideo::create([
            'user_id'            => $user->id,
            'post_id'            => $post->id,
            'path'               => $this->cloudinaryVideoUrl,
            'public_id'          => $this->cloudinaryVideoPublicId,
            'thumbnail_path'     => $this->cloudinaryThumbnailUrl,  // correct column name
            'duration'           => $this->videoDuration,
            'width'              => $this->videoWidth,
            'height'             => $this->videoHeight,
            'format'             => $this->videoFormat,
            'file_size'          => $this->videoFileSize,
            'quality_versions'   => $this->videoQualityVersions ?: null,
            'processing_status'  => 'completed',
        ]);

        session()->flash('success', 'Video posted!');
        $this->reset('content');
        $this->resetVideoState();
        $this->dispatch('postPublished');
        $this->loadPosts();
    }

    #[On('cancelVideoUpload')]
    public function cancelVideoUpload(): void
    {
        // Delete from Cloudinary only if fully uploaded
        if ($this->cloudinaryVideoPublicId) {
            try {
                app(VideoUploadService::class)->delete($this->cloudinaryVideoPublicId);
            } catch (\Exception) {}
        }
        $this->resetVideoState();
    }

    protected function resetVideoState(): void
    {
        $this->video                   = null;
        $this->videoUploadProgress     = 0;
        $this->cloudinaryVideoUrl      = null;
        $this->cloudinaryVideoPublicId = null;
        $this->cloudinaryThumbnailUrl  = null;
        $this->videoDuration           = null;
        $this->videoWidth              = null;
        $this->videoHeight             = null;
        $this->videoFormat             = null;
        $this->videoFileSize           = null;
        $this->videoQualityVersions    = [];
        $this->videoUploadStatus       = '';
    }

    /**
     * Extract cloud name from the Cloudinary DSN string.
     * e.g. "cloudinary://key:secret@my-cloud" → "my-cloud"
     */
    private function extractCloudName(string $dsn): ?string
    {
        preg_match('/cloudinary:\/\/[^:]+:[^@]+@([^\/\s]+)/', $dsn, $m);
        return $m[1] ?? null;
    }

    private function convertUrlsToLinks(string $text): string
    {
        return preg_replace(
            '/\b(?:https?:\/\/|www\.)\S+\b/',
            '<a href="$0" target="_blank" rel="noopener noreferrer">$0</a>',
            $text
        );
    }

    public function render()
    {
        return view('livewire.user.dashboard-timeline');
    }
}