<?php

namespace App\Livewire\User;


use App\Models\Timeline as ModelsTimeline;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use App\Models\AccessCode;
use App\Models\Comment;
use App\Models\Post;
use App\Models\PostImages;
use App\Models\PostTrend;
use App\Models\Transaction;
use App\Models\Trend;
use App\Models\User;
use App\Models\UserComment;
use App\Models\UserLevel;
use App\Models\UserLike;
use App\Models\UserView;
use App\Models\Wallet;
use App\Notifications\GeneralNotification;
use App\Services\HashtagService;
use App\Models\PostVideo;
use App\Services\PostEarningsService;
use App\Services\VideoUploadService;
// use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[On('user.timeline')]
#[On('openVideoPlayer')]

class Timeline extends Component
{
    use WithFileUploads, WithPagination;

    public $postId;
    #[Validate('required|string')]
    public $content = '';

    public $images = [];
    public $videos = [];
    public $imagePreviews = [];
    public array $selectedTrends = [];

    protected $rules = [
        'content' => 'required|string',
        'images.*' => 'nullable|image|max:2048',
    ];

    public $access_code = '';
    public $currency = '';

    protected $rates = [
        'USD' => 1,
        'NGN' => 1500,
        'EUR' => 0.92,
        'GBP' => 0.79,
    ];

    public $editingPostId = null;

    public $convertedAmount;
    public $currentPostId;
    public $message = [];

    protected $listeners = [
        'loadMorePosts' => '$refresh',
    ];

    public Collection $posts;
    public Collection $buffer;

    public int $perPage = 20;          // batch size
    public ?string $cursor = null;    // cursor for pagination
    public bool $loadingNext = false;

    public int $page = 1;
    public bool $hasMore = true;
    public $isVideoOpen = false;
    public $activeVideoId = null;

    // ── Video upload (Creator / Influencer) ───────────────────
    public $video = null;
    public string $videoUploadStatus = '';
    public int $videoUploadProgress = 0;
    public ?string $cloudinaryVideoUrl = null;
    public ?string $cloudinaryVideoPublicId = null;
    public ?string $cloudinaryThumbnailUrl = null;
    public ?int $videoDuration = null;
    public ?int $videoWidth = null;
    public ?int $videoHeight = null;
    public ?string $videoFormat = null;
    public ?int $videoFileSize = null;
    public array $videoQualityVersions = [];
    public bool $composerVideoMode = false;


    public function openVideoPlayer($videoId)
    {
        return redirect()->route('rolls.show', ['video' => $videoId]);
    }

    public function closeVideoPlayer()
    {
        $this->isVideoOpen = false;
        $this->activeVideoId = null;
    }


    public function mount()
    {
        // $this->loadPosts();
        // $this->preloadNext();

        $this->posts = collect();
        $this->loadPosts();

        // $this->buffer = collect();

        // $this->loadInitial();
        // $this->preloadNext();
    }



    public function loadPosts()
    {
        $userId = auth()->id();

        $query = Post::with(['user', 'trends', 'images', 'video'])
            ->withExists(['likes as liked_by_me' => fn ($q) => $q->where('user_id', $userId)])
            ->where('status', 'LIVE')
            ->latest('created_at');

        // Fetch more than perPage to allow interleaving
        $allPosts = $query->take($this->perPage * $this->page * 2)->get();

        // Step 2: group by user
        $grouped = $allPosts->groupBy('user_id');

        // Step 3: interleave posts: take first from each user, then second, etc.
        $interleaved = collect();
        $index = 0;

        do {
            $added = 0;
            foreach ($grouped as $userPosts) {
                if (isset($userPosts[$index])) {
                    $interleaved->push($userPosts[$index]);
                    $added++;
                }
            }
            $index++;
        } while ($added > 0 && $interleaved->count() < $this->perPage * $this->page);

        // Step 4: limit final posts
        $this->posts = $interleaved->take($this->perPage * $this->page);

        // Step 5: check if there are more posts
        $this->hasMore = $allPosts->count() > $this->posts->count();
    }

