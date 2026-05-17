<?php

namespace App\Livewire\User;

use App\Models\Post;
use App\Models\PostImages;
use App\Models\PostVideo;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Component;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

#[On('user.timeline')]
#[On('openVideoPlayer')]

class NewTimeline extends Component
{
    use WithFileUploads, WithPagination;

    // ─── Text & Image ───────────────────────────────────────────────────────────
    public string $content = '';
    public array  $images  = [];

    // ─── Video Upload ────────────────────────────────────────────────────────────
    /**
     * Temporary uploaded video file (Livewire TemporaryUploadedFile).
     * Only used for the "select file" phase – actual upload is done via
     * initiateVideoUpload() which calls Cloudinary directly.
     */
    public $video = null;

    /** Progress 0-100 shown in the UI while Cloudinary is uploading */
    public int $videoUploadProgress = 0;

    /** Cloudinary secure URL after a successful upload */
    public ?string $cloudinaryVideoUrl = null;

    /** Cloudinary public_id (needed for transformations / deletion) */
    public ?string $cloudinaryVideoPublicId = null;

    /** Human-readable upload status message */
    public string $videoUploadStatus = '';

    /** Whether the video upload panel is open */
    public bool $showVideoUpload = false;

    // ─── Feed pagination ─────────────────────────────────────────────────────────
    public Collection $posts;
    public int  $page    = 1;
    public bool $hasMore = true;

    // ─── Video player state ──────────────────────────────────────────────────────
    public bool  $isVideoOpen   = false;
    public ?int  $activeVideoId = null;

    // ─── Validation rules ────────────────────────────────────────────────────────
    protected function videoRules(): array
    {
        $level   = userLevel();
        $maxSecs = match ($level) {
            'Creator'    => 20,
            'Influencer' => 80,
            default      => 0,
        };
        // 20 s @ ~10 Mbps ≈ 25 MB | 80 s ≈ 100 MB
        $maxKB = $maxSecs === 20 ? 25600 : 102400;

        return [
            'video' => "required|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/webm|max:{$maxKB}",
        ];
    }

    // ─── Lifecycle ───────────────────────────────────────────────────────────────
    public function mount(): void
    {
        $this->posts = collect();
        $this->loadPosts();
    }

    // ─── Feed Loading ─────────────────────────────────────────────────────────────
    public function loadPosts(): void
    {
        $allPosts = Post::with(['user', 'images', 'video'])
            ->where('status', 'LIVE')
            ->latest('created_at')
            ->take($this->perPage() * $this->page * 2)
            ->get();

        $grouped    = $allPosts->groupBy('user_id');
        $interleaved = collect();
        $index      = 0;

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
        if (! $this->hasMore) return;
        $this->page++;
        $this->loadPosts();
    }

    // ─── Video Player ─────────────────────────────────────────────────────────────
    public function openVideoPlayer(int $videoId): void
    {
        $this->activeVideoId = $videoId;
        $this->isVideoOpen   = true;
        $this->dispatch('openVideoPlayer', videoId: $videoId)
             ->to(\App\Livewire\User\VideoPlayer::class);
    }

    public function closeVideoPlayer(): void
    {
        $this->isVideoOpen   = false;
        $this->activeVideoId = null;
    }

