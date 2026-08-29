<div class="pf-page">
    @php
        $level = userLevel($user->id);
        $isVerified = in_array($level, ['Creator', 'Influencer']);
        $isInfluencer = $level === 'Influencer';
        $defaultBanner = asset('src/assets/media/photos/photo19@2x.jpg');
        $defaultAvatar = asset('src/assets/media/avatars/avatar13.jpg');
        $avatarUrl = $user->avatar ?: $defaultAvatar;
        $bannerUrl = $user->banner ?: $defaultBanner;
        $hasCustomBanner = filled($user->banner);
        $totalLikes = sumCounter($user->total_likes, $user->total_likes_external);
    @endphp

    @verbatim
        <style>
            .pf-page {
                --pf-violet: #5A4FDC;
                --pf-blue: #1877F2;
                --pf-blue-hover: #166FE5;
                --pf-ink: #0F1117;
                --pf-text: #050505;
                --pf-muted: #65676B;
                --pf-gray-400: #9CA3AF;
                --pf-gray-500: #6B7280;
                --pf-gray-700: #374151;
                --pf-line: #CED0D4;
                --pf-bg: #F0F2F5;
                --pf-white: #FFFFFF;
                --pf-avatar: clamp(112px, 22vw, 168px);
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                color: var(--pf-text);
                margin: 0 -12px 16px;
            }

            @media (min-width: 992px) {
                .pf-page { margin-inline: 0; }
            }

            .pf-page *,
            .pf-page *::before,
            .pf-page *::after { box-sizing: border-box; }

            /* ---- hero shell (matches community) ---- */
            .pf-hero {
                background: var(--pf-white);
                border: 1px solid #eff3f4;
                border-radius: 0 0 8px 8px;
                box-shadow: 0 1px 2px rgba(0, 0, 0, .06);
                overflow: visible;
                margin-bottom: 12px;
                padding-bottom: 0;
            }

            /* ---- cover + fade ---- */
            .pf-cover {
                position: relative;
                height: clamp(160px, 32vw, 350px);
                background-color: #BCC0C4;
                background-size: cover;
                background-position: center;
            }

            .pf-cover:not(.has-image)::before {
                content: "";
                position: absolute;
                inset: 0;
                background:
                    radial-gradient(circle at 18% 28%, rgba(255, 255, 255, .14) 0%, transparent 42%),
                    radial-gradient(circle at 82% 72%, rgba(255, 255, 255, .1) 0%, transparent 38%),
                    radial-gradient(circle at 55% 105%, rgba(255, 255, 255, .08) 0%, transparent 45%);
                pointer-events: none;
            }

            .pf-cover::after {
                content: "";
                position: absolute;
                inset: 0;
                background: linear-gradient(
                    to bottom,
                    transparent 0%,
                    transparent 58%,
                    rgba(255, 255, 255, .55) 82%,
                    rgba(255, 255, 255, .98) 100%
                );
                pointer-events: none;
                z-index: 1;
            }

            .pf-cover.has-image::after {
                background: linear-gradient(
                    to bottom,
                    transparent 0%,
                    transparent 52%,
                    rgba(255, 255, 255, .6) 80%,
                    rgba(255, 255, 255, .98) 100%
                );
            }

            .pf-cover-edit {
                position: absolute;
                right: 12px;
                bottom: 16px;
                z-index: 2;
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                max-width: calc(100% - 24px);
                justify-content: flex-end;
            }

            @media (min-width: 576px) {
                .pf-cover-edit {
                    right: 16px;
                    bottom: 20px;
                }
            }

            .pf-cover-edit label,
            .pf-cover-edit button {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 8px 12px;
                border-radius: 6px;
                border: none;
                background: rgba(255, 255, 255, .92);
                color: var(--pf-text);
                font-size: clamp(.8rem, 2.4vw, .9375rem);
                font-weight: 600;
                cursor: pointer;
                box-shadow: 0 1px 2px rgba(0, 0, 0, .15);
                font-family: inherit;
                white-space: nowrap;
            }

            .pf-cover-edit label:hover,
            .pf-cover-edit button:hover {
                background: #fff;
            }

            .pf-cover-edit button.pf-cover-remove {
                background: rgba(0, 0, 0, .55);
                color: #fff;
            }

            .pf-cover-edit button.pf-cover-remove:hover {
                background: rgba(0, 0, 0, .7);
            }

            .pf-cover-loading {
                position: absolute;
                inset: 0;
                display: grid;
                place-items: center;
                background: rgba(0, 0, 0, .35);
                color: #fff;
                font-size: 15px;
                font-weight: 600;
                z-index: 3;
            }

            /* ---- profile head ---- */
            .pf-head {
                position: relative;
                padding: 0 16px 10px;
                background: var(--pf-white);
                margin-top: 0;
                z-index: 2;
                border-radius: 0 0 8px 8px;
            }

            @media (min-width: 768px) {
                .pf-head { padding: 0 32px 12px; }
            }

            .pf-head-row {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 8px;
            }

            @media (min-width: 900px) {
                .pf-head-row {
                    flex-direction: row;
                    align-items: flex-end;
                    justify-content: space-between;
                    min-height: 0;
                }
            }

            .pf-head-left {
                display: flex;
                flex-direction: column;
                align-items: center;
                width: 100%;
                min-width: 0;
                gap: 4px;
            }

            @media (min-width: 900px) {
                .pf-head-left {
                    flex-direction: row;
                    align-items: flex-end;
                    gap: 14px;
                    width: auto;
                    flex: 1;
                }
            }

            /* 50% of avatar sits on the faded cover */
            .pf-avatar-wrap {
                position: relative;
                margin-top: calc(var(--pf-avatar) / -2);
                flex-shrink: 0;
                z-index: 3;
            }

            .pf-avatar-wrap .ua {
                --ua-size: var(--pf-avatar) !important;
                width: var(--pf-avatar);
                height: var(--pf-avatar);
            }

            .pf-avatar-edit {
                position: absolute;
                inset: 4px;
                border-radius: 50%;
                background: rgba(0, 0, 0, 0);
                display: grid;
                place-items: center;
                cursor: pointer;
                transition: background .2s ease;
                z-index: 4;
            }

            .pf-avatar-wrap:hover .pf-avatar-edit {
                background: rgba(0, 0, 0, .45);
            }

            .pf-avatar-edit i {
                color: #fff;
                font-size: clamp(1.25rem, 4vw, 1.75rem);
                opacity: 0;
                transition: opacity .2s ease;
            }

            .pf-avatar-wrap:hover .pf-avatar-edit i {
                opacity: 1;
            }

            .pf-avatar-loading {
                position: absolute;
                inset: 4px;
                border-radius: 50%;
                background: rgba(0, 0, 0, .45);
                color: #fff;
                font-size: 13px;
                font-weight: 600;
                display: grid;
                place-items: center;
                z-index: 5;
            }

            .pf-identity {
                flex: 1;
                min-width: 0;
                padding-bottom: 0;
                text-align: center;
            }

            @media (min-width: 900px) {
                .pf-identity {
                    text-align: left;
                    padding-bottom: 2px;
                }
            }

            .pf-name {
                margin: 0;
                font-size: clamp(1.2rem, 2.8vw, 1.75rem);
                font-weight: 800;
                line-height: 1.1;
                letter-spacing: -.02em;
                color: var(--pf-ink);
                word-break: break-word;
            }

            .pf-name-row {
                display: inline-flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 5px;
                justify-content: center;
            }

            @media (min-width: 900px) {
                .pf-name-row { justify-content: flex-start; }
            }

            .pf-name-row svg {
                flex: none;
                width: 18px;
                height: 18px;
            }

            .pf-subline {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: center;
                gap: 5px;
                margin: 2px 0 0;
                font-size: clamp(.78rem, 1.8vw, .875rem);
                color: var(--pf-gray-500);
                line-height: 1.25;
            }

            @media (min-width: 900px) {
                .pf-subline { justify-content: flex-start; }
            }

            .pf-subline a {
                color: var(--pf-gray-700);
                text-decoration: none;
                font-weight: 600;
            }

            .pf-subline a:hover { text-decoration: underline; }

            .pf-subline strong {
                color: var(--pf-gray-700);
                font-weight: 600;
            }

            .pf-dot {
                width: 3px;
                height: 3px;
                border-radius: 50%;
                background: var(--pf-gray-400);
                flex: none;
            }

            .pf-meta-inline {
                display: flex;
                flex-wrap: wrap;
                gap: 4px 12px;
                justify-content: center;
                font-size: clamp(.75rem, 1.8vw, .82rem);
                color: var(--pf-muted);
                margin-top: 4px;
                line-height: 1.25;
            }

            @media (min-width: 900px) {
                .pf-meta-inline { justify-content: flex-start; }
            }

            .pf-meta-inline span {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                min-width: 0;
                max-width: 100%;
            }

            .pf-head-actions {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: center;
                gap: 6px;
                width: 100%;
                position: relative;
                z-index: 2;
            }

            @media (min-width: 900px) {
                .pf-head-actions {
                    justify-content: flex-end;
                    width: auto;
                    flex-shrink: 0;
                    padding-bottom: 2px;
                }
            }

            .pf-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                padding: 0 14px;
                min-height: 32px;
                border-radius: 6px;
                border: none;
                font-family: inherit;
                font-size: .875rem;
                font-weight: 600;
                line-height: 1.2;
                white-space: nowrap;
                cursor: pointer;
                text-decoration: none;
                transition: background .15s ease, filter .15s ease;
            }

            .pf-btn--primary {
                background: var(--pf-violet);
                color: #fff;
            }

            .pf-btn--primary:hover {
                filter: brightness(1.06);
                color: #fff;
            }

            .pf-btn--secondary {
                background: #E4E6EB;
                color: var(--pf-ink);
            }

            .pf-btn--secondary:hover { background: #D8DADF; color: var(--pf-ink); }

            .pf-btn--following {
                background: #E4E6EB;
                color: var(--pf-ink);
            }

            @media (max-width: 899.98px) {
                .pf-head-actions .pf-btn {
                    flex: 1 1 calc(50% - 4px);
                    min-width: 0;
                }
            }

            @media (max-width: 479.98px) {
                .pf-page {
                    --pf-avatar: clamp(96px, 28vw, 128px);
                }

                .pf-head-actions .pf-btn {
                    flex: 1 1 100%;
                }

                .pf-cover {
                    height: clamp(140px, 42vw, 220px);
                }
            }

            /* ---- tabs (FB-style) ---- */
            .pf-tabs {
                display: flex;
                gap: 2px;
                border-top: 1px solid var(--pf-line);
                margin-top: 8px;
                padding-top: 0;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
            }

            .pf-tabs::-webkit-scrollbar { display: none; }

            .pf-tab {
                padding: 10px 14px;
                font-size: .875rem;
                font-weight: 600;
                color: var(--pf-muted);
                border: none;
                background: none;
                border-bottom: 3px solid transparent;
                cursor: default;
                white-space: nowrap;
                font-family: inherit;
                line-height: 1.2;
            }

            a.pf-tab { cursor: pointer; }

            .pf-tab--active {
                color: var(--pf-violet);
                border-bottom-color: var(--pf-violet);
            }

            .pf-alert {
                margin: 0 16px 12px;
                padding: 12px 14px;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 600;
            }

            .pf-alert--success {
                background: #E7F3FF;
                color: #1877F2;
                border: 1px solid #B6D4FE;
            }

            .pf-alert--error {
                background: #FEF2F2;
                color: #B91C1C;
                border: 1px solid #FECACA;
            }

            .pf-feed-wrap {
                padding-top: 8px;
            }
        </style>
    @endverbatim

    <div class="pf-hero">
        {{-- Cover photo with fade into header --}}
        <div @class(['pf-cover', 'has-image' => true])
            style="background-image:url('{{ $bannerUrl }}')">
            @if ($isOwner)
                <div class="pf-cover-edit">
                    <label for="bannerUploadInput">
                        <i class="fa fa-camera"></i>
                        <span class="d-none d-sm-inline">Edit cover photo</span>
                    </label>
                    @if ($hasCustomBanner)
                        <button type="button" class="pf-cover-remove"
                            wire:click="removeBanner"
                            wire:confirm="Remove your cover photo?"
                            wire:loading.attr="disabled"
                            title="Remove cover">
                            <i class="fa fa-trash"></i>
                        </button>
                    @endif
                    <input type="file" id="bannerUploadInput" wire:model="bannerUpload" class="d-none" accept="image/*">
                </div>
                <div wire:loading wire:target="bannerUpload,removeBanner" class="pf-cover-loading">
                    Updating cover…
                </div>
            @endif
        </div>

        {{-- Profile header --}}
        <div class="pf-head">
            <div class="pf-head-row">
                <div class="pf-head-left">
                    <div class="pf-avatar-wrap">
                        <x-user-avatar :user="$user" size="hero" :href="false" :alt="$user->name" />

                        @if ($isOwner)
                            <label class="pf-avatar-edit" for="avatarUploadInput" title="Edit profile picture">
                                <i class="fa fa-camera"></i>
                            </label>
                            <input type="file" id="avatarUploadInput" wire:model="avatarUpload" class="d-none" accept="image/*">
                            <div wire:loading wire:target="avatarUpload" class="pf-avatar-loading">…</div>
                        @endif
                    </div>

                    <div class="pf-identity">
                        <div class="pf-name-row">
                            <h1 class="pf-name">{{ $user->name }}</h1>
                            @if ($isVerified)
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="{{ $isInfluencer ? '#5A4FDC' : '#1877F2' }}" aria-label="Verified">
                                    <path d="M22.5 5.5l-12 12-5.5-5.5 1.5-1.5 4 4 10.5-10.5z"/>
                                </svg>
                            @endif
                        </div>

                        <p class="pf-subline">
                            <a href="{{ url('profile/' . $user->username . '/connection?tab=followers') }}">
                                <strong>{{ number_format($user->followers) }}</strong> followers
                            </a>
                            <span class="pf-dot" aria-hidden="true"></span>
                            <a href="{{ url('profile/' . $user->username . '/connection?tab=following') }}">
                                <strong>{{ number_format($user->following) }}</strong> following
                            </a>
                            <span class="pf-dot" aria-hidden="true"></span>
                            <span><strong>{{ number_format($totalLikes) }}</strong> likes</span>
                        </p>

                        @if ($user->profile?->about || $user->profile?->date_of_birth || $user->profile?->location)
                            <div class="pf-meta-inline">
                                @if ($user->profile?->about)
                                    <span><i class="fa fa-user"></i> {{ $user->profile->about }}</span>
                                @endif
                                @if ($user->profile?->date_of_birth)
                                    <span>
                                        <i class="fa fa-birthday-cake"></i>
                                        {{ \Carbon\Carbon::parse($user->profile->date_of_birth)->format('F j') }}
                                    </span>
                                @endif
                                @if ($user->profile?->location)
                                    <span><i class="fa fa-map-marker"></i> {{ $user->profile->location }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="pf-head-actions">
                    @if ($isOwner)
                        <a href="{{ url('settings') }}" class="pf-btn pf-btn--secondary">
                            <i class="fa fa-pencil"></i> Edit profile
                        </a>
                    @else
                        @if (auth()->user()->hasRole('user'))
                        <a href="{{ route('messages', ['start' => $user->username]) }}" class="pf-btn pf-btn--secondary">
                            <i class="fa fa-comment-dots"></i> Message
                        </a>
                        @endif
                        <button type="button" wire:click="toggleFollow"
                            @class(['pf-btn', 'pf-btn--following' => $isFollowing, 'pf-btn--primary' => ! $isFollowing])>
                            <i class="fa {{ $isFollowing ? 'fa-check' : 'fa-user-plus' }}"></i>
                            {{ $isFollowing ? 'Following' : 'Follow' }}
                        </button>
                    @endif
                </div>
            </div>

            <div class="pf-tabs" role="tablist">
                <span class="pf-tab pf-tab--active" role="tab" aria-selected="true">Posts</span>
                @if ($isOwner)
                    <a href="{{ route('bookmarks') }}" class="pf-tab" role="tab" style="text-decoration:none">Bookmarked</a>
                @endif
                <a href="{{ url('profile/' . $user->username . '/connection') }}" class="pf-tab" role="tab" style="text-decoration:none">Connections</a>
            </div>
        </div>
    </div>

    @error('avatarUpload')
        <div class="pf-alert pf-alert--error">{{ $message }}</div>
    @enderror
    @error('bannerUpload')
        <div class="pf-alert pf-alert--error">{{ $message }}</div>
    @enderror

    <div class="row pf-feed-wrap">
        <div class="col-md-8">
            @if (session()->has('success'))
                <div class="pf-alert pf-alert--success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

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
                    <h6>{{ $isOwner ? 'Your feed is waiting' : 'No posts yet' }}</h6>
                    <p>
                        {{ $isOwner
                            ? 'Share your first post — it can start earning the moment people engage.'
                            : displayName($user->name) . ' has not posted anything yet.' }}
                    </p>
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

    @if (auth()->user()->email_verified_at == null)
        @include('layouts.accesscode_verification')
    @else
        @include('layouts.onboarding')
    @endif

    <livewire:user.post-photo-viewer />
</div>
