<div>
    <style>
        .bk-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: .88rem;
            font-weight: 600;
            color: #536471;
            text-decoration: none;
            padding: 16px 0 12px;
            transition: color .15s;
        }

        .bk-back:hover { color: #5A4FDC; }

        .bk-back svg { width: 18px; height: 18px; }

        .ph-feed-head {
            font-size: 1.05rem;
            font-weight: 700;
            color: #0f1419;
            padding: 12px 0 14px;
            border-bottom: 1px solid #eff3f4;
            margin-bottom: 1px;
        }

        .ph-flash {
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 12px;
        }

        .ph-flash--success {
            background: #e6f4ea;
            color: #1b5e20;
            border-left: 4px solid #43a047;
        }

        .ph-empty {
            text-align: center;
            padding: 48px 24px;
            background: #fff;
            border: 1px solid #eff3f4;
        }

        .ph-empty .ph-empty-ic {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: rgba(90, 79, 220, .1);
            color: #5A4FDC;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
            font-size: 20px;
        }

        .ph-empty h6 {
            font-size: 16px;
            font-weight: 700;
            color: #0f1419;
            margin: 0 0 6px;
        }

        .ph-empty p {
            font-size: 14px;
            color: #536471;
            margin: 0;
        }
    </style>

    <div class="row">
        <div class="col-md-8 ph-feed-wrap">
            <a href="{{ url('timeline') }}" class="bk-back" wire:navigate>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Back to feed
            </a>

            @if (session()->has('success'))
                <div class="ph-flash ph-flash--success" role="alert">{{ session('success') }}</div>
            @endif

            <div class="ph-feed-head">
                <i class="fa fa-bookmark" style="margin-right:8px;color:#5A4FDC"></i>
                Bookmarked
            </div>

            @forelse ($posts as $post)
                <livewire:user.post-content
                    :post="$post"
                    :estimated-earnings="$earnings[$post->id] ?? 0"
                    :format-text="false"
                    :show-post-menu="true"
                    wire:key="bookmark-{{ $post->id }}" />
            @empty
                <div class="ph-empty">
                    <div class="ph-empty-ic"><i class="fa fa-bookmark"></i></div>
                    <h6>No bookmarked posts yet</h6>
                    <p>Bookmark posts from your feed to find them here later.</p>
                    <a href="{{ url('timeline') }}" class="pk-btn pk-btn--primary" wire:navigate style="margin-top:12px;display:inline-flex">
                        Browse feed
                    </a>
                </div>
            @endforelse

            @if ($hasMore)
                <div class="text-center my-3">
                    <button type="button"
                        class="ph-loadmore"
                        style="display:inline-flex;align-items:center;gap:8px;border:1px solid #cfd9de;background:#fff;color:#5A4FDC;font-weight:700;font-size:.9rem;padding:11px 24px;border-radius:999px;cursor:pointer"
                        wire:click="loadMore"
                        wire:loading.attr="disabled"
                        wire:target="loadMore">
                        <span wire:loading.remove wire:target="loadMore">Load more posts <i class="fa fa-arrow-down"></i></span>
                        <span wire:loading wire:target="loadMore">Loading…</span>
                    </button>
                </div>
            @endif
        </div>

        @include('layouts.engagement')
    </div>

    @include('layouts.onboarding')

    <livewire:user.post-photo-viewer />
</div>