    // ─── Text Post ───────────────────────────────────────────────────────────────
    public function createPost(): void
    {
        $level = userLevel();

        $rules = ['content' => 'required|string'];
        if (! in_array($level, ['Creator', 'Influencer'])) {
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

        if ($maxImages === 0 && count($this->images)) {
            session()->flash('error', 'You are not allowed to upload images.');
            return;
        }

        if (count($this->images) > $maxImages) {
            session()->flash('error', "You can upload a maximum of {$maxImages} image(s).");
            return;
        }

        $user    = Auth::user();
        $content = $this->convertUrlsToLinks($this->content);
        $existing = Post::where('user_id', $user->id)->pluck('content')->toArray();

        if (isSimilar($content, $existing, 4)) {
            session()->flash('info', 'This content is too similar to existing content.');
            $this->reset('content');
            return;
        }

        $status = $user->status === 'ACTIVE' ? 'LIVE' : 'SHADOW_BANNED';

        $post = Post::create([
            'user_id'          => $user->id,
            'content'          => $content,
            'unicode'          => rand(1000, 9999) . time(),
            'comment_external' => 0,
            'status'           => $status,
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

        session()->flash('success', 'Your post was successful!');
        $this->reset('content', 'images');
        $this->loadPosts();
    }

    // ─── Video Upload (Async via Cloudinary) ─────────────────────────────────────

    /**
     * Called from the blade via wire:click to toggle the video panel.
     */
    public function toggleVideoUpload(): void
    {
        $level = userLevel();
        if (! in_array($level, ['Creator', 'Influencer'])) {
            session()->flash('error', 'Only Creators and Influencers can upload videos.');
            return;
        }
        $this->showVideoUpload = ! $this->showVideoUpload;
        $this->resetVideoState();
    }

    /**
     * Livewire calls this automatically after the user selects a file.
     * We validate the local temp file, then kick off the Cloudinary upload.
     */
    public function updatedVideo(): void
    {
        $this->validate($this->videoRules());

        // Start the async upload (runs synchronously in PHP but is isolated here
        // so the UI can show "uploading…" state and we can return early on error).
        $this->initiateVideoUpload();
    }

    protected function initiateVideoUpload(): void
    {
        $level = userLevel();

        if (! in_array($level, ['Creator', 'Influencer'])) {
            $this->videoUploadStatus = 'error';
            session()->flash('error', 'Permission denied.');
            return;
        }

        $maxSeconds = match ($level) {
            'Creator'    => 20,
            'Influencer' => 80,
        };

        $this->videoUploadStatus   = 'uploading';
        $this->videoUploadProgress = 10;

        try {
            // Cloudinary upload with eager transformations for adaptive streaming
            $result = cloudinary()->uploadVideo($this->video->getRealPath(), [
                'folder'               => 'payhankey_videos',
                'resource_type'        => 'video',
                // Adaptive bitrate: create multiple quality versions
                'eager'                => [
                    // Low quality (mobile / slow network)
                    ['format' => 'mp4', 'quality' => 'auto:low',  'width' => 480,  'crop' => 'scale'],
                    // Medium quality
                    ['format' => 'mp4', 'quality' => 'auto:good', 'width' => 720,  'crop' => 'scale'],
                    // High quality (fast network / WiFi)
                    ['format' => 'mp4', 'quality' => 'auto:best', 'width' => 1080, 'crop' => 'scale'],
                    // Poster thumbnail
                    ['format' => 'jpg', 'quality' => 'auto',      'width' => 480,  'crop' => 'scale'],
                ],
                'eager_async'          => true,   // generate eager versions in background
                'eager_notification_url' => url('/cloudinary/webhook'), // optional webhook
                // Hard-cap duration server-side (Cloudinary incoming transformation)
                'transformation'       => [
                    ['duration' => $maxSeconds],
                ],
                // Auto-generate a waveform thumbnail as poster
                'notification_url'     => url('/cloudinary/webhook'),
            ]);

            $this->videoUploadProgress   = 90;
            $this->cloudinaryVideoUrl    = $result->getSecurePath();
            $this->cloudinaryVideoPublicId = $result->getPublicId();
            $this->videoUploadProgress   = 100;
            $this->videoUploadStatus     = 'done';

        } catch (\Exception $e) {
            $this->videoUploadStatus = 'error';
            $this->videoUploadProgress = 0;
            session()->flash('error', 'Video upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Publishes the video as a post once upload is confirmed.
     */
    public function publishVideo(): void
    {
        $level = userLevel();

        if (! in_array($level, ['Creator', 'Influencer'])) {
            session()->flash('error', 'Permission denied.');
            return;
        }

        $this->validate([
            'content'             => 'required|string',
            'cloudinaryVideoUrl'  => 'required|url',
        ]);

        if ($this->videoUploadStatus !== 'done') {
            session()->flash('error', 'Please wait for the video upload to complete.');
            return;
        }

        $user    = Auth::user();
        $content = $this->convertUrlsToLinks($this->content);
        $status  = $user->status === 'ACTIVE' ? 'LIVE' : 'SHADOW_BANNED';

        $post = Post::create([
            'user_id'          => $user->id,
            'content'          => $content,
            'unicode'          => rand(1000, 9999) . time(),
            'comment_external' => 0,
            'status'           => $status,
            'post_type'        => 'video',
        ]);

        // Store video metadata
        PostVideo::create([
            'user_id'   => $user->id,
            'post_id'   => $post->id,
            'path'      => $this->cloudinaryVideoUrl,
            'public_id' => $this->cloudinaryVideoPublicId,
        ]);

        session()->flash('success', 'Your video was posted successfully!');
        $this->reset('content');
        $this->resetVideoState();
        $this->showVideoUpload = false;
        $this->loadPosts();
    }

    public function cancelVideoUpload(): void
    {
        // Optionally delete from Cloudinary if already uploaded
        if ($this->cloudinaryVideoPublicId) {
            try {
                cloudinary()->destroy($this->cloudinaryVideoPublicId, ['resource_type' => 'video']);
            } catch (\Exception) {}
        }
        $this->resetVideoState();
        $this->showVideoUpload = false;
    }

    protected function resetVideoState(): void
    {
        $this->video                 = null;
        $this->videoUploadProgress   = 0;
        $this->cloudinaryVideoUrl    = null;
        $this->cloudinaryVideoPublicId = null;
        $this->videoUploadStatus     = '';
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────────
    public function removeImage(int $index): void
    {
        if (isset($this->images[$index])) {
            unset($this->images[$index]);
            $this->images = array_values($this->images);
        }
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
        return view('livewire.user.timeline')->layout('layouts.app');
    }
}