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

#[On('user.timeline')]
#[On('openVideoPlayer')]

class DashboardTimeline extends Component
{
    use WithFileUploads;

    public string $content = '';
    public array  $images  = [];

    public $video                    = null;
    public int $videoUploadProgress  = 0;
    public ?string $cloudinaryVideoUrl      = null;
    public ?string $cloudinaryVideoPublicId = null;
    public string $videoUploadStatus = '';
    public bool $showVideoUpload     = false;

    public Collection $posts;
    public int  $page    = 1;
    public bool $hasMore = true;

    public bool  $isVideoOpen   = false;
    public ?int  $activeVideoId = null;

    // ─── Boot ────────────────────────────────────────────────────────────────────
    public function mount(): void
    {
        $this->posts = collect();
        $this->loadPosts();
    }

    // ─── Feed ────────────────────────────────────────────────────────────────────
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
        if (! $this->hasMore) return;
        $this->page++;
        $this->loadPosts();
    }

    // ─── Video Player ─────────────────────────────────────────────────────────────
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

    // ─── Create Post ─────────────────────────────────────────────────────────────
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

        // Duplicate check
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

    // ─── Remove image (called from Alpine via Livewire.dispatch) ─────────────────
    #[On('removeImageAt')]
    public function removeImageAt(int $index): void
    {
        if (isset($this->images[$index])) {
            unset($this->images[$index]);
            $this->images = array_values($this->images);
        }
    }

    // Also keep the direct wire:click version for backwards compat
    public function removeImage(int $index): void
    {
        $this->removeImageAt($index);
    }

    // ─── Video Upload ─────────────────────────────────────────────────────────────
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

    public function updatedVideo(): void
    {
        if (! $this->video) return;

        $level   = userLevel();
        $maxKB   = $level === 'Creator' ? 25600 : 102400;

        $this->validate([
            'video' => "required|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/webm|max:{$maxKB}",
        ]);

        $this->initiateVideoUpload();
    }

    protected function initiateVideoUpload(): void
    {
        $level = userLevel();

        if (! in_array($level, ['Creator', 'Influencer'])) {
            $this->videoUploadStatus = 'error';
            return;
        }

        $maxSeconds = $level === 'Creator' ? 20 : 80;

        $this->videoUploadStatus   = 'uploading';
        $this->videoUploadProgress = 10;
        $this->dispatch('videoUploadStatus', status: 'uploading', progress: 10);

        try {
            $result = cloudinary()->uploadVideo($this->video->getRealPath(), [
                'folder'         => 'payhankey_videos',
                'resource_type'  => 'video',
                'transformation' => [['duration' => $maxSeconds]],
                'eager'          => [
                    ['format' => 'mp4', 'quality' => 'auto:low',  'width' => 480,  'crop' => 'scale'],
                    ['format' => 'mp4', 'quality' => 'auto:good', 'width' => 720,  'crop' => 'scale'],
                    ['format' => 'mp4', 'quality' => 'auto:best', 'width' => 1080, 'crop' => 'scale'],
                ],
                'eager_async' => true,
            ]);

            $this->cloudinaryVideoUrl      = $result->getSecurePath();
            $this->cloudinaryVideoPublicId = $result->getPublicId();
            $this->videoUploadProgress     = 100;
            $this->videoUploadStatus       = 'done';
            $this->dispatch('videoUploadStatus', status: 'done', progress: 100);

        } catch (\Exception $e) {
            $this->videoUploadStatus   = 'error';
            $this->videoUploadProgress = 0;
            $this->dispatch('videoUploadStatus', status: 'error', progress: 0);
            session()->flash('error', 'Video upload failed: ' . $e->getMessage());
        }
    }

    public function publishVideo(): void
    {
        $level = userLevel();

        if (! in_array($level, ['Creator', 'Influencer'])) {
            session()->flash('error', 'Permission denied.');
            return;
        }

        $this->validate([
            'content'            => 'required|string',
            'cloudinaryVideoUrl' => 'required|url',
        ]);

        if ($this->videoUploadStatus !== 'done') {
            session()->flash('error', 'Please wait for the video to finish uploading.');
            return;
        }

        $user = Auth::user();

        $post = Post::create([
            'user_id'          => $user->id,
            'content'          => $this->convertUrlsToLinks($this->content),
            'unicode'          => rand(1000, 9999) . time(),
            'comment_external' => 0,
            'status'           => $user->status === 'ACTIVE' ? 'LIVE' : 'SHADOW_BANNED',
            'post_type'        => 'video',
        ]);

        PostVideo::create([
            'user_id'   => $user->id,
            'post_id'   => $post->id,
            'path'      => $this->cloudinaryVideoUrl,
            'public_id' => $this->cloudinaryVideoPublicId,
        ]);

        session()->flash('success', 'Video posted!');
        $this->reset('content');
        $this->resetVideoState();
        $this->showVideoUpload = false;
        $this->dispatch('postPublished');
        $this->loadPosts();
    }

    #[On('cancelVideoUpload')]
    public function cancelVideoUpload(): void
    {
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
        $this->video                   = null;
        $this->videoUploadProgress     = 0;
        $this->cloudinaryVideoUrl      = null;
        $this->cloudinaryVideoPublicId = null;
        $this->videoUploadStatus       = '';
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
