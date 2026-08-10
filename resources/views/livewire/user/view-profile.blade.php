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
        $profileViews = sumCounter($user->profile_views ?? 0, $user->profile_views_external ?? 0);
    @endphp

    @verbatim
        <style>
            .pf-page {
                --pf-blue: #1877F2;
                --pf-blue-hover: #166FE5;
                --pf-text: #050505;
                --pf-muted: #65676B;
                --pf-line: #CED0D4;
                --pf-bg: #F0F2F5;
                --pf-white: #FFFFFF;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                color: var(--pf-text);
                margin: 0 -12px 16px;
            }

            @media (min-width: 992px) {
                .pf-page { margin-inline: 0; }
            }

            .pf-page * { box-sizing: border-box; }

            /* ---- cover photo ---- */
            .pf-cover-wrap {
                background: var(--pf-white);
                box-shadow: 0 1px 2px rgba(0, 0, 0, .1);
                border-radius: 0 0 8px 8px;
                overflow: hidden;
            }

            .pf-cover {
                position: relative;
                height: clamp(180px, 35vw, 350px);
                background-color: #BCC0C4;
                background-size: cover;
                background-position: center;
            }

            .pf-cover-edit {
                position: absolute;
                right: 16px;
                bottom: 16px;
                z-index: 2;
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
                font-size: 15px;
                font-weight: 600;
                cursor: pointer;
                box-shadow: 0 1px 2px rgba(0, 0, 0, .15);
                font-family: inherit;
            }

            .pf-cover-edit label:hover,
            .pf-cover-edit button:hover {
                background: #fff;
            }

            .pf-cover-edit button.pf-cover-remove {
                margin-left: 8px;
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
                padding: 0 16px 16px;
                background: var(--pf-white);
            }

            @media (min-width: 768px) {
                .pf-head { padding: 0 32px 16px; }
            }

            .pf-head-row {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 12px;
            }

            @media (min-width: 900px) {
                .pf-head-row {
                    flex-direction: row;
                    align-items: flex-end;
                    justify-content: space-between;
                    min-height: 36px;
                }
            }

            .pf-head-left {
                display: flex;
                flex-direction: column;
                align-items: center;
                width: 100%;
            }

            @media (min-width: 900px) {
                .pf-head-left {
                    flex-direction: row;
                    align-items: flex-end;
                    gap: 20px;
                    width: auto;
                    flex: 1;
                    min-width: 0;
                }
            }

            .pf-avatar-wrap {
                position: relative;
                margin-top: -64px;
                flex-shrink: 0;
            }

            @media (min-width: 900px) {
                .pf-avatar-wrap { margin-top: -84px; }
            }

            .pf-avatar-ring {
                padding: 4px;
                border-radius: 50%;
                background: var(--pf-white);
                display: inline-block;
            }

            .pf-avatar-ring--influencer {
                background: linear-gradient(135deg, #1877F2, #5A4FDC);
                padding: 5px;
            }

            .pf-avatar {
                width: clamp(120px, 22vw, 168px);
                height: clamp(120px, 22vw, 168px);
                border-radius: 50%;
                object-fit: cover;
                display: block;
                border: 4px solid var(--pf-white);
                background: #E4E6EB;
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
            }

            .pf-avatar-wrap:hover .pf-avatar-edit {
                background: rgba(0, 0, 0, .45);
            }

            .pf-avatar-edit i {
                color: #fff;
                font-size: 28px;
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
                z-index: 2;
            }

            .pf-identity {
                text-align: center;
                min-width: 0;
                padding-bottom: 4px;
            }

            @media (min-width: 900px) {
                .pf-identity { text-align: left; }
            }

            .pf-name {
                font-size: clamp(1.5rem, 4vw, 2rem);
                font-weight: 700;
                line-height: 1.2;
                margin: 0 0 4px;
                color: var(--pf-text);
            }

            .pf-name-row {
                display: inline-flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 6px;
                justify-content: center;
            }

            @media (min-width: 900px) {
                .pf-name-row { justify-content: flex-start; }
            }

            .pf-subline {
                font-size: 15px;
                color: var(--pf-muted);
                margin: 0 0 8px;
                line-height: 1.4;
            }

            .pf-subline a {
                color: var(--pf-muted);
                text-decoration: none;
                font-weight: 600;
            }

            .pf-subline a:hover { text-decoration: underline; }

            .pf-dot { margin: 0 4px; }

            .pf-meta-inline {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                justify-content: center;
                font-size: 15px;
                color: var(--pf-muted);
                margin-top: 6px;
            }

            @media (min-width: 900px) {
                .pf-meta-inline { justify-content: flex-start; }
            }

            .pf-meta-inline span {
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }

            .pf-head-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                justify-content: center;
                width: 100%;
                padding-top: 4px;
            }

            @media (min-width: 900px) {
                .pf-head-actions {
                    width: auto;
                    justify-content: flex-end;
                    padding-bottom: 8px;
                }
            }

            .pf-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                padding: 0 16px;
                min-height: 36px;
                border-radius: 6px;
                border: none;
                font-family: inherit;
                font-size: 15px;
                font-weight: 600;
                cursor: pointer;
                text-decoration: none;
                transition: background .15s ease;
            }

            .pf-btn--primary {
                background: var(--pf-blue);
                color: #fff;
            }

            .pf-btn--primary:hover {
                background: var(--pf-blue-hover);
                color: #fff;
            }

            .pf-btn--secondary {
                background: #E4E6EB;
                color: var(--pf-text);
            }

            .pf-btn--secondary:hover { background: #D8DADF; color: var(--pf-text); }

            .pf-btn--following {
                background: #E4E6EB;
                color: var(--pf-text);
            }

            /* ---- tabs (FB-style) ---- */
            .pf-tabs {
                display: flex;
                gap: 4px;
                border-top: 1px solid var(--pf-line);
                margin-top: 12px;
                padding-top: 0;
                overflow-x: auto;
            }

            .pf-tab {
                padding: 14px 16px;
                font-size: 15px;
                font-weight: 600;
                color: var(--pf-muted);
                border: none;
                background: none;
                border-bottom: 3px solid transparent;
                cursor: default;
                white-space: nowrap;
                font-family: inherit;
            }

            .pf-tab--active {
                color: var(--pf-blue);
                border-bottom-color: var(--pf-blue);
            }

            .pf-referral {
                margin-top: 10px;
                font-size: 13px;
                color: var(--pf-muted);
                word-break: break-all;
            }

            .pf-referral i { margin-right: 4px; }

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

    <div class="pf-cover-wrap">
        {{-- Cover photo --}}
        <div class="pf-cover" style="background-image:url('{{ $bannerUrl }}')">
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
                        <span @class(['pf-avatar-ring', 'pf-avatar-ring--influencer' => $isInfluencer])>
                            <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="pf-avatar">
                        </span>

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
                            <h1 class="pf-name">{{ displayName($user->name) }}</h1>
                            @if ($isVerified)
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="#1877F2" aria-label="Verified">
                                    <path d="M22.5 5.5l-12 12-5.5-5.5 1.5-1.5 4 4 10.5-10.5z"/>
                                </svg>
                            @endif
                        </div>

                        <p class="pf-subline">
                            <a href="{{ url('profile/' . $user->username . '/connection?tab=followers') }}">{{ number_format($user->followers) }} followers</a>
                            <span class="pf-dot">·</span>
                            <a href="{{ url('profile/' . $user->username . '/connection?tab=following') }}">{{ number_format($user->following) }} following</a>
                            <span class="pf-dot">·</span>
                            <span>{{ number_format($totalLikes) }} likes</span>
                            <span class="pf-dot">·</span>
                            <span>{{ number_format($profileViews) }} profile views</span>
                        </p>

                        <p class="pf-subline" style="margin-bottom:0">
                            <strong>{{ $level }}</strong> account
                            @if ($isVerified)
                                <span class="pf-dot">·</span> Verified creator
                            @endif
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

                        @if ($isOwner)
                            <div class="pf-referral">
                                <i class="fa fa-share"></i>
                                {{ url('/reg?referral_code=' . auth()->user()->referral_code) }}
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
                <livewire:user.post-content :post="$post" :wire:key="'post-' . $post->id" />
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
        </div>

        @include('layouts.engagement')
    </div>

    @if (auth()->user()->email_verified_at == null)
        @include('layouts.accesscode_verification')
    @else
        @include('layouts.onboarding')
    @endif
</div>