    public function loadNextPage()
    {
        if (!$this->hasMore) return;

        $this->page++;
        $this->loadPosts();
    }

    // public function addTrend(string $trendId): void
    // {
    //     if (!in_array($trendId, $this->selectedTrends)) {
    //         $this->selectedTrends[] = $trendId;
    //     }
    // }

    // public function removeTrend(string $trendId): void
    // {
    //     $this->selectedTrends = array_values(
    //         array_filter($this->selectedTrends, fn($id) => $id !== $trendId)
    //     );
    // }



    public function createPost()
    {
        $level = userLevel();
        

        $rules = [
            'content' => 'required|string',
        ];

        if (!in_array($level, ['Creator', 'Influencer'])) {
            $rules['content'] .= '|max:160';
            $rules['images'] = 'prohibited';
        } else {
            $rules['images'] = 'nullable|array|max:4';
            $rules['images.*'] = 'image|max:2048';
        }

        $this->validate($rules);

        // Determine max length
        $maxLength = in_array($level, ['Creator', 'Influencer']) ? null : 160;

        // if (count($this->selectedTrends) < 2) {
        //     session()->flash('error', 'Please select at least 2 trending topics for your post.');
        //     return;
        // }


        // Check content length for regular users
        if ($maxLength && strlen($this->content) > $maxLength) {
            session()->flash('error', "You cannot post more than $maxLength characters.");
            return;
        }


        $maxImages = match ($level) {
            'Creator' => 1,
            'Influencer' => 4,
            default => 0,
        };

        // Block image upload for normal users
        if ($maxImages === 0 && count($this->images) > 0) {
            session()->flash('error', 'You are not allowed to upload images.');
            return;
        }

        // Enforce image count
        if (count($this->images) > $maxImages) {
            session()->flash('error', "You can upload a maximum of {$maxImages} image(s).");
            return;
        }

        // Validate images
        if ($maxImages > 0) {
            $this->validate([
                'images.*' => 'image|max:2048', // 2MB per image
            ]);
        }

        $user = Auth::user();

        $content = $this->convertUrlsToLinks(strip_tags($this->content));
        $getContent = Post::where(['user_id' => $user->id])->pluck('content')->toArray();

        // dd($content);

        if (isSimilar($content, $getContent, 4)) {
            session()->flash('info', 'This content is too similar to existing content, therefore it will not be posted.');
            $this->reset('content');
            return;
        }

        $status = 'LIVE';
        if ($user->status != 'ACTIVE') {
            $status = 'SHADOW_BANNED';
        }

        $uniqueCode = rand(1000, 9999) . time();
        $timelines = Post::create(['user_id' => $user->id, 'content' => $content, 'unicode' => $uniqueCode, 'comment_external' => 0, 'status' => $status]);

        // dd([$timelines, $timelines->body]);
       app(HashtagService::class)->attach($timelines, $timelines->content);

     

        // foreach ($this->selectedTrends as $trendId) {  
        //     PostTrend::create([
        //             'post_id' => $timelines->id,
        //             'trend_id' => $trendId,
        //         ]); 
        //     // $timelines->trends()->attach($trendId);
        // }   

        if (!empty($this->images)) {
            foreach ($this->images as $image) {
                // $uploadedFileUrl = cloudinary()->upload($image->getRealPath(), [
                //     'folder' => 'payhankey_post_images',
                // ])->getSecurePath();


                $path = Storage::disk('spaces')->putFileAs(
                    'payhankey_media/images', // folder inside bucket
                    $image,
                    Str::uuid() . '-' . auth()->id(), // unique filename
                    'public'
                );
                $url = config('filesystems.disks.spaces.url') . '/' . $path;


                PostImages::create([
                    'user_id' => Auth::id(),
                    'post_id' => $timelines->id,
                    'path' => $url,
                ]);
            }
        }

        session()->flash('success', 'Your post was successful!');

        $this->reset('content', 'images');
        $this->page = 1;
        $this->loadPosts();
    }


