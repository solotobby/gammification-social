<?php

namespace App\Livewire\User;

use App\Models\AccessCode;
use App\Models\Post;
use App\Models\PostImages;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserLevel;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use Livewire\Component;

class Posts extends Component
{
    use WithFileUploads;


    public $perpage = 10;

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

    public $convertedAmount;


    // public function mount(){
    //     $user = UserLevel::where('user_id', auth()->user()->id)->first();

    //     dd($user);
    // }

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


    public function loadMore()
    {
        $this->perpage += 10;
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
        } else {
            $uniqueCode = rand(1000, 9999) . time();
            $timelines = Post::create(['user_id' => auth()->user()->id, 'content' => $content, 'unicode' => $uniqueCode, 'status' => 'LIVE']);

            foreach ($this->images as $image) {
                $path = $image->store('post_images', 'public');

                PostImages::create(['user_id' => Auth::id(), 'post_id' => $timelines->id, 'path' => $path]);

                // $post->images()->create([
                //     'path' => $path,
                // ]);
            }

            $this->reset('content', 'images');
        }
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

        $post = Post::where('unicode', $postId)->first();

        if ($post->isLikedBy(Auth::user())) {
            $post->likes()->where('user_id', Auth::id())->delete();
            $post->decrement('likes');
        } else {
            // if (auth()->user()->id != $post->user_id) {
            $post->likes()->create(['user_id' => Auth::id(), 'is_paid' => false, 'amount' => calculateUniqueEarningPerLike(), 'poster_user_id' => $post->user_id]);
            $post->increment('likes');
            // }
        }

        // $this->timelines();

        // $this->dispatch('user.timeline');
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

    public function render()
    {

        $posts = Post::take($this->perpage)
            ->where('status', 'LIVE')
            ->orderBy('created_at', 'desc')
            ->get();
        // Group posts by user_id
        $groupedPosts = $posts->groupBy('user_id');

        // Flatten the grouped collection in an interleaved manner
        $interleavedPosts = new Collection();
        while ($groupedPosts->isNotEmpty()) {
            foreach ($groupedPosts as $userId => $userPosts) {
                if ($userPosts->isNotEmpty()) {
                    $interleavedPosts->push($userPosts->shift());
                    if ($userPosts->isEmpty()) {
                        $groupedPosts->forget($userId);
                    }
                }
            }
        }

        return view('livewire.user.posts', ['posts' => $interleavedPosts]);
    }
}
