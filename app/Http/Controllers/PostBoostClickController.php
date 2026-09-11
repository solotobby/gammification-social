<?php

namespace App\Http\Controllers;

use App\Models\PostBoost;
use App\Services\PostBoostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostBoostClickController extends Controller
{
    public function __construct(
        protected PostBoostService $boostService
    ) {}

    /**
     * Track a click on a boosted post and redirect the visitor to the target destination.
     */
    public function handleClick(Request $request, string $boostId): RedirectResponse
    {
        $boost = PostBoost::findOrFail($boostId);
        $platform = $request->query('platform', 'payhankey');

        $destinationUrl = $this->boostService->recordClick($boost, $request, (string) $platform);

        return redirect()->away($destinationUrl);
    }

    /**
     * API Endpoint to syndicate active ads to Partner Websites.
     */
    public function syndicatedAds(Request $request): JsonResponse
    {
        $limit = min(20, max(1, (int) $request->query('limit', 5)));

        $boosts = PostBoost::query()
            ->forPartner()
            ->with(['post.user', 'post.images'])
            ->inRandomOrder()
            ->take($limit)
            ->get();

        $ads = $boosts->map(function (PostBoost $boost) {
            $post = $boost->post;
            $firstImage = $post->images->first()?->path ?? null;

            return [
                'boost_id' => $boost->id,
                'post_id' => $boost->post_id,
                'author' => [
                    'name' => displayName($post->user->name ?? 'User'),
                    'username' => $post->user->username ?? '',
                    'avatar' => $post->user->avatar ?? null,
                ],
                'content' => Str::limit(plainPostText($post->content ?? ''), 200),
                'image' => $firstImage,
                'cta' => $boost->cta,
                'click_url' => route('boost.click', [
                    'boostId' => $boost->id,
                    'platform' => 'partner',
                ]),
            ];
        });

        return response()->json([
            'status' => 'success',
            'count' => $ads->count(),
            'ads' => $ads,
        ]);
    }
}