    private function convertUrlsToLinks($text)
    {
        $pattern = '/\b(?:https?:\/\/|www\.)\S+\b/';
        $replacement = '<a href="$0" target="_blank" rel="noopener noreferrer">$0</a>';
        return preg_replace($pattern, $replacement, $text);
    }

    private function isSimilar($newData, $existingData, $threshold = 5)
    {
        $normalizedNewData = normalizeText($newData);

        foreach ($existingData as $data) {
            $normalizedData = normalizeText($data);
            $levenshteinDistance = levenshtein($normalizedNewData, $normalizedData);

            if ($levenshteinDistance <= $threshold) {
                return true;
            }
        }

        return false;
    }

    public function updatedImages(): void
    {
        if (! empty($this->images) && $this->composerVideoMode) {
            $this->cancelVideoUpload();
        }

        $this->validate([
            'images.*' => 'image|max:2048',
        ]);

        $this->imagePreviews = [];
        foreach ($this->images as $image) {
            $this->imagePreviews[] = $image->temporaryUrl();
        }
    }

    public function removeImage($index)
    {
        if (isset($this->images[$index])) {
            unset($this->images[$index]);

            // Reindex array so Livewire stays in sync
            $this->images = array_values($this->images);
        }
    }

    public function clearImages(): void
    {
        $this->reset('images');
    }

    // ── Video upload ──────────────────────────────────────────
    public function updatedVideo(): void
    {
        if (! $this->video) {
            return;
        }

        $level = userLevel();
        $service = app(VideoUploadService::class);
        $maxKb = $service->maxFileKb($level);

        if ($maxKb === 0) {
            $this->videoUploadStatus = 'error';
            $this->addError('video', 'Your account level cannot upload videos.');
            return;
        }

        $this->validate([
            'video' => 'required|file|mimetypes:'.$service->allowedMimetypes()."|max:{$maxKb}",
        ]);

        $this->composerVideoMode = true;
        $this->images = [];
        $this->uploadVideo();
    }

