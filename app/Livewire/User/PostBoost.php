<?php

namespace App\Livewire\User;

use App\Models\Post;
use App\Services\PostBoostService;
use Illuminate\Support\Str;
use Livewire\Component;

class PostBoost extends Component
{
    public Post $post;
    public string $errorMessage = '';
    public bool $isSuccess = false;
    public ?array $boostSummary = null;

    public function mount(string $id): void
    {
        $this->post = Post::query()->with(['user', 'images', 'video'])->findOrFail($id);

        if ($this->post->user_id !== auth()->id()) {
            abort(403);
        }
    }

    public function boostPost(array $data): array
    {
        $this->errorMessage = '';

        try {
            $boostService = app(PostBoostService::class);
            $boost = $boostService->createBoost(auth()->user(), $this->post, $data);

            $this->isSuccess = true;
            $this->boostSummary = [
                'id' => $boost->id,
                'target_url' => $boost->target_url,
                'cta' => $boost->cta,
                'total_clicks' => $boost->total_clicks,
                'pk_cost' => $boost->pk_cost,
                'naira_cost' => $boost->pk_cost * 10,
                'platform_payhankey' => $boost->platform_payhankey,
                'platform_partner' => $boost->platform_partner,
            ];

            return [
                'status' => 'success',
                'boost' => $this->boostSummary,
            ];
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();

            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    public function render()
    {
        $post = $this->post;
        $spendablePk = (int) (auth()->user()?->wallet?->paykoin_spendable ?? 0);
        $authorName = displayName($post->user->name ?? 'User');
        $authorUsername = $post->user->username ?? 'creator';
        $authorAvatar = $post->user->avatar ?? '';
        $postText = plainPostText($post->content ?? '');
        $postExcerpt = Str::limit($postText, 180);
        $firstImage = $post->images->first()?->path ?? null;

        return view('livewire.user.post-boost', [
            'spendablePk' => $spendablePk,
            'authorName' => $authorName,
            'authorUsername' => $authorUsername,
            'authorAvatar' => $authorAvatar,
            'postText' => $postText,
            'postExcerpt' => $postExcerpt,
            'firstImage' => $firstImage,
            'currency' => getCurrencyCode(),
        ]);
    }
}
