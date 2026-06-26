<div>
    {{-- GLightbox (used by post-content lightbox) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

    {{-- Fonts — ideally move these <link>s to your layout <head> so they aren't re-touched on Livewire re-renders --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://api.fontshare.com" crossorigin>
    <link href="https://api.fontshare.com/v2/css?f[]=clash-display@600,700&display=swap" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Space+Mono:wght@700&display=swap"
        rel="stylesheet">

    @verbatim
        <style>
            /* ===== Payhankey feed — scoped design tokens (prefixed .ph- so they never leak into includes) ===== */
            .ph-feed-wrap {
                --ph-violet: #5A4FDC;
                --ph-violet-bright: #7B6CF6;
                --ph-indigo: #15103A;
                --ph-mint: #10B981;
                --ph-mint-bright: #34D399;
                --ph-gold: #F4B740;
                --ph-pink: #F4467E;
                --ph-ink: #171331;
                --ph-slate: #5A5578;
                --ph-slate-light: #8B86A6;
                --ph-surface: #EFEBFF;
                --ph-bg: #F6F4FF;
                --ph-line: rgba(90, 79, 220, .12);
                --ph-line-strong: rgba(90, 79, 220, .22);
                --ph-shadow: 0 2px 16px -6px rgba(54, 40, 150, .12);
                --ph-shadow-md: 0 14px 40px -12px rgba(74, 58, 180, .22);
                --ph-display: 'Clash Display', system-ui, sans-serif;
                --ph-body: 'Plus Jakarta Sans', system-ui, sans-serif;
                --ph-mono: 'Space Mono', ui-monospace, monospace;
                font-family: var(--ph-body);
                color: var(--ph-ink);
            }

            .ph-feed-wrap .ph-money {
                font-family: var(--ph-mono);
                font-weight: 700;
                color: var(--ph-mint)
            }

            /* invite & earn */
            .ph-invite {
                display: flex;
                align-items: center;
                gap: 14px;
                background: linear-gradient(120deg, rgba(16, 185, 129, .1), rgba(123, 108, 246, .08));
                border: 1px solid var(--ph-line);
                border-radius: 18px;
                padding: 14px 16px;
                margin-bottom: 18px
            }

            .ph-invite .ph-inv-ic {
                width: 44px;
                height: 44px;
                border-radius: 13px;
                flex: none;
                display: grid;
                place-items: center;
                background: linear-gradient(135deg, var(--ph-mint), var(--ph-mint-bright));
                box-shadow: 0 8px 18px -8px rgba(16, 185, 129, .6)
            }

            .ph-invite .ph-inv-ic i {
                color: #fff;
                font-size: 1.1rem
            }

            .ph-invite .ph-inv-txt {
                flex: 1;
                min-width: 0
            }

            .ph-invite .ph-inv-txt b {
                font-family: var(--ph-display);
                font-weight: 700;
                font-size: .98rem;
                display: block
            }

            .ph-invite .ph-inv-txt small {
                color: var(--ph-slate);
                font-size: .8rem
            }

            .ph-invite .ph-inv-row {
                display: flex;
                gap: 8px;
                margin-top: 8px
            }

            .ph-invite input {
                flex: 1;
                min-width: 0;
                border: 1px solid var(--ph-line);
                border-radius: 10px;
                padding: 8px 12px;
                font-size: .82rem;
                background: #fff;
                color: var(--ph-slate);
                outline: none
            }

            .ph-invite .ph-inv-copy {
                flex: none;
                border-radius: 10px;
                padding: 8px 14px;
                font-weight: 700;
                color: #fff;
                border: none;
                background: linear-gradient(135deg, var(--ph-violet), var(--ph-violet-bright));
                transition: transform .2s
            }

            .ph-invite .ph-inv-copy:hover {
                transform: translateY(-2px)
            }

            /* flashes */
            .ph-flash {
                border: 1px solid var(--ph-line);
                border-radius: 14px;
                padding: 13px 16px;
                margin-bottom: 12px;
                font-size: .92rem;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 10px
            }

            .ph-flash::before {
                content: "";
                width: 8px;
                height: 8px;
                border-radius: 50%;
                flex: none
            }

            .ph-flash--success {
                background: rgba(16, 185, 129, .1);
                color: #0c8a64
            }

            .ph-flash--success::before {
                background: var(--ph-mint)
            }

            .ph-flash--warning {
                background: rgba(244, 183, 64, .13);
                color: #a9791a
            }

            .ph-flash--warning::before {
                background: var(--ph-gold)
            }

            .ph-flash--danger {
                background: rgba(244, 70, 126, .1);
                color: #c2275c
            }

            .ph-flash--danger::before {
                background: var(--ph-pink)
            }

            /* composer */
            .ph-composer {
                background: #fff;
                border: 1px solid var(--ph-line);
                border-radius: 22px;
                box-shadow: var(--ph-shadow);
                margin-bottom: 22px;
                transition: box-shadow .25s, border-color .25s
            }

            .ph-composer:focus-within {
                box-shadow: var(--ph-shadow-md);
                border-color: var(--ph-line-strong)
            }

            .ph-comp-body {
                padding: 18px 20px
            }

            .ph-comp-top {
                display: flex;
                gap: 13px
            }

            .ph-avatar {
                width: 46px;
                height: 46px;
                border-radius: 50%;
                flex: none;
                display: grid;
                place-items: center;
                color: #fff;
                font-family: var(--ph-display);
                font-weight: 700;
                font-size: 1.15rem;
                background: linear-gradient(135deg, var(--ph-violet), var(--ph-violet-bright))
            }

            .ph-field {
                flex: 1;
                min-width: 0
            }

            .ph-field textarea {
                width: 100%;
                border: none;
                outline: none;
                resize: none;
                font-family: inherit;
                font-size: 1.08rem;
                line-height: 1.5;
                color: var(--ph-ink);
                background: none;
                min-height: 34px;
                overflow: hidden
            }

            .ph-field textarea::placeholder {
                color: var(--ph-slate-light)
            }

            .ph-count {
                display: block;
                margin-top: 6px;
                font-size: .78rem;
                color: var(--ph-slate-light);
                font-family: var(--ph-mono)
            }

            .ph-count--warn {
                color: var(--ph-pink)
            }

            /* earnings nudge */
            .ph-nudge {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                margin-top: 12px;
                font-size: .8rem;
                font-weight: 600;
                color: #0c8a64;
                background: linear-gradient(120deg, rgba(16, 185, 129, .1), rgba(52, 211, 153, .06));
                border: 1px solid rgba(16, 185, 129, .2);
                padding: 6px 13px;
                border-radius: 999px
            }

            .ph-nudge i {
                color: var(--ph-mint)
            }

            /* image upload */
            .ph-imgwrap {
                margin-top: 14px
            }

            .ph-imgbtn {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                cursor: pointer;
                border: 1.5px dashed var(--ph-line-strong);
                border-radius: 13px;
                padding: 10px 16px;
                font-weight: 600;
                font-size: .88rem;
                color: var(--ph-violet);
                transition: .2s
            }

            .ph-imgbtn:hover {
                background: var(--ph-surface)
            }

            .ph-imgbtn i {
                font-size: 1rem
            }

            .ph-imghint {
                display: block;
                margin-top: 7px;
                font-size: .78rem;
                color: var(--ph-slate-light)
            }

            .ph-previews {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-top: 12px
            }

            .ph-prev {
                position: relative;
                width: 88px;
                height: 88px;
                border-radius: 14px;
                overflow: hidden;
                border: 1px solid var(--ph-line)
            }

            .ph-prev img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block
            }

            .ph-prev .ph-prev-x {
                position: absolute;
                top: 5px;
                right: 5px;
                width: 24px;
                height: 24px;
                border-radius: 50%;
                border: none;
                background: rgba(12, 8, 38, .62);
                color: #fff;
                font-size: 1rem;
                line-height: 1;
                display: grid;
                place-items: center;
                cursor: pointer
            }

            /* trends */
            .ph-trends-sel {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin-top: 14px
            }

            .ph-chip {
                display: inline-flex;
                align-items: center;
                gap: 7px;
                font-size: .84rem;
                font-weight: 600;
                padding: 7px 8px 7px 13px;
                border-radius: 999px;
                background: linear-gradient(135deg, var(--ph-violet), var(--ph-violet-bright));
                color: #fff;
                border: none
            }

            .ph-chip .ph-chip-x {
                width: 18px;
                height: 18px;
                border-radius: 50%;
                border: none;
                background: rgba(255, 255, 255, .22);
                color: #fff;
                font-size: .7rem;
                line-height: 1;
                display: grid;
                place-items: center;
                cursor: pointer
            }

            .ph-pick-label {
                display: block;
                margin-top: 16px;
                margin-bottom: 9px;
                font-size: .82rem;
                color: var(--ph-slate)
            }

            .ph-pick-label .req {
                color: var(--ph-pink);
                font-weight: 700
            }

            .ph-pick {
                display: inline-flex;
                align-items: center;
                font-size: .84rem;
                font-weight: 600;
                padding: 7px 14px;
                border-radius: 999px;
                background: var(--ph-surface);
                color: var(--ph-violet);
                border: 1px solid transparent;
                margin: 0 7px 7px 0;
                cursor: pointer;
                transition: .18s
            }

            .ph-pick:hover {
                border-color: var(--ph-line-strong);
                transform: translateY(-1px)
            }

            .ph-counter {
                display: flex;
                align-items: center;
                gap: 7px;
                margin-top: 6px;
                font-size: .82rem;
                font-weight: 600;
                color: var(--ph-slate-light)
            }

            .ph-counter.ok {
                color: #0c8a64
            }

            .ph-counter.ok i {
                color: var(--ph-mint);
                animation: ph-pop .4s cubic-bezier(.2, .9, .3, 1.3)
            }

            @keyframes ph-pop {
                from {
                    transform: scale(0)
                }

                to {
                    transform: scale(1)
                }
            }

            /* footer / post button */
            .ph-comp-foot {
                padding: 14px 20px;
                border-top: 1px solid var(--ph-line);
                display: flex;
                justify-content: flex-end
            }

            .ph-post-btn {
                display: inline-flex;
                align-items: center;
                gap: 9px;
                border: none;
                cursor: pointer;
                font-family: var(--ph-body);
                font-weight: 700;
                font-size: .95rem;
                color: #fff;
                padding: 12px 28px;
                border-radius: 999px;
                background: linear-gradient(135deg, var(--ph-violet), var(--ph-violet-bright));
                box-shadow: 0 10px 22px -8px rgba(90, 79, 220, .6);
                transition: transform .2s, opacity .2s
            }

            .ph-post-btn:hover {
                transform: translateY(-2px)
            }

            .ph-post-btn:disabled {
                opacity: .55;
                cursor: wait;
                transform: none
            }

            .ph-post-btn i {
                font-size: .9rem
            }

            .ph-spin {
                width: 15px;
                height: 15px;
                border: 2px solid rgba(255, 255, 255, .4);
                border-top-color: #fff;
                border-radius: 50%;
                display: inline-block;
                animation: ph-rot .7s linear infinite;
                vertical-align: -2px;
                margin-right: 6px
            }

            @keyframes ph-rot {
                to {
                    transform: rotate(360deg)
                }
            }

            /* feed header + empty + load more */
            .ph-feed-head {
                display: flex;
                align-items: center;
                gap: 9px;
                font-family: var(--ph-display);
                font-weight: 700;
                font-size: 1.15rem;
                margin: 4px 2px 16px
            }

            .ph-feed-head::after {
                content: "";
                flex: 1;
                height: 1px;
                background: var(--ph-line)
            }

            .ph-empty {
                text-align: center;
                background: #fff;
                border: 1px dashed var(--ph-line-strong);
                border-radius: 22px;
                padding: 48px 24px
            }

            .ph-empty .ph-empty-ic {
                width: 64px;
                height: 64px;
                border-radius: 18px;
                margin: 0 auto 16px;
                display: grid;
                place-items: center;
                background: var(--ph-surface);
                color: var(--ph-violet);
                font-size: 1.6rem
            }

            .ph-empty h6 {
                font-family: var(--ph-display);
                font-weight: 700;
                font-size: 1.2rem;
                margin-bottom: 6px;
                color: var(--ph-ink)
            }

            .ph-empty p {
                color: var(--ph-slate);
                font-size: .92rem;
                margin: 0
            }

            .ph-loadmore {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                border: 1px solid var(--ph-line-strong);
                background: #fff;
                color: var(--ph-violet);
                font-weight: 700;
                font-size: .9rem;
                padding: 11px 24px;
                border-radius: 999px;
                cursor: pointer;
                transition: .2s
            }

            .ph-loadmore:hover {
                background: var(--ph-surface);
                transform: translateY(-2px)
            }

            .ph-loadmore:disabled {
                opacity: .6;
                cursor: wait
            }
        </style>
    @endverbatim

    <?php $userLevel = userLevel(); ?>

    <div class="row">
        <div class="col-md-8 ph-feed-wrap">

            {{-- ===== Invite & earn (referral) ===== --}}
            <div class="ph-invite">
                <span class="ph-inv-ic"><i class="fa fa-gift"></i></span>
                <div class="ph-inv-txt">
                    <b>Invite friends &amp; earn together</b>
                    <small>Friends who join boost your engagement — and your referral pays you.</small>
                    <div class="ph-inv-row">
                        <input type="text" id="referralLink"
                            value="{{ url('/reg?referral_code=' . auth()->user()->referral_code) }}" readonly />
                        <button class="ph-inv-copy" type="button" onclick="copyReferralLink()"
                            title="Copy to clipboard">
                            <i class="fa fa-copy me-1"></i> Copy
                        </button>
                    </div>
                </div>
            </div>

            {{-- ===== Flash messages ===== --}}
            @foreach (['success' => 'success', 'info' => 'warning', 'error' => 'danger'] as $key => $type)
                @if (session()->has($key))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition.opacity
                        class="ph-flash ph-flash--{{ $type }}" role="alert">
                        {{ session($key) }}
                    </div>
                @endif
            @endforeach

            @error('content')
                <div class="ph-flash ph-flash--danger" role="alert">{{ $message }}</div>
            @enderror
            @error('images')
                <div class="ph-flash ph-flash--danger" role="alert">{{ $message }}</div>
            @enderror
            @error('images.*')
                <div class="ph-flash ph-flash--danger" role="alert">{{ $message }}</div>
            @enderror

            {{-- ===== Composer ===== --}}
            <div class="ph-composer">
                <form wire:submit.prevent="createPost">
                    <div class="ph-comp-body">

                        <div class="ph-comp-top">
                            <div class="ph-avatar">
                                {{ strtoupper(substr(auth()->user()->username ?? (auth()->user()->name ?? 'U'), 0, 1)) }}
                            </div>
                            <div class="ph-field" x-data="{ content: @entangle('content') }">
                                <textarea x-model="content" rows="2" placeholder="Say something amazing — every post can earn"
                                    x-on:input="$el.style.height='auto'; $el.style.height = $el.scrollHeight + 'px'"
                                    @if (!in_array(@$userLevel, ['Creator', 'Influencer'])) maxlength="160" @endif required></textarea>
                                @if (!in_array(@$userLevel, ['Creator', 'Influencer']))
                                    <small class="ph-count"
                                        x-bind:class="content.length > 150 ? 'ph-count ph-count--warn' : 'ph-count'"
                                        x-text="content.length + ' / 160'"></small>
                                @endif
                            </div>
                        </div>

                        <span class="ph-nudge"><i class="fa fa-bolt"></i> Every like, comment &amp; view on this post
                            earns you money, Add hashtags for more visiblity</span>

                        {{-- Images (Creator / Influencer only) --}}
                        @if (in_array($userLevel, ['Creator', 'Influencer']))
                            <div class="ph-imgwrap" x-data="{ images: @entangle('images') }">
                                <label class="ph-imgbtn">
                                    <i class="fas fa-image"></i> Add photo
                                    <input type="file" wire:model="images" multiple accept="image/*" hidden
                                        @if (@$userLevel === 'Creator') x-bind:disabled="images.length >= 1" @endif
                                        @if (@$userLevel === 'Influencer') x-bind:disabled="images.length >= 4" @endif>
                                </label>
                                <small
                                    class="ph-imghint">{{ $userLevel === 'Creator' ? 'Up to 1 image' : 'Up to 4 images' }}</small>

                                {{-- Preview + Remove --}}
                                <div class="ph-previews">
                                    @foreach ($images as $index => $image)
                                        <div class="ph-prev">
                                            <img src="{{ $image->temporaryUrl() }}" alt="preview">
                                            <button type="button" class="ph-prev-x"
                                                wire:click="removeImage({{ $index }})"
                                                aria-label="Remove">&times;</button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Selected trends --}}
                        {{-- @if (count($selectedTrends) > 0)
                            <div class="ph-trends-sel">
                                @foreach (activeTrends()->whereIn('id', $selectedTrends) as $trend)
                                    <span class="ph-chip">#{{ $trend->name }}
                                        <button type="button" class="ph-chip-x" wire:click="removeTrend('{{ $trend->id }}')" aria-label="Remove">&times;</button>
                                    </span>
                                @endforeach
                            </div>
                        @endif --}}

                        {{-- Trend picker (unselected only) --}}
                        {{-- <small class="ph-pick-label">Add trending topics <span class="req">(min. 2 required)</span></small>
                        <div>
                            @foreach (activeTrends()->whereNotIn('id', $selectedTrends) as $trend)
                                <button type="button" class="ph-pick" wire:click="addTrend('{{ $trend->id }}')">#{{ $trend->name }}</button>
                            @endforeach
                        </div> --}}

                        {{-- Counter --}}
                        {{-- <div class="ph-counter {{ count($selectedTrends) >= 2 ? 'ok' : '' }}">
                            {{ count($selectedTrends) }} selected
                            @if (count($selectedTrends) >= 2)
                                <i class="fa fa-check-circle"></i> ready to post
                            @else
                                · pick {{ 2 - count($selectedTrends) }} more
                            @endif
                        </div> --}}

                        <div class="ph-comp-foot">
                            <button class="ph-post-btn" type="submit" wire:loading.attr="disabled"
                                wire:target="createPost">
                                <span wire:loading.remove wire:target="createPost"><i class="fa fa-paper-plane"></i>
                                    Post</span>
                                <span wire:loading wire:target="createPost"><span class="ph-spin"></span>
                                    Posting…</span>
                            </button>
                        </div>


                    </div>

                    {{-- <div class="ph-comp-foot">
                        <button class="ph-post-btn" type="submit" wire:loading.attr="disabled"
                            wire:target="createPost">
                            <span wire:loading.remove wire:target="createPost"><i class="fa fa-paper-plane"></i>
                                Post</span>
                            <span wire:loading wire:target="createPost"><span class="ph-spin"></span> Posting…</span>
                        </button>
                    </div> --}}
                </form>
            </div>

            {{-- ===== Feed ===== --}}
            <div class="ph-feed-head">Your feed</div>

            @forelse (@$posts as $post)
                <livewire:user.post-content :post="$post" :wire:key="'post-'.$post->id" />
            @empty
                <div class="ph-empty">
                    <div class="ph-empty-ic"><i class="fa fa-feather-alt"></i></div>
                    <h6>Your feed is waiting</h6>
                    <p>Share your first post above — it can start earning the moment people engage.</p>
                </div>
            @endforelse

            {{-- Global video player --}}
            @if (@$isVideoOpen)
                <livewire:user.video-player :videoId="$activeVideoId" wire:key="video-player-{{ @$activeVideoId }}" />
            @endif

            {{-- Load more --}}
            @if ($hasMore)
                <div class="text-center my-3">
                    <button wire:click="loadNextPage" wire:loading.attr="disabled" wire:target="loadNextPage"
                        class="ph-loadmore">
                        <span wire:loading.remove wire:target="loadNextPage">Load more feeds <i
                                class="fa fa-arrow-down"></i></span>
                        <span wire:loading wire:target="loadNextPage"><span class="ph-spin"></span> Loading
                            feeds…</span>
                    </button>
                </div>
            @endif

        </div>

        @include('layouts.engagement')
    </div>

    @include('layouts.onboarding')
</div>
