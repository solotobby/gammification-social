<?php

namespace App\Livewire\User;

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

class Posts extends Component
{
    use WithFileUploads;


    public $perPage = 25;

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

    protected $listeners = [
        'refreshFeed' => 'clearFeedCache',
    ];


    public function openEditModal($postId)
    {
        $post = Post::where('unicode', $postId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // dd($post);

        $this->editingPostId = $post->id;
        $this->content = $post->content;
    }

    public function editPost()
    {
        $maxLength = in_array(userLevel(), ['Creator', 'Influencer']) ? null : 160;

        if ($maxLength && strlen($this->content) > $maxLength) {
            session()->flash('error', "Maximum {$maxLength} characters allowed.");
            return;
        }

        Post::where('id', $this->editingPostId)
            ->where('user_id', auth()->id())
            ->update([
                'content' => $this->content,
            ]);

        session()->flash('success', 'Post updated successfully.');

        $this->reset('editingPostId', 'content');

        // Close modal
        $this->dispatch('closeEditModal');
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




    public function post()
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



        $content = $this->convertUrlsToLinks($this->content);
        $getContent = Post::where(['user_id' => auth()->user()->id])->pluck('content')->toArray();

        // dd($content);

        if (isSimilar($content, $getContent, 4)) {
            session()->flash('info', 'This content is too similar to existing content, therefore it will not be posted.');
            $this->reset('content');
            return;
        }
        $uniqueCode = rand(1000, 9999) . time();
        $timelines = Post::create(['user_id' => auth()->user()->id, 'content' => $content, 'unicode' => $uniqueCode, 'status' => 'LIVE']);

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

        // foreach ($this->images as $image) {
        //     $path = $image->store('post_images', 'public');

        //     PostImages::create(['user_id' => Auth::id(), 'post_id' => $timelines->id, 'path' => $path]);


        // }

        $this->reset('content', 'images');
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

    private function convertUrlsToLinks($text)
    {
        $pattern = '/\b(?:https?:\/\/|www\.)\S+\b/';
        $replacement = '<a href="$0" target="_blank" rel="noopener noreferrer">$0</a>';
        return preg_replace($pattern, $replacement, $text);
    }

    public function removeImage($index)
    {
        if (isset($this->images[$index])) {
            unset($this->images[$index]);

            // Reindex array so Livewire stays in sync
            $this->images = array_values($this->images);
        }
    }

    public function toggleLike($postId)
    {
        $user = Auth::user();

        $post = Post::with('user') // eager load user
            ->where('unicode', $postId)
            ->firstOrFail();

        DB::transaction(function () use ($post, $user) {
            $isSelfView = $user->id === $post->user_id;

            $like = $post->likes()->where('user_id', $user->id)->first();

            if ($like) {
                // Unlike
                $like->delete();
                $post->decrement('likes');
            } else {
                // Like
                $post->likes()->create([
                    'user_id' => $user->id,
                    'is_paid' => false,
                    'amount'  => calculateUniqueEarningPerLike(),
                    'poster_user_id' => $post->user_id,
                    'type' => $isSelfView ? 'self-like' : 'like',
                ]);

                $post->increment('likes');

                // Queue the notification for async processing
                $post->user->notify((new GeneralNotification([
                    'title'   => displayName($user->name) . ' liked your post',
                    'message' => displayName($user->name) . ' liked your post',
                    'icon'    => 'fa-thumbs-up text-primary',
                    'url'     => url('show/' . $post->id),
                ]))->delay(now()->addSeconds(1))); // small delay to decouple DB write
            }
        });
    }

    public function deletePost($postId)
    {

        $post = Post::where('unicode', $postId)->first();
        $post->delete();

        UserView::where('post_id', $post->id)->delete();
        UserComment::where('post_id', $post->id)->delete();
        UserLike::where('post_id', $post->id)->delete();

        session()->flash('success', "Post deleted");
    }



    public function verifyAccessCode()
    {

        $accessCode = AccessCode::where('code', $this->access_code)->where('is_active', false)->first();

        if ($accessCode) {

            $accessCode->is_active = true;
            $accessCode->save();

            $user = User::where('id', auth()->user()->id)->first();
            $user->level_id = $accessCode->level_id;
            $user->email_verified_at = now();
            $user->save();

            $amount = $accessCode->level->reg_bonus;


            $rate = $this->rates[$this->currency] ?? 1;
            $this->convertedAmount = $amount * $rate;


            $wallet = Wallet::where('user_id', auth()->user()->id)->first();
            $wallet->balance = $this->convertedAmount; //$accessCode->amount;
            $wallet->currency = $this->currency;
            $wallet->save();

            Transaction::create([
                'user_id' => auth()->user()->id,
                'ref' => time() . rand(999, 99999),
                'amount' => $this->convertedAmount,
                'currency' => $this->currency,
                'status' =>  'successful',
                'type' => 'reg_bonus',
                'action' => 'Credit',
                'description' => 'Payhankey Sign-up Bonus', //ucfirst($update->wallet_type).' withdrawal via '.ucwords($method), 
                'meta' => null,
                'customer' => null
            ]);


            session()->flash('success', 'Access Code Redeemed Successfully');

            return redirect('timeline');
        } else {
            session()->flash('error', 'Invalid Access Code');
            return redirect('timeline');
        }
    }

    public function commentFeed($currentPostId)
    {
        dd($currentPostId);
    }

    public function comment($currentPostId)
    {
        $this->validate([
            'message' => 'required|string|max:500',
        ]);

        if (!$this->message || !$currentPostId) {
            return;
        }

        dd($currentPostId);

        $user = Auth::user();


        Comment::create([
            'user_id' => $user->id,
            'post_id' => $this->currentPostId,
            'content' => $this->message,
            // 'is_paid' => false,
            // 'amount'  => calculateUniqueEarningPerComment(),
            // 'poster_user_id' => Post::where('id', $this->postId)->value('user_id'),
        ]);

        // Increment comment count
        Post::where('id', $this->currentPostId)->increment('comments');

        // Reset content
        $this->reset('content');
    }

    public function render()
    {

        // Use window function to rank posts per user
        $posts = Post::select('*')
            ->where('status', 'LIVE')
            ->selectRaw('ROW_NUMBER() OVER (PARTITION BY user_id ORDER BY created_at DESC) as row_num')
            ->orderBy('row_num')            // interleave by row number
            ->orderBy('created_at', 'desc') // newest posts first within same row_num
            ->limit($this->perPage * 2)     // fetch extra posts to ensure enough for interleaving
            // ->with(['user', 'postComments' => function ($query) {
            //     $query->latest()->take(2)->with('user'); // latest 2 comments with user
            // }])
            ->get();

        // Group by row number
        $groupedByRow = $posts->groupBy('row_num');

        // Flatten in interleaved order
        $interleavedPosts = new Collection();
        foreach ($groupedByRow as $rowGroup) {
            foreach ($rowGroup as $post) {
                $interleavedPosts->push($post);
            }
        }

        // Limit final output to perPage
        $interleavedPosts = $interleavedPosts->take($this->perPage);

        return view('livewire.user.posts', ['posts' => $interleavedPosts]);
    }



    private function getTimeline(): Collection
    {
        $user = Auth::user();

        $cacheKey = "feed:timeline:user:{$user->id}:{$this->perPage}";

        return Cache::remember($cacheKey, now()->addSeconds(30), function () use ($user, $cacheKey) {

            // Track this cache key
            $this->rememberUserCacheKey($user->id, $cacheKey);


            $priorityKey = "feed:priority-users:{$user->id}";

            // Track priority cache key
            $this->rememberUserCacheKey($user->id, $priorityKey);



            $posts = Post::where('posts.status', 'LIVE')
                ->with('user')
                ->leftJoin('follows', function ($join) use ($user) {
                    $join->on('posts.user_id', '=', 'follows.following_id')
                        ->where('follows.follower_id', $user->id);
                })
                ->select('posts.*')
                ->selectRaw('CASE WHEN follows.id IS NOT NULL THEN 1 ELSE 0 END as priority')
                ->orderByDesc('priority')
                ->orderByDesc('posts.created_at')
                ->take($this->perPage)
                ->get();


            $priorityPosts = $posts->where('priority', 1)->values();
            $otherPosts    = $posts->where('priority', 0)->values();

            return $this->interleaveByUser($priorityPosts)
                ->merge($this->interleaveByUser($otherPosts))
                ->take($this->perPage)
                ->values();
        });
    }


    private function rememberUserCacheKey(string $userId, string $key): void
    {
        $indexKey = "feed:keys:user:{$userId}";

        $keys = Cache::get($indexKey, []);

        if (!in_array($key, $keys)) {
            $keys[] = $key;
            Cache::put($indexKey, $keys, now()->addMinutes(10));
        }
    }



    private function clearUserFeedCache(string $userId): void
    {
        $indexKey = "feed:keys:user:{$userId}";
        $keys = Cache::get($indexKey, []);

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Cache::forget($indexKey);
    }


    // public function getTimeline(): Collection
    // {
    //     $user = Auth::user();
    //     $cacheKey = "feed:timeline:user:{$user->id}:{$this->perPage}";

    //     return Cache::remember($cacheKey, now()->addSeconds(30), function () use ($user) {

    //         /*
    //         |--------------------------------------------------------------------------
    //         | 1. Cache priority user IDs (followers + following)
    //         |--------------------------------------------------------------------------
    //         */
    //         $priorityUserIds = Cache::remember(
    //             "feed:priority-users:{$user->id}",
    //             now()->addMinutes(5),
    //             function () use ($user) {
    //                 return $user->following()
    //                     ->pluck('users.id')
    //                     ->merge($user->followers()->pluck('users.id'))
    //                     ->unique()
    //                     ->values();
    //             }
    //         );

    //         /*
    //         |--------------------------------------------------------------------------
    //         | 2. Fetch posts with priority flag
    //         |--------------------------------------------------------------------------
    //         */
    //         $posts = Post::where('status', 'LIVE')
    //             ->with('user') // prevent N+1
    //             ->select('posts.*')
    //             ->selectRaw(
    //                 'CASE WHEN user_id IN (?) THEN 1 ELSE 0 END as priority',
    //                 [$priorityUserIds]
    //             )
    //             ->orderByDesc('priority')
    //             ->orderByDesc('created_at')
    //             ->take($this->perPage * 2) // buffer for interleaving
    //             ->get();

    //         /*
    //         |--------------------------------------------------------------------------
    //         | 3. Split by priority
    //         |--------------------------------------------------------------------------
    //         */
    //         $priorityPosts = $posts->where('priority', 1)->values();
    //         $otherPosts    = $posts->where('priority', 0)->values();

    //         /*
    //         |--------------------------------------------------------------------------
    //         | 4. Interleave to prevent domination
    //         |--------------------------------------------------------------------------
    //         */
    //         return $this->interleaveByUser($priorityPosts)
    //             ->merge($this->interleaveByUser($otherPosts))
    //             ->take($this->perPage)
    //             ->values();
    //     });


    // }

    private function interleaveByUser(Collection $posts): Collection
    {
        $grouped = $posts->groupBy('user_id');
        $result = new Collection();

        while ($grouped->isNotEmpty()) {
            foreach ($grouped as $userId => $userPosts) {
                if ($userPosts->isNotEmpty()) {
                    $result->push($userPosts->shift());
                }

                if ($userPosts->isEmpty()) {
                    $grouped->forget($userId);
                }
            }
        }

        return $result;
    }

    public function clearFeedCache(): void
    {
        $userId = Auth::id();

        Cache::forget("feed:timeline:user:{$userId}:{$this->perPage}");
        Cache::forget("feed:priority-users:{$userId}");
    }

    public function loadMore(): void
    {
        $this->perPage += 25;
        $this->clearFeedCache();
    }
}
