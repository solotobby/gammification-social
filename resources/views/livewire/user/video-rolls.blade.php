{{-- resources/views/livewire/video-reels.blade.php --}}

<div x-data="videoReelsPlayer()" 
     x-init="init()"
     @keydown.arrow-up.window.prevent="handlePrevious()"
     @keydown.arrow-down.window.prevent="handleNext()"
     @keydown.space.window.prevent="togglePlayPause()"
     class="reels-container">
    
    @if($currentVideo)
        {{-- Top Navigation Bar --}}
        <div class="reels-nav-bar">
            <a href="{{ url()->previous() }}" class="nav-back-btn" @click.stop>
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="nav-context">
                <i class="fas fa-video me-2"></i>
                @switch($context)
                    @case('user')
                        {{ $currentVideo['user']['name'] }}'s Videos
                        @break
                    @case('following')
                        Following
                        @break
                    @case('trending')
                        Trending
                        @break
                    @default
                        Reels
                @endswitch
            </div>
            <div class="nav-actions">
                <button class="nav-action-btn" 
                        @click.prevent.stop="shareVideo()"
                        type="button">
                    <i class="fas fa-share-alt"></i>
                </button>
            </div>
        </div>

        {{-- Main Video Container --}}
        <div class="reels-video-container">
            {{-- Video Wrapper - ONLY responds to direct clicks --}}
            <div class="video-wrapper" @click="togglePlayPause()">
                <video 
                    x-ref="videoElement"
                    class="reels-video"
                    loop
                    playsinline
                    preload="auto"
                    @play="onPlay()"
                    @pause="onPause()"
                    @ended="onEnded()"
                    @timeupdate="onTimeUpdate()"
                    @loadstart="onLoadStart()"
                    @canplay="onCanPlay()">
                    <source src="{{ $currentVideo['path'] }}" type="video/mp4">
                </video>

                {{-- Play/Pause Indicator - Non-interactive --}}
                <div class="play-pause-indicator" 
                     x-show="showPlayPause" 
                     x-transition.opacity
                     style="pointer-events: none;">
                    <i class="fas" :class="isPlaying ? 'fa-pause' : 'fa-play'"></i>
                </div>

                {{-- Loading Spinner - Non-interactive --}}
                <div class="video-loading" 
                     x-show="isLoading" 
                     x-cloak
                     style="pointer-events: none;">
                    <div class="spinner-border text-light" role="status"></div>
                </div>
            </div>

            {{-- Previous Video Button --}}
            @if($currentIndex > 0)
                <button class="reels-nav-btn reels-nav-prev" 
                        @click.prevent.stop="handlePrevious()"
                        type="button"
                        title="Previous video (↑)">
                    <i class="fas fa-chevron-up"></i>
                </button>
            @endif

            {{-- Next Video Button --}}
            <button class="reels-nav-btn reels-nav-next" 
                    @click.prevent.stop="handleNext()"
                    type="button"
                    title="Next video (↓)">
                <i class="fas fa-chevron-down"></i>
            </button>

            {{-- Video Info Overlay (Bottom Left) - Non-interactive --}}
            <div class="reels-info-overlay" style="pointer-events: none;">
                <div style="pointer-events: auto;">
                    {{-- User Info --}}
                    <div class="user-info-overlay mb-2">
                        <img src="{{ $currentVideo['user']['avatar'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($currentVideo['user']['name']) }}" 
                             alt="{{ $currentVideo['user']['name'] }}"
                             class="user-avatar"
                             width="40"
                             height="40">
                        <span class="user-name">{{ $currentVideo['user']['name'] }}</span>
                        @if(Auth::check() && Auth::id() !== $currentVideo['user_id'])
                            <button class="btn-follow" 
                                    @click.prevent.stop="followUser()"
                                    type="button">
                                <i class="fas fa-plus"></i>
                            </button>
                        @endif
                    </div>

                    {{-- Video Caption --}}
                    <div class="video-caption mb-2">
                        {{ $currentVideo['post']['content'] ?? '' }}
                    </div>

                    {{-- Video Stats --}}
                    <div class="video-meta">
                        <span><i class="fas fa-eye"></i> {{ $this->formatCount($currentVideo['view_count']) }}</span>
                        <span class="mx-2">•</span>
                        <span>{{ \Carbon\Carbon::parse($currentVideo['created_at'])->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            {{-- Action Buttons (Bottom Right) - Each button is interactive --}}
            <div class="reels-actions-overlay" style="pointer-events: none;">
                <div style="pointer-events: auto; display: flex; flex-direction: column; gap: 20px;">
                    {{-- Like --}}
                    <button class="action-btn" 
                            @click.prevent.stop="toggleLike()"
                            type="button">
                        <i class="fas fa-heart" :class="{ 'text-danger': isLiked }"></i>
                        <span class="action-count">{{ $currentVideo['post']['likes_count'] ?? 0 }}</span>
                    </button>

                    {{-- Comment --}}
                    <button class="action-btn" 
                            @click.prevent.stop="openComments()"
                            type="button">
                        <i class="fas fa-comment"></i>
                        <span class="action-count">{{ $currentVideo['post']['comments_count'] ?? 0 }}</span>
                    </button>

                    {{-- Share --}}
                    <button class="action-btn" 
                            @click.prevent.stop="shareVideo()"
                            type="button">
                        <i class="fas fa-share-alt"></i>
                        <span class="action-count">Share</span>
                    </button>

                    {{-- Sound Toggle --}}
                    <button class="action-btn" 
                            @click.prevent.stop="toggleMute()"
                            type="button"
                            title="Toggle sound">
                        <i class="fas" :class="isMuted ? 'fa-volume-mute' : 'fa-volume-up'"></i>
                        <span class="action-count" x-text="isMuted ? 'Muted' : 'Sound'"></span>
                    </button>

                    {{-- More Options --}}
                    <button class="action-btn" 
                            @click.prevent.stop="showOptions()"
                            type="button">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div class="video-progress-bar" style="pointer-events: none;">
                <div class="progress-fill" :style="`width: ${progress}%`"></div>
            </div>
        </div>
    @else
        {{-- Video Not Found --}}
        <div class="reels-not-found">
            <i class="fas fa-video-slash fa-4x mb-3"></i>
            <h4>Video not available</h4>
            <p class="text-muted">This video may have been removed</p>
            <a href="{{ route('home') }}" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>Go Home
            </a>
        </div>
    @endif
</div>

@push('styles')
<style>
    [x-cloak] { 
        display: none !important; 
    }

    * {
        -webkit-tap-highlight-color: transparent;
        box-sizing: border-box;
    }

    body {
        margin: 0;
        padding: 0;
        overflow: hidden;
        touch-action: pan-y;
    }

    .reels-container {
        width: 100vw;
        height: 100vh;
        background: #000;
        position: relative;
        display: flex;
        flex-direction: column;
    }

    /* Navigation Bar */
    .reels-nav-bar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 60px;
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 20px;
        z-index: 1000;
    }

    .nav-back-btn {
        color: white;
        font-size: 20px;
        text-decoration: none;
        padding: 10px;
        cursor: pointer;
    }

    .nav-context {
        color: white;
        font-weight: 600;
        font-size: 16px;
    }

    .nav-action-btn {
        background: none;
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
        padding: 10px;
    }

    /* Video Container */
    .reels-video-container {
        flex: 1;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 60px;
    }

    .video-wrapper {
        width: 100%;
        height: 100%;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .reels-video {
        width: 100%;
        height: 100%;
        object-fit: contain;
        max-height: calc(100vh - 60px);
        pointer-events: none;
    }

    /* Play/Pause Indicator */
    .play-pause-indicator {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 80px;
        height: 80px;
        background: rgba(0, 0, 0, 0.7);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 32px;
        z-index: 50;
    }

    /* Loading */
    .video-loading {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 50;
    }

    /* Navigation Buttons */
    .reels-nav-btn {
        position: absolute;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
        z-index: 100;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .reels-nav-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.1);
    }

    .reels-nav-btn:active {
        transform: scale(0.95);
    }

    .reels-nav-prev {
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
    }

    .reels-nav-prev:hover {
        transform: translateX(-50%) scale(1.1);
    }

    .reels-nav-next {
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
    }

    .reels-nav-next:hover {
        transform: translateX(-50%) scale(1.1);
    }

    /* Info Overlay */
    .reels-info-overlay {
        position: absolute;
        bottom: 20px;
        left: 20px;
        max-width: 70%;
        color: white;
        text-shadow: 0 1px 4px rgba(0, 0, 0, 0.8);
        z-index: 90;
    }

    .user-info-overlay {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .user-avatar {
        border-radius: 50%;
        border: 2px solid white;
    }

    .user-name {
        font-weight: 600;
        font-size: 16px;
    }

    .btn-follow {
        background: #ff2d55;
        border: none;
        color: white;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 14px;
    }

    .video-caption {
        font-size: 14px;
        line-height: 1.4;
    }

    .video-meta {
        font-size: 12px;
        opacity: 0.9;
    }

    /* Actions Overlay */
    .reels-actions-overlay {
        position: absolute;
        bottom: 80px;
        right: 20px;
        z-index: 90;
    }

    .action-btn {
        background: none;
        border: none;
        color: white;
        font-size: 28px;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        text-shadow: 0 1px 4px rgba(0, 0, 0, 0.8);
        transition: transform 0.2s;
        padding: 10px;
        user-select: none;
        -webkit-user-select: none;
    }

    .action-btn:hover {
        transform: scale(1.1);
    }

    .action-btn:active {
        transform: scale(0.9);
    }

    .action-count {
        font-size: 12px;
        font-weight: 600;
    }

    /* Progress Bar */
    .video-progress-bar {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: rgba(255, 255, 255, 0.3);
        z-index: 100;
    }

    .progress-fill {
        height: 100%;
        background: white;
        transition: width 0.1s linear;
    }

    /* Not Found */
    .reels-not-found {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100vh;
        text-align: center;
        color: white;
    }

    /* Mobile Adjustments */
    @media (max-width: 768px) {
        .reels-nav-bar {
            height: 50px;
            padding: 0 15px;
        }

        .nav-context {
            font-size: 14px;
        }

        .reels-video-container {
            margin-top: 50px;
        }

        .reels-video {
            max-height: calc(100vh - 50px);
        }

        .reels-nav-btn {
            width: 40px;
            height: 40px;
            font-size: 16px;
        }

        .action-btn {
            font-size: 24px;
        }

        .reels-info-overlay {
            bottom: 10px;
            left: 10px;
            max-width: 65%;
        }

        .reels-actions-overlay {
            bottom: 60px;
            right: 10px;
        }
    }

    /* Desktop */
    @media (min-width: 769px) {
        .reels-video {
            max-width: 500px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function videoReelsPlayer() {
        return {
            isPlaying: false,
            isMuted: false,
            isLiked: false,
            isLoading: true,
            showPlayPause: false,
            progress: 0,
            watchStartTime: null,
            playPauseTimeout: null,
            isToggling: false,
            currentVideoId: @js($videoId),

            init() {
                console.log('🎬 Video Reels Player Initialized');
                console.log('📹 Current Video ID:', this.currentVideoId);
                
                this.$nextTick(() => {
                    this.loadVideo();
                });

                this.$wire.recordView();
                this.setupEventListeners();
            },

            setupEventListeners() {
                console.log('🎧 Setting up event listeners...');
                
                Livewire.on('videoChanged', (event) => {
                    console.log('🔄 Video changed event received', event);
                    this.currentVideoId = event.videoId;
                    this.loadVideo();
                });

                Livewire.on('updateBrowserUrl', (event) => {
                    console.log('🔗 Updating URL', event);
                    window.history.pushState({}, '', event.url);
                });
            },

            loadVideo() {
                console.log('⏳ Loading video ID:', this.currentVideoId);
                this.isLoading = true;
                this.progress = 0;
                this.isLiked = false;
                
                this.$nextTick(() => {
                    const video = this.$refs.videoElement;
                    if (video) {
                        console.log('✅ Video element found');
                        video.load();
                        
                        // Try to play, but don't worry if autoplay is blocked
                        const playPromise = video.play();
                        
                        if (playPromise !== undefined) {
                            playPromise
                                .then(() => {
                                    console.log('▶️ Video playing successfully');
                                    this.isPlaying = true;
                                    this.isLoading = false;
                                })
                                .catch((error) => {
                                    console.log('⚠️ Autoplay prevented (user needs to click play)');
                                    this.isPlaying = false;
                                    this.isLoading = false;
                                });
                        }
                    } else {
                        console.error('❌ Video element not found!');
                    }
                });
            },

            togglePlayPause(event) {
                // CRITICAL: Prevent ANY duplicate calls
                if (this.isToggling) {
                    console.log('🚫 BLOCKED: Already toggling');
                    return;
                }

                this.isToggling = true;
                console.log('🎮 togglePlayPause - LOCKED');
                
                const video = this.$refs.videoElement;
                if (!video) {
                    console.error('❌ No video element');
                    this.isToggling = false;
                    return;
                }

                const currentlyPaused = video.paused;
                console.log('📊 Video state: paused=' + currentlyPaused);

                if (currentlyPaused) {
                    console.log('▶️ PLAY');
                    video.play()
                        .then(() => {
                            console.log('✅ NOW PLAYING');
                            this.isPlaying = true;
                            setTimeout(() => {
                                this.isToggling = false;
                                console.log('🔓 UNLOCKED after play');
                            }, 500);
                        })
                        .catch(err => {
                            console.error('❌ Play failed:', err);
                            this.isToggling = false;
                        });
                } else {
                    console.log('⏸️ PAUSE');
                    video.pause();
                    this.isPlaying = false;
                    console.log('✅ NOW PAUSED');
                    setTimeout(() => {
                        this.isToggling = false;
                        console.log('🔓 UNLOCKED after pause');
                    }, 500);
                }

                // Show indicator
                this.showPlayPause = true;
                clearTimeout(this.playPauseTimeout);
                this.playPauseTimeout = setTimeout(() => {
                    this.showPlayPause = false;
                }, 500);
            },

            toggleMute() {
                const video = this.$refs.videoElement;
                if (!video) {
                    console.error('❌ Video element not found in toggleMute');
                    return;
                }

                console.log('🔊 Mute button clicked');
                console.log('📊 Before - Muted:', video.muted);
                
                video.muted = !video.muted;
                this.isMuted = video.muted;
                
                console.log('📊 After - Muted:', this.isMuted);
                console.log(this.isMuted ? '🔇 Sound MUTED' : '🔊 Sound UNMUTED');
                
                // Show visual feedback
                this.showPlayPause = true;
                clearTimeout(this.playPauseTimeout);
                this.playPauseTimeout = setTimeout(() => {
                    this.showPlayPause = false;
                }, 500);
            },

            handleNext() {
                console.log('⏭️ Next video clicked');
                this.trackWatchTime();
                this.$wire.nextVideo();
            },

            handlePrevious() {
                console.log('⏮️ Previous video clicked');
                this.trackWatchTime();
                this.$wire.previousVideo();
            },

            onLoadStart() {
                console.log('⏳ Video load started');
                this.isLoading = true;
            },

            onCanPlay() {
                console.log('✅ Video can play');
                this.isLoading = false;
            },

            recordPlay() {
                fetch(`/api/videos/${this.currentVideoId}/record-play`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).catch(error => console.log('⚠️ Play recording failed:', error));
            },

            onPlay() {
                console.log('▶️ Video play event fired');
                this.isPlaying = true;
                this.watchStartTime = Date.now();
                this.recordPlay();
            },

            onPause() {
                console.log('⏸️ Video pause event fired');
                this.isPlaying = false;
                this.trackWatchTime();
            },

            onEnded() {
                console.log('⏹️ Video ended');
                this.handleNext();
            },

            onTimeUpdate() {
                const video = this.$refs.videoElement;
                if (video && video.duration) {
                    this.progress = (video.currentTime / video.duration) * 100;
                }
            },

            trackWatchTime() {
                if (this.watchStartTime) {
                    const watchDuration = (Date.now() - this.watchStartTime) / 1000;
                    console.log('⏱️ Watch time:', watchDuration, 'seconds');
                    
                    fetch(`/api/videos/${this.currentVideoId}/watch-time`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ watch_time: watchDuration })
                    }).catch(error => console.log('⚠️ Watch time tracking failed:', error));
                    
                    this.watchStartTime = null;
                }
            },

            toggleLike() {
                this.isLiked = !this.isLiked;
                console.log(this.isLiked ? '❤️ Liked' : '🤍 Unliked');
                
                fetch(`/api/videos/${this.currentVideoId}/like`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).catch(error => console.log('⚠️ Like failed:', error));
            },

            followUser() {
                console.log('👤 Follow button clicked');
                alert('Follow functionality - implement as needed');
            },

            openComments() {
                console.log('💬 Comments button clicked');
                alert('Comments - implement as needed');
            },

            shareVideo() {
                console.log('📤 Share button clicked');
                const url = window.location.href;
                
                if (navigator.share) {
                    navigator.share({
                        title: 'Check out this video!',
                        url: url
                    }).catch(error => console.log('⚠️ Share failed:', error));
                } else {
                    navigator.clipboard.writeText(url)
                        .then(() => alert('✅ Link copied!'))
                        .catch(error => console.log('⚠️ Copy failed:', error));
                }
            },

            showOptions() {
                console.log('⚙️ Options button clicked');
                alert('Options - implement as needed');
            }
        };
    }
</script>
@endpush