<?php

namespace App\Livewire\User;


use App\Models\Timeline as ModelsTimeline;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use App\Models\AccessCode;
use App\Models\Comment;
use App\Models\Post;
use App\Models\PostImages;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserComment;
use App\Models\UserLevel;
use App\Models\UserLike;
use App\Models\UserView;
use App\Models\Wallet;
use App\Notifications\GeneralNotification;
// use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

#[On('user.timeline')]
class Timeline extends Component
{
    use WithFileUploads, WithPagination;

    public $postId;
    #[Validate('required|string')]
    public $content = '';

    public $images = [];
    public $imagePreviews = [];

    protected $rules = [
        'content' => 'required|string',
        'images.*' => 'nullable|image|max:1024', // 1MB Max per image
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

    // protected $listeners = [
    //     'refreshFeed' => 'clearFeedCache',
    // ];

    protected $listeners = [
        'loadMorePosts' => '$refresh',
    ];

    // public $posts; 
    // public $buffer = [];    // preloaded next batch
    // public $perPage = 3;   // batch size
    // public $page = 1;       // current page
    // public $loadingNext = false;

    public Collection $posts;
    public Collection $buffer;

    public int $perPage = 10;          // batch size
    public ?string $cursor = null;    // cursor for pagination
    public bool $loadingNext = false;

    public int $page = 1;
    public bool $hasMore = true;


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
        // Step 1: get latest posts per user (interleaving)
        $query = Post::with('user')
            ->where('status', 'LIVE')
            ->latest('created_at');

        // Fetch more than perPage to allow interleaving
        $allPosts = $query->take($this->perPage * $this->page * 2)
                          ->get();

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

     // Core batch query using cursor
    // protected function fetchBatch(?string $cursor = null)
    // {
    //     return Post::with('user')
    //         ->where('status', 'LIVE')
    //         ->when($cursor, fn ($q) => $q->where('created_at', '<', $cursor))
    //         ->orderByDesc('created_at')
    //         ->limit($this->perPage)
    //         ->get();
    // }

    // public function loadInitial()
    // {
    //     $batch = $this->fetchBatch();

    //     if ($batch->isEmpty()) {
    //         $this->hasMore = false;
    //         return;
    //     }

    //     $this->posts = $batch;
    //     $this->cursor = $batch->last()->created_at;
    // }

    // // Preload next batch for smooth scroll
    // public function preloadNext()
    // {
    //     if (! $this->hasMore) return;

    //     $this->buffer = $this->fetchBatch($this->cursor);

    //     if ($this->buffer->isEmpty()) {
    //         $this->hasMore = false;
    //     }
    // }

    // // Load the preloaded batch
    // public function loadNextBatch()
    // {
    //     if ($this->loadingNext || $this->buffer->isEmpty()) return;

    //     $this->loadingNext = true;

    //     $this->posts = $this->posts->concat($this->buffer);

    //     $this->cursor = $this->buffer->last()->created_at;

    //     $this->buffer = collect();
    //     $this->preloadNext();

    //     $this->loadingNext = false;
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

        $content = $this->convertUrlsToLinks($this->content);
        $getContent = Post::where(['user_id' => $user->id])->pluck('content')->toArray();

        // dd($content);

        if (isSimilar($content, $getContent, 4)) {
            session()->flash('info', 'This content is too similar to existing content, therefore it will not be posted.');
            $this->reset('content');
            return;
        }

        $status = 'LIVE';
        if($user->status != 'ACTIVE'){
            $status = 'SHADOW_BANNED';
        }

        // $check = isSpam($content);
        // if($check){
        //     $status = 'SHADOW_BANNED';
        // }

        // dd($status);

        $uniqueCode = rand(1000, 9999) . time();
        $timelines = Post::create(['user_id' => $user->id, 'content' => $content, 'unicode' => $uniqueCode, 'comment_external' => 0, 'status' => $status]);

        if (!empty($this->images)) {
            foreach ($this->images as $image) {
                $uploadedFileUrl = cloudinary()->upload($image->getRealPath(), [
                    'folder' => 'payhankey_post_images',
                ])->getSecurePath();

                PostImages::create([
                    'user_id' => Auth::id(),
                    'post_id' => $timelines->id,
                    'path' => $uploadedFileUrl,
                ]);
            }
        }
         session()->flash('success', 'Your post was successful!');

        $this->reset('content', 'images');

        

        // Refresh feed
        // $this->resetPage();
    }

    private function checkWriteUp(){

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

    public function updatedImages()
    {
        $this->validate([
            'images.*' => 'image|max:1024', // 1MB Max per image
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


    public function loadMore()
    {
        $this->dispatch('loadMorePosts');
    }

    public function render()
    {
        return view('livewire.user.timeline')->layout('layouts.app');
    }
}
