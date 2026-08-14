<div class="st-page">
    @php
        $defaultBanner = asset('src/assets/media/photos/photo19@2x.jpg');
        $defaultAvatar = asset('src/assets/media/avatars/avatar13.jpg');
        $avatarUrl = auth()->user()->avatar ?: $defaultAvatar;
        $bannerUrl = auth()->user()->banner ?: $defaultBanner;
    @endphp

    @verbatim
        <style>
            .st-page {
                --st-text: #050505;
                --st-muted: #65676B;
                --st-line: #CED0D4;
                --st-bg: #F0F2F5;
                --st-blue: #1877F2;
                --st-blue-hover: #166FE5;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                color: var(--st-text);
                margin: 0 -12px 16px;
            }

            @media (min-width: 992px) {
                .st-page { margin-inline: 0; }
            }

            .st-page * { box-sizing: border-box; }

            .st-shell {
                background: #fff;
                border: 1px solid #E4E6EB;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 1px 2px rgba(0, 0, 0, .08);
            }

            .st-cover {
                height: clamp(120px, 22vw, 180px);
                background-size: cover;
                background-position: center;
                background-color: #BCC0C4;
            }

            .st-head {
                padding: 0 16px 0;
                border-bottom: 1px solid #E4E6EB;
            }

            @media (min-width: 768px) {
                .st-head { padding-inline: 24px; }
            }

            .st-head-row {
                display: flex;
                flex-direction: column;
                gap: 12px;
                margin-top: -44px;
                padding-bottom: 12px;
            }

            @media (min-width: 768px) {
                .st-head-row {
                    flex-direction: row;
                    align-items: flex-end;
                    justify-content: space-between;
                }
            }

            .st-head-left {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }

            @media (min-width: 768px) {
                .st-head-left {
                    flex-direction: row;
                    align-items: flex-end;
                    gap: 16px;
                }
            }

            .st-avatar {
                width: 96px;
                height: 96px;
                border-radius: 50%;
                object-fit: cover;
                border: 4px solid #fff;
                box-shadow: 0 2px 8px rgba(0, 0, 0, .15);
                background: #E4E6EB;
            }

            .st-head-left .ua {
                --ua-size: 96px !important;
                width: 96px;
                height: 96px;
            }

            @media (min-width: 768px) {
                .st-avatar { width: 112px; height: 112px; margin-top: -8px; }
                .st-head-left .ua {
                    --ua-size: 112px !important;
                    width: 112px;
                    height: 112px;
                    margin-top: -8px;
                }
            }

            .st-title-block { text-align: center; }

            @media (min-width: 768px) {
                .st-title-block { text-align: left; padding-bottom: 8px; }
            }

            .st-title {
                margin: 0;
                font-size: clamp(1.35rem, 3vw, 1.75rem);
                font-weight: 700;
                line-height: 1.2;
            }

            .st-subtitle {
                margin: 4px 0 0;
                font-size: 15px;
                color: var(--st-muted);
            }

            .st-back {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 0 16px;
                min-height: 36px;
                border-radius: 6px;
                border: none;
                background: #E4E6EB;
                color: var(--st-text);
                font-size: 15px;
                font-weight: 600;
                text-decoration: none;
                font-family: inherit;
            }

            .st-back:hover { background: #D8DADF; color: var(--st-text); }

            .st-tabs {
                display: flex;
                border-bottom: 1px solid #E4E6EB;
            }

            .st-tab {
                flex: 1;
                border: none;
                background: none;
                padding: 14px 12px;
                font-family: inherit;
                font-size: 15px;
                font-weight: 600;
                color: var(--st-muted);
                cursor: pointer;
                position: relative;
                transition: background .15s ease;
            }

            .st-tab:hover { background: #F2F3F5; }

            .st-tab--active { color: var(--st-blue); }

            .st-tab--active::after {
                content: '';
                position: absolute;
                left: 0;
                right: 0;
                bottom: -1px;
                height: 3px;
                background: var(--st-blue);
                border-radius: 3px 3px 0 0;
            }

            .st-body { padding: 20px 16px 24px; }

            @media (min-width: 768px) {
                .st-body { padding: 24px 32px 32px; }
            }

            .st-layout {
                display: grid;
                gap: 20px;
            }

            @media (min-width: 900px) {
                .st-layout {
                    grid-template-columns: minmax(0, 280px) minmax(0, 1fr);
                    gap: 28px;
                    align-items: start;
                }
            }

            .st-aside {
                padding: 16px;
                border-radius: 8px;
                background: #F0F2F5;
                font-size: 14px;
                line-height: 1.55;
                color: var(--st-muted);
            }

            .st-aside strong {
                display: block;
                color: var(--st-text);
                margin-bottom: 6px;
                font-size: 15px;
            }

            .st-form { min-width: 0; }

            .st-field { margin-bottom: 18px; }

            .st-label {
                display: block;
                margin-bottom: 6px;
                font-size: 15px;
                font-weight: 600;
                color: var(--st-text);
            }

            .st-label small {
                font-weight: 400;
                color: var(--st-muted);
            }

            .st-input,
            .st-select,
            .st-textarea {
                width: 100%;
                padding: 11px 12px;
                border: 1px solid var(--st-line);
                border-radius: 8px;
                font-family: inherit;
                font-size: 15px;
                color: var(--st-text);
                background: #fff;
                transition: border-color .15s ease, box-shadow .15s ease;
            }

            .st-input:focus,
            .st-select:focus,
            .st-textarea:focus {
                outline: none;
                border-color: var(--st-blue);
                box-shadow: 0 0 0 3px rgba(24, 119, 242, .15);
            }

            .st-input:read-only,
            .st-input:disabled {
                background: #F0F2F5;
                color: var(--st-muted);
                cursor: not-allowed;
            }

            .st-textarea { resize: vertical; min-height: 88px; }

            .st-hint {
                display: block;
                margin-top: 6px;
                font-size: 13px;
                color: var(--st-muted);
            }

            .st-error {
                display: block;
                margin-top: 6px;
                font-size: 13px;
                color: #B42318;
            }

            .st-grid-2 {
                display: grid;
                gap: 16px;
            }

            @media (min-width: 600px) {
                .st-grid-2 { grid-template-columns: 1fr 1fr; }
            }

            .st-readonly-grid {
                display: grid;
                gap: 12px;
                margin-bottom: 20px;
                padding: 14px;
                border-radius: 8px;
                background: #F7F8FA;
                border: 1px solid #E4E6EB;
            }

            @media (min-width: 600px) {
                .st-readonly-grid { grid-template-columns: 1fr 1fr; }
            }

            .st-readonly-item span {
                display: block;
                font-size: 12px;
                color: var(--st-muted);
                margin-bottom: 2px;
                text-transform: uppercase;
                letter-spacing: .03em;
                font-weight: 600;
            }

            .st-readonly-item b {
                font-size: 15px;
                font-weight: 600;
            }

            .st-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                min-height: 40px;
                padding: 0 20px;
                border: none;
                border-radius: 6px;
                background: var(--st-blue);
                color: #fff;
                font-family: inherit;
                font-size: 15px;
                font-weight: 600;
                cursor: pointer;
                transition: background .15s ease;
            }

            .st-btn:hover { background: var(--st-blue-hover); }

            .st-btn:disabled { opacity: .6; cursor: not-allowed; }

            .st-alert {
                margin-bottom: 16px;
                padding: 12px 14px;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 600;
            }

            .st-alert--success {
                background: #E7F3FF;
                color: #1877F2;
                border: 1px solid #B6D4FE;
            }

            .st-social-icon {
                width: 18px;
                text-align: center;
                color: var(--st-muted);
            }
        </style>
    @endverbatim

    <div class="st-shell">
        <div class="st-cover" style="background-image:url('{{ $bannerUrl }}')"></div>

        <div class="st-head">
            <div class="st-head-row">
                <div class="st-head-left">
                    <x-user-avatar :user="auth()->user()" size="xl" :href="false" class="st-avatar-ua" />
                    <div class="st-title-block">
                        <h1 class="st-title">Settings</h1>
                        <p class="st-subtitle">{{ displayName($userName) }} · @<span>{{ $username }}</span></p>
                    </div>
                </div>
                <a href="{{ $profileUrl }}" class="st-back">
                    <i class="fa fa-arrow-left"></i> Back to profile
                </a>
            </div>

            <div class="st-tabs" role="tablist">
                <button type="button" role="tab"
                    class="st-tab {{ $activeTab === 'profile' ? 'st-tab--active' : '' }}"
                    wire:click="switchTab('profile')"
                    aria-selected="{{ $activeTab === 'profile' ? 'true' : 'false' }}">
                    Profile
                </button>
                <button type="button" role="tab"
                    class="st-tab {{ $activeTab === 'social' ? 'st-tab--active' : '' }}"
                    wire:click="switchTab('social')"
                    aria-selected="{{ $activeTab === 'social' ? 'true' : 'false' }}">
                    Social links
                </button>
            </div>
        </div>

        <div class="st-body">
            @if (session()->has('settings_success'))
                <div class="st-alert st-alert--success" role="alert">
                    {{ session('settings_success') }}
                </div>
            @endif

            @if ($activeTab === 'profile')
                <div class="st-layout">
                    <aside class="st-aside">
                        <strong>Public profile details</strong>
                        Your username appears on posts and your profile URL. You can change it once every 6 months.
                        Your short bio shows on your profile and in connection lists.
                    </aside>

                    <form class="st-form" wire:submit.prevent="updateProfile">
                        <div class="st-readonly-grid">
                            <div class="st-readonly-item">
                                <span>Name</span>
                                <b>{{ displayName($userName) }}</b>
                            </div>
                            <div class="st-readonly-item">
                                <span>Email</span>
                                <b>{{ $userEmail }}</b>
                            </div>
                            <div class="st-readonly-item">
                                <span>Level</span>
                                <b>{{ $userLevel }}</b>
                            </div>
                            <div class="st-readonly-item">
                                <span>Referral code</span>
                                <b>{{ $referralCode }}</b>
                            </div>
                        </div>

                        <div class="st-field">
                            <label class="st-label" for="st-username">
                                Username
                                @unless ($canEditUsername)
                                    <small>(locked until {{ $usernameNextEditDate }})</small>
                                @endunless
                            </label>
                            <input id="st-username" type="text" class="st-input" wire:model.defer="username"
                                @disabled(! $canEditUsername) placeholder="your_username">
                            @error('username') <span class="st-error">{{ $message }}</span> @enderror
                            @unless ($canEditUsername)
                                <span class="st-hint">Usernames can be changed once every 6 months.</span>
                            @endunless
                        </div>

                        <div class="st-field">
                            <label class="st-label" for="st-about">Bio <small>(40 characters max)</small></label>
                            <textarea id="st-about" class="st-textarea" wire:model.live="about" maxlength="40" rows="2"
                                placeholder="Tell people a little about you"></textarea>
                            <span class="st-hint">{{ strlen($about) }}/40</span>
                            @error('about') <span class="st-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="st-grid-2">
                            <div class="st-field">
                                <label class="st-label" for="st-dob">Date of birth</label>
                                <input id="st-dob" type="date" class="st-input" wire:model.defer="date_of_birth"
                                    max="{{ now()->subYears(13)->toDateString() }}">
                                @error('date_of_birth') <span class="st-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="st-field">
                                <label class="st-label" for="st-gender">Gender</label>
                                <select id="st-gender" class="st-select" wire:model.defer="gender">
                                    <option value="">Select gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                                @error('gender') <span class="st-error">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="st-field">
                            <label class="st-label" for="st-location">Location</label>
                            <input id="st-location" type="text" class="st-input" wire:model.defer="location"
                                maxlength="50" placeholder="City, country">
                            @error('location') <span class="st-error">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="st-btn" wire:loading.attr="disabled" wire:target="updateProfile">
                            <span wire:loading.remove wire:target="updateProfile">
                                <i class="fa fa-check"></i> Save profile
                            </span>
                            <span wire:loading wire:target="updateProfile">Saving…</span>
                        </button>
                    </form>
                </div>
            @else
                <div class="st-layout">
                    <aside class="st-aside">
                        <strong>Social handles</strong>
                        Add your usernames or profile links so people can find you elsewhere. Only paste handles or full URLs — no passwords.
                    </aside>

                    <form class="st-form" wire:submit.prevent="updateSocial">
                        <div class="st-field">
                            <label class="st-label" for="st-facebook"><i class="fa fa-facebook st-social-icon"></i> Facebook</label>
                            <input id="st-facebook" type="text" class="st-input" wire:model.defer="facebook"
                                placeholder="facebook.com/your.page">
                            @error('facebook') <span class="st-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="st-field">
                            <label class="st-label" for="st-instagram"><i class="fa fa-instagram st-social-icon"></i> Instagram</label>
                            <input id="st-instagram" type="text" class="st-input" wire:model.defer="instagram"
                                placeholder="@username">
                            @error('instagram') <span class="st-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="st-field">
                            <label class="st-label" for="st-tiktok"><i class="fa fa-music st-social-icon"></i> TikTok</label>
                            <input id="st-tiktok" type="text" class="st-input" wire:model.defer="tiktok"
                                placeholder="@username">
                            @error('tiktok') <span class="st-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="st-field">
                            <label class="st-label" for="st-twitter"><i class="fa fa-twitter st-social-icon"></i> X (Twitter)</label>
                            <input id="st-twitter" type="text" class="st-input" wire:model.defer="twitter"
                                placeholder="@username">
                            @error('twitter') <span class="st-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="st-grid-2">
                            <div class="st-field">
                                <label class="st-label" for="st-linkedin"><i class="fa fa-linkedin st-social-icon"></i> LinkedIn</label>
                                <input id="st-linkedin" type="text" class="st-input" wire:model.defer="linkedin"
                                    placeholder="linkedin.com/in/you">
                                @error('linkedin') <span class="st-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="st-field">
                                <label class="st-label" for="st-pinterest"><i class="fa fa-pinterest st-social-icon"></i> Pinterest</label>
                                <input id="st-pinterest" type="text" class="st-input" wire:model.defer="pinterest"
                                    placeholder="@username">
                                @error('pinterest') <span class="st-error">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <button type="submit" class="st-btn" wire:loading.attr="disabled" wire:target="updateSocial">
                            <span wire:loading.remove wire:target="updateSocial">
                                <i class="fa fa-check"></i> Save social links
                            </span>
                            <span wire:loading wire:target="updateSocial">Saving…</span>
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    @if (auth()->user()->email_verified_at == null)
        @include('layouts.accesscode_verification')
    @else
        @include('layouts.onboarding')
    @endif
</div>
