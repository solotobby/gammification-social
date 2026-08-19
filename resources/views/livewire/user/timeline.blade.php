<div>
    {{-- GLightbox removed — Facebook-style photo viewer handles images --}}

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

            /* Facebook-style composer media */
            .ph-fb-preview {
                margin: 12px 20px 0;
                border: 1px solid #ced0d4;
                border-radius: 8px;
                overflow: hidden;
                background: #f0f2f5
            }

            .ph-fb-preview-bar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 8px 12px;
                background: #fff;
                border-bottom: 1px solid #e4e6eb;
                font-size: .8rem;
                font-weight: 600;
                color: #65676b
            }

            .ph-fb-preview-bar button {
                border: none;
                background: #e4e6eb;
                color: #050505;
                font-size: .75rem;
                font-weight: 600;
                padding: 4px 10px;
                border-radius: 6px;
                cursor: pointer;
                font-family: inherit
            }

            .ph-fb-preview-bar button:hover { background: #d8dadf }

            /* FB image collage — composer + feed */
            .fb-img-grid {
                display: grid;
                gap: 2px;
                background: #000
            }

            .fb-img-grid.n1 { grid-template-columns: 1fr }
            .fb-img-grid.n1 .fb-img-cell { height: 280px }
            .fb-img-grid.n2 { grid-template-columns: 1fr 1fr }
            .fb-img-grid.n2 .fb-img-cell { height: 220px }
            .fb-img-grid.n3 { grid-template-columns: 1fr 1fr }
            .fb-img-grid.n3 .fb-img-cell:first-child { grid-row: span 2; min-height: 220px }
            .fb-img-grid.n3 .fb-img-cell { height: 110px }
            .fb-img-grid.n4 { grid-template-columns: 1fr 1fr }
            .fb-img-grid.n4 .fb-img-cell { height: 140px }

            .fb-img-cell {
                position: relative;
                overflow: hidden;
                background: #1c1e21
            }

            .fb-img-cell img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block
            }

            .fb-img-remove {
                position: absolute;
                top: 6px;
                right: 6px;
                width: 28px;
                height: 28px;
                border-radius: 50%;
                border: none;
                background: rgba(0, 0, 0, .65);
                color: #fff;
                font-size: 1rem;
                cursor: pointer;
                display: grid;
                place-items: center;
                z-index: 2
            }

            .fb-img-more {
                position: absolute;
                inset: 0;
                background: rgba(0, 0, 0, .55);
                color: #fff;
                font-size: 1.5rem;
                font-weight: 700;
                display: grid;
                place-items: center;
                text-decoration: none
            }

            /* FB video preview in composer */
            .ph-fb-vid {
                position: relative;
                background: #1c1e21;
                aspect-ratio: 9/16;
                max-height: 360px;
                margin: 0 auto
            }

            .ph-fb-vid video {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block
            }

            .ph-fb-vid-status {
                padding: 12px;
                font-size: .82rem;
                color: #65676b;
                text-align: center
            }

            /* FB toolbar — Photo | Roll side by side */
            .ph-fb-toolbar {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
                padding: 12px 20px;
                border-top: 1px solid #e4e6eb;
                margin-top: 12px
            }

            .ph-fb-tool {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                padding: 10px 12px;
                border: none;
                border-radius: 8px;
                background: #f0f2f5;
                font-family: inherit;
                font-size: .88rem;
                font-weight: 600;
                color: #050505;
                cursor: pointer;
                transition: background .15s
            }

            .ph-fb-tool:hover:not(:disabled) { background: #e4e6eb }
            .ph-fb-tool:disabled { opacity: .4; cursor: not-allowed }

            .ph-fb-tool-ic {
                width: 28px;
                height: 28px;
                border-radius: 50%;
                display: grid;
                place-items: center;
                font-size: .8rem;
                color: #fff;
                flex-shrink: 0
            }

            .ph-fb-tool--photo .ph-fb-tool-ic { background: #45bd62 }
            .ph-fb-tool--roll .ph-fb-tool-ic { background: #f02849 }

            .ph-fb-tool small {
                display: block;
                font-size: .65rem;
                font-weight: 500;
                color: #65676b;
                margin-top: 1px
            }

            .ph-prev {
                position: relative;
                overflow: hidden
            }

            .ph-prev img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block
            }

            /* video upload states */
            .ph-vprog {
                margin-top: 0;
                padding: 12px
            }

            .ph-vprog-meta {
                display: flex;
                justify-content: space-between;
                font-size: .78rem;
                color: var(--ph-slate);
                margin-bottom: 6px
            }

            .ph-vprog-track {
                height: 6px;
                background: var(--ph-line);
                border-radius: 99px;
                overflow: hidden
            }

            .ph-vprog-fill {
                height: 100%;
                background: linear-gradient(90deg, var(--ph-pink), var(--ph-violet));
                border-radius: 99px;
                transition: width .4s
            }

            .ph-vthumb {
                margin-top: 0;
                border-radius: 0;
                overflow: hidden;
                background: #000;
                aspect-ratio: 9/16;
                max-height: 360px;
                position: relative
            }

            .ph-vthumb video {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block
            }

            .ph-vdone {
                margin-top: 12px;
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px 14px;
                background: rgba(16, 185, 129, .08);
                border: 1px solid rgba(16, 185, 129, .2);
                border-radius: 12px;
                font-size: .82rem;
                color: #0c8a64
            }

            .ph-vcancel {
                margin-left: auto;
                border: none;
                background: none;
                color: var(--ph-slate);
                font-size: .78rem;
                cursor: pointer;
                text-decoration: underline;
                font-family: inherit
            }

            .ph-vidbtn {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                cursor: pointer;
                border: none;
                background: rgba(244, 70, 126, .08);
                border-radius: 13px;
                padding: 10px 16px;
                font-weight: 600;
                font-size: .88rem;
                color: var(--ph-pink);
                font-family: inherit;
                transition: .2s
            }

            .ph-vidbtn:hover {
                background: rgba(244, 70, 126, .14)
            }

            .ph-comp-actions {
                display: none
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
                border-top: 1px solid #e4e6eb;
                display: flex;
                justify-content: flex-end;
                align-items: center;
                gap: 12px;
                margin-top: 0
            }

            .ph-comp-foot-note {
                flex: 1;
                font-size: .78rem;
                color: #65676b;
                text-align: left
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
            {{-- <div class="ph-invite">
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
            </div> --}}

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
            @error('video')
                <div class="ph-flash ph-flash--danger" role="alert">{{ $message }}</div>
            @enderror

            {{-- ===== Composer ===== --}}
            <div class="ph-composer" x-data="timelineComposer()" x-init="boot()"
                 @if($videoUploadStatus === 'processing' && $videoJobId) wire:poll.2s="pollVideoProcessing" @endif>
                <form @submit.prevent="videoMode && vStatus === 'done' ? $wire.publishVideo() : (!videoMode ? $wire.createPost() : null)">
                    <div class="ph-comp-body">

                        @if (in_array($userLevel, ['Creator', 'Influencer']))
                            <input type="file"
                                x-ref="imgInput"
                                wire:model="images"
                                accept="image/*"
                                multiple
                                style="position:absolute;width:1px;height:1px;opacity:0;pointer-events:none"
                                tabindex="-1"
                                @if ($userLevel === 'Creator') x-bind:disabled="images.length >= 1" @endif
                                @if ($userLevel === 'Influencer') x-bind:disabled="images.length >= 4" @endif>

                            @if (canUploadVideo($userLevel))
                                <input type="file"
                                    x-ref="vidInput"
                                    wire:model="video"
                                    accept="video/*"
                                    style="position:absolute;width:1px;height:1px;opacity:0;pointer-events:none"
                                    tabindex="-1">
                            @endif
                        @endif

                        <div class="ph-comp-top">
                            <div class="ph-avatar">
                                {{ strtoupper(substr(auth()->user()->username ?? (auth()->user()->name ?? 'U'), 0, 1)) }}
                            </div>
                            <div class="ph-field" x-data="{ content: @entangle('content') }">
                                <textarea x-model="content" rows="2"
                                    :placeholder="videoMode ? 'Say something about your roll…' : 'What\'s on your mind?'"
                                    x-on:input="$el.style.height='auto'; $el.style.height = $el.scrollHeight + 'px'"
                                    @if (!in_array(@$userLevel, ['Creator', 'Influencer'])) maxlength="160" @endif required></textarea>
                                @if (!in_array(@$userLevel, ['Creator', 'Influencer']))
                                    <small class="ph-count"
                                        x-bind:class="content.length > 150 ? 'ph-count ph-count--warn' : 'ph-count'"
                                        x-text="content.length + ' / 160'"></small>
                                @endif
                            </div>
                        </div>

                        {{-- Facebook-style media preview + toolbar --}}
                        @if (in_array($userLevel, ['Creator', 'Influencer']))
                            @php
                                $maxImages = $userLevel === 'Creator' ? 1 : 4;
                                $imgPreviewCount = count($images);
                                $imgGridClass = 'n' . min(max($imgPreviewCount, 1), 4);
                            @endphp

                            {{-- Photo preview (FB collage) --}}
                            <div class="ph-fb-preview" x-show="!videoMode && images.length > 0" x-cloak>
                                <div class="ph-fb-preview-bar">
                                    <span><i class="fas fa-image" style="color:#45bd62;margin-right:4px"></i> Photos</span>
                                    <button type="button" @click="$wire.clearImages()">Remove all</button>
                                </div>
                                <div class="fb-img-grid {{ $imgGridClass }}">
                                    @foreach ($images as $index => $image)
                                        <div class="fb-img-cell">
                                            <img src="{{ $image->temporaryUrl() }}" alt="Preview">
                                            <button type="button" class="fb-img-remove"
                                                wire:click="removeImage({{ $index }})"
                                                @click.stop aria-label="Remove">&times;</button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            @if (canUploadVideo($userLevel))
                                {{-- Roll preview --}}
                                <div class="ph-fb-preview" x-show="videoMode && vStatus !== 'idle'" x-cloak>
                                    <div class="ph-fb-preview-bar">
                                        <span><i class="fas fa-video" style="color:#f02849;margin-right:4px"></i> Roll</span>
                                        <button type="button" @click="resetVideo(); $wire.cancelVideoUpload()">Remove</button>
                                    </div>

                                    <div class="ph-vprog" x-show="vStatus === 'uploading' || vStatus === 'processing'">
                                        <div class="ph-vprog-meta">
                                            <span x-text="vStatus === 'processing' ? 'Processing video (high / medium / low)…' : 'Uploading…'"></span>
                                            <span x-text="vPct + '%'"></span>
                                        </div>
                                        <div class="ph-vprog-track">
                                            <div class="ph-vprog-fill" :style="'width:' + vPct + '%'"></div>
                                        </div>
                                    </div>


                                    @if ($cloudinaryVideoUrl)
                                        <div class="ph-fb-vid" x-show="vStatus === 'done'">
                                            <video src="{{ $cloudinaryVideoUrl }}" muted playsinline controls></video>
                                        </div>
                                    @endif

                                    <div class="ph-fb-vid-status" x-show="vStatus === 'done'">
                                        <i class="fas fa-check-circle" style="color:#45bd62"></i> Ready — add a caption and post
                                    </div>

                                    <div class="ph-fb-vid-status" x-show="vStatus === 'error'" style="color:#f02849">
                                        Upload failed. Tap Roll below to try again.
                                    </div>
                                </div>

                                <div wire:loading wire:target="video" class="ph-fb-preview" style="margin-top:12px" x-show="videoMode">
                                    <div class="ph-fb-vid-status"><span class="ph-spin"></span> Staging video…</div>
                                </div>
                            @endif

                            {{-- FB toolbar: Photo | Roll (Influencer only) --}}
                            <div class="ph-fb-toolbar">
                                <button type="button" class="ph-fb-tool ph-fb-tool--photo"
                                    @click="openPhoto()"
                                    :disabled="videoMode && vStatus !== 'idle'">
                                    <span class="ph-fb-tool-ic"><i class="fas fa-image"></i></span>
                                    <span>Photo<small>{{ $maxImages === 1 ? '1 max' : 'Up to '.$maxImages }}</small></span>
                                </button>
                                @if (canUploadVideo($userLevel))
                                    <button type="button" class="ph-fb-tool ph-fb-tool--roll"
                                        @click="openVideo()"
                                        :disabled="images.length > 0 && !videoMode">
                                        <span class="ph-fb-tool-ic"><i class="fas fa-video"></i></span>
                                        <span>Roll<small>10 min</small></span>
                                    </button>
                                @endif
                            </div>
                        @endif

                        <div class="ph-comp-foot">
                            <span class="ph-comp-foot-note" x-show="videoMode && vStatus === 'processing'" x-cloak>
                                Converting your roll to multiple qualities — you can keep this tab open.
                            </span>
                            <span class="ph-comp-foot-note" x-show="videoMode && vStatus === 'uploading'" x-cloak>
                                Uploading your video…
                            </span>
                            <span class="ph-comp-foot-note" x-show="!videoMode" wire:loading.remove wire:target="createPost">
                                @if ($userLevel === 'Influencer')
                                    Photo post or roll — pick one above.
                                @elseif ($userLevel === 'Creator')
                                    Photo posts · 1 image max.
                                @else
                                    Basic posts · 160 characters max.
                                @endif
                            </span>
                            <button class="ph-post-btn" type="submit"
                                wire:loading.attr="disabled"
                                wire:target="createPost,publishVideo,uploadVideo,video,uploadToCloudinary"
                                :disabled="videoMode && (vStatus === 'uploading' || vStatus === 'processing')">
                                <span wire:loading.remove wire:target="createPost,publishVideo">
                                    <i class="fa fa-paper-plane"></i>
                                    <span x-text="videoMode ? 'Publish Roll' : 'Post'"></span>
                                </span>
                                <span wire:loading wire:target="createPost,publishVideo,video,uploadVideo,uploadToCloudinary">
                                    <span class="ph-spin"></span> Working…
                                </span>
                            </button>
                        </div>

                    </div>
                </form>
            </div>

            {{-- ===== Feed ===== --}}
            <div class="ph-feed-head">Your feed</div>

            @forelse ($posts as $post)
                <livewire:user.post-content
                    :post="$post"
                    :estimated-earnings="$earnings[$post->id] ?? 0"
                    :format-text="false"
                    :show-post-menu="true"
                    wire:key="post-{{ $post->id }}" />
            @empty
                <div class="ph-empty">
                    <div class="ph-empty-ic"><i class="fa fa-feather-alt"></i></div>
                    <h6>Your feed is waiting</h6>
                    <p>Share your first post above — it can start earning the moment people engage.</p>
                </div>
            @endforelse

            {{-- Global video player — redirects to /rolls/{id} --}}
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

    <livewire:user.post-photo-viewer />

    @include('layouts.onboarding')

    <script>
        function timelineComposer() {
            return {
                videoMode: @js($composerVideoMode ?? false),
                vStatus: @js($videoUploadStatus ?: 'idle'),
                vPct: @js($videoUploadProgress ?? 0),
                images: @entangle('images'),
                boot() {
                    Livewire.on('videoUploadStatus', ({ status, progress }) => {
                        this.vStatus = status;
                        this.vPct = progress ?? 0;
                        if (status === 'uploading' || status === 'processing' || status === 'ready') {
                            this.videoMode = true;
                        }
                    });
                },
                photoBlocked() {
                    return this.videoMode && this.vStatus !== 'idle';
                },
                videoBlocked() {
                    return this.images.length > 0 && !this.videoMode;
                },
                openPhoto() {
                    if (this.photoBlocked()) return;
                    if (this.videoMode && this.vStatus === 'idle') {
                        this.resetVideo();
                        this.$wire.cancelVideoUpload();
                    }
                    this.$refs.imgInput?.click();
                },
                openVideo() {
                    if (this.videoBlocked()) return;
                    if (this.vStatus === 'idle' && !this.videoMode) {
                        this.videoMode = true;
                        this.$refs.vidInput?.click();
                    } else if (this.videoMode && this.vStatus === 'idle') {
                        this.$refs.vidInput?.click();
                    }
                },
                resetVideo() {
                    this.videoMode = false;
                    this.vStatus = 'idle';
                    this.vPct = 0;
                }
            };
        }
    </script>
</div>