    public function uploadVideo(): void
    {
        if (! $this->video) {
            return;
        }

        $level = userLevel();
        if (! in_array($level, ['Creator', 'Influencer'])) {
            $this->videoUploadStatus = 'error';
            session()->flash('error', 'Permission denied.');
            return;
        }

        $this->videoUploadStatus = 'uploading';
        $this->videoUploadProgress = 15;
        $this->dispatch('videoUploadStatus', status: 'uploading', progress: 15);

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
        } catch (\Throwable $e) {
            $this->videoUploadStatus = 'error';
            $this->videoUploadProgress = 0;
            $this->dispatch('videoUploadStatus', status: 'error', progress: 0);
            session()->flash('error', 'Video upload failed: '.$e->getMessage());
        }
    }

    /** @deprecated alias */
    public function uploadToCloudinary(): void
    {
        $this->uploadVideo();
    }

    public function publishVideo(): void
    {
        $level = userLevel();

        if (! in_array($level, ['Creator', 'Influencer'])) {
            session()->flash('error', 'Permission denied.');
            return;
        }

        if ($this->videoUploadStatus !== 'done' || ! $this->cloudinaryVideoUrl) {
            session()->flash('error', 'Please wait for your video to finish processing.');
            return;
        }

        $this->validate(['content' => 'required|string']);

        $user = Auth::user();
        $content = $this->convertUrlsToLinks(strip_tags($this->content));

        $existing = Post::where('user_id', $user->id)->pluck('content')->toArray();
        if (isSimilar($content, $existing, 4)) {
            session()->flash('info', 'This content is too similar to an existing post.');
            return;
        }

        $post = Post::create([
            'user_id' => $user->id,
            'content' => $content,
            'unicode' => rand(1000, 9999).time(),
            'comment_external' => 0,
            'status' => $user->status === 'ACTIVE' ? 'LIVE' : 'SHADOW_BANNED',
            'has_video' => true,
        ]);

        PostVideo::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
            'path' => $this->cloudinaryVideoUrl,
            'public_id' => $this->cloudinaryVideoPublicId,
            'thumbnail_path' => $this->cloudinaryThumbnailUrl,
            'duration' => $this->videoDuration,
            'width' => $this->videoWidth,
            'height' => $this->videoHeight,
            'format' => $this->videoFormat,
            'file_size' => $this->videoFileSize,
            'quality_versions' => $this->videoQualityVersions ?: null,
            'processing_status' => 'completed',
        ]);

        app(HashtagService::class)->attach($post, $post->content);

        session()->flash('success', 'Your video was posted!');
        $this->reset('content');
        $this->resetVideoState();
        $this->page = 1;
        $this->loadPosts();
    }

    public function cancelVideoUpload(): void
    {
        if ($this->cloudinaryVideoPublicId) {
            try {
                app(VideoUploadService::class)->delete($this->cloudinaryVideoPublicId);
            } catch (\Exception $e) {
                // ignore cleanup failures
            }
        }
        $this->resetVideoState();
    }

    protected function resetVideoState(): void
    {
        $this->video = null;
        $this->composerVideoMode = false;
        $this->videoUploadProgress = 0;
        $this->cloudinaryVideoUrl = null;
        $this->cloudinaryVideoPublicId = null;
        $this->cloudinaryThumbnailUrl = null;
        $this->videoDuration = null;
        $this->videoWidth = null;
        $this->videoHeight = null;
        $this->videoFormat = null;
        $this->videoFileSize = null;
        $this->videoQualityVersions = [];
        $this->videoUploadStatus = '';
        $this->dispatch('videoUploadStatus', status: 'idle', progress: 0);
    }



    public function loadMore()
    {
        $this->dispatch('loadMorePosts');
    }

    public function render()
    {
        $earnings = app(PostEarningsService::class)->forPosts($this->posts->pluck('id'));

        return view('livewire.user.timeline', [
            'earnings' => $earnings,
        ])->layout('layouts.app');
    }


    // public function loadPosts()
    // {
    //     $this->posts = Post::with('user')
    //         ->latest()
    //         ->take($this->perPage * $this->page)
    //         ->get();


    //         // Use window function to rank posts per user
    //     $posts = Post::select('*')
    //         ->where('status', 'LIVE')
    //         ->selectRaw('ROW_NUMBER() OVER (PARTITION BY user_id ORDER BY created_at DESC) as row_num')
    //         ->orderBy('row_num')            // interleave by row number
    //         ->orderBy('created_at', 'desc') // newest posts first within same row_num
    //         ->limit($this->perPage *  $this->page)     // fetch extra posts to ensure enough for interleaving
    //         // ->with(['user', 'postComments' => function ($query) {
    //         //     $query->latest()->take(2)->with('user'); // latest 2 comments with user
    //         // }])
    //         ->get();

    //     // Group by row number
    //     $groupedByRow = $posts->groupBy('row_num');

    //     // Flatten in interleaved order
    //     $interleavedPosts = new Collection();
    //     foreach ($groupedByRow as $rowGroup) {
    //         foreach ($rowGroup as $post) {
    //             $interleavedPosts->push($post);
    //         }
    //     }

    //     // Limit final output to perPage
    //     $this->posts = $interleavedPosts->take($this->perPage);


    // }

    // public function preloadNext()
    // {
    //     $this->buffer = Post::with('user')
    //         ->latest()
    //         ->skip($this->perPage * $this->page)
    //         ->take($this->perPage)
    //         ->get();
    // }

    // public function loadNextBatch()
    // {
    //     if ($this->loadingNext || $this->buffer->isEmpty()) return;

    //     $this->loadingNext = true;

    //     // Append buffer to posts
    //     $this->posts = $this->posts->merge($this->buffer);

    //     // increment page
    //     $this->page++;

    //     // preload next batch
    //     $this->preloadNext();

    //     $this->loadingNext = false;
    // }



}
