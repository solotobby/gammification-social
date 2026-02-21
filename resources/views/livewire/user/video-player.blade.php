

<div>
    <p class="text-center text-muted">Video Player for video ID: {{ $videoId }}</p>
    {{-- Placeholder content - replace with actual video player UI --}}
    <div class="d-flex justify-content-center align-items-center" style="height: 300px; background-color: #f0f0f0; border-radius: 8px;">
        <span class="text-muted">[Video Player UI goes here]</span>
    </div>
</div>  



    
{{-- 

@props(['video', 'post'])

<div class="video-container position-relative" 
     x-data="videoPlayer('{{ $video->id }}')" 
     x-init="init()">
    
    @if ($video->processing_status === 'processing')
        {{-- Show processing state --
        <div class="video-processing text-center p-5 bg-dark rounded">
            <div class="spinner-border text-light mb-3" role="status">
                <span class="visually-hidden">Processing...</span>
            </div>
            <p class="text-light mb-0">Video is processing... Check back soon!</p>
        </div>
    @elseif ($video->processing_status === 'failed')
        {{-- Show error state --
        <div class="video-error text-center p-5 bg-danger rounded">
            <i class="fas fa-exclamation-triangle fa-3x text-white mb-3"></i>
            <p class="text-white mb-0">Video processing failed</p>
        </div>
    @else
        {{-- Video player --
        <div class="video-wrapper" @click="togglePlay()">
            <video 
                x-ref="video"
                class="w-100 rounded"
                :src="currentVideoUrl"
                poster="{{ $video->thumbnail_path }}"
                playsinline
                preload="metadata"
                @ended="onVideoEnd()"
                @timeupdate="onTimeUpdate()"
                @play="onPlay()"
                @pause="onPause()">
            </video>

            {{-- Play/Pause Overlay --
            <div class="video-overlay position-absolute top-50 start-50 translate-middle"
                 x-show="!isPlaying"
                 x-transition>
                <button class="btn btn-light btn-lg rounded-circle">
                    <i class="fas fa-play"></i>
                </button>
            </div>

            {{-- Video Controls --
            <div class="video-controls position-absolute bottom-0 start-0 end-0 p-3 bg-gradient"
                 x-show="showControls"
                 @mouseenter="showControls = true"
                 @mouseleave="showControls = false">
                
                {{-- Progress Bar --
                <div class="progress mb-2" style="height: 4px; cursor: pointer;" 
                     @click="seekTo($event)">
                    <div class="progress-bar bg-primary" 
                         :style="`width: ${progress}%`"></div>
                </div>

                <div class="d-flex align-items-center justify-content-between text-white">
                    {{-- Play/Pause --
                    <button class="btn btn-link text-white p-0" @click.stop="togglePlay()">
                        <i class="fas" :class="isPlaying ? 'fa-pause' : 'fa-play'"></i>
                    </button>

                    {{-- Time --
                    <span class="small" x-text="currentTime + ' / ' + duration"></span>

                    {{-- Quality Selector --
                    <div class="dropdown dropup">
                        <button class="btn btn-link text-white p-0 dropdown-toggle" 
                                type="button" 
                                data-bs-toggle="dropdown">
                            <i class="fas fa-cog"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Quality</h6></li>
                            <li>
                                <a class="dropdown-item" href="#" 
                                   @click.prevent="changeQuality('high')"
                                   :class="{ 'active': quality === 'high' }">
                                    High (1080p)
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" 
                                   @click.prevent="changeQuality('medium')"
                                   :class="{ 'active': quality === 'medium' }">
                                    Medium (720p)
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" 
                                   @click.prevent="changeQuality('low')"
                                   :class="{ 'active': quality === 'low' }">
                                    Low (480p)
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" 
                                   @click.prevent="changeQuality('auto')"
                                   :class="{ 'active': quality === 'auto' }">
                                    Auto
                                </a>
                            </li>
                        </ul>
                    </div>

                    {{-- Mute/Unmute --
                    <button class="btn btn-link text-white p-0" @click.stop="toggleMute()">
                        <i class="fas" :class="isMuted ? 'fa-volume-mute' : 'fa-volume-up'"></i>
                    </button>

                    {{-- Fullscreen --
                    <button class="btn btn-link text-white p-0" @click.stop="toggleFullscreen()">
                        <i class="fas fa-expand"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Video Info --
        <div class="video-info mt-2">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    <i class="fas fa-eye"></i> {{ number_format($video->view_count) }} views
                    • {{ $video->formatted_duration }}
                </small>
                <small class="text-muted">
                    {{ $video->formatted_file_size }}
                </small>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    function videoPlayer(videoId) {
        return {
            videoId: videoId,
            isPlaying: false,
            isMuted: false,
            progress: 0,
            currentTime: '0:00',
            duration: '0:00',
            showControls: false,
            quality: 'auto',
            currentVideoUrl: '',
            qualityUrls: @json([
                'high' => $video->getQualityUrl('high'),
                'medium' => $video->getQualityUrl('medium'),
                'low' => $video->getQualityUrl('low'),
                'auto' => $video->path,
            ]),
            watchStartTime: null,
            viewCounted: false,
            
            init() {
                // Detect network speed and set initial quality
                this.detectNetworkSpeed();
                
                // Set up intersection observer for autoplay
                this.setupIntersectionObserver();
            },
            
            detectNetworkSpeed() {
                const networkStrength = sessionStorage.getItem('networkStrength') || 'medium';
                
                if (this.quality === 'auto') {
                    const qualityMap = {
                        'slow': 'low',
                        '2g': 'low',
                        '3g': 'medium',
                        'medium': 'medium',
                        '4g': 'high',
                        '5g': 'high',
                    };
                    
                    const selectedQuality = qualityMap[networkStrength] || 'medium';
                    this.currentVideoUrl = this.qualityUrls[selectedQuality];
                } else {
                    this.currentVideoUrl = this.qualityUrls[this.quality];
                }
            },
            
            setupIntersectionObserver() {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting && entry.intersectionRatio > 0.5) {
                            // Video is more than 50% visible - count as view
                            if (!this.viewCounted) {
                                this.countView();
                                this.viewCounted = true;
                            }
                        } else {
                            // Video scrolled out of view - pause it
                            if (this.isPlaying) {
                                this.$refs.video.pause();
                            }
                        }
                    });
                }, { threshold: [0.5] });
                
                observer.observe(this.$el);
            },
            
            togglePlay() {
                if (this.isPlaying) {
                    this.$refs.video.pause();
                } else {
                    this.$refs.video.play();
                }
            },
            
            toggleMute() {
                this.$refs.video.muted = !this.$refs.video.muted;
                this.isMuted = this.$refs.video.muted;
            },
            
            toggleFullscreen() {
                if (this.$refs.video.requestFullscreen) {
                    this.$refs.video.requestFullscreen();
                } else if (this.$refs.video.webkitRequestFullscreen) {
                    this.$refs.video.webkitRequestFullscreen();
                }
            },
            
            seekTo(event) {
                const percent = event.offsetX / event.target.offsetWidth;
                this.$refs.video.currentTime = percent * this.$refs.video.duration;
            },
            
            changeQuality(newQuality) {
                const currentTime = this.$refs.video.currentTime;
                this.quality = newQuality;
                
                if (newQuality === 'auto') {
                    this.detectNetworkSpeed();
                } else {
                    this.currentVideoUrl = this.qualityUrls[newQuality];
                }
                
                // Preserve playback position
                this.$nextTick(() => {
                    this.$refs.video.currentTime = currentTime;
                    if (this.isPlaying) {
                        this.$refs.video.play();
                    }
                });
            },
            
            onPlay() {
                this.isPlaying = true;
                this.watchStartTime = Date.now();
                this.countPlay();
            },
            
            onPause() {
                this.isPlaying = false;
                this.trackWatchTime();
            },
            
            onTimeUpdate() {
                const video = this.$refs.video;
                this.progress = (video.currentTime / video.duration) * 100;
                this.currentTime = this.formatTime(video.currentTime);
                this.duration = this.formatTime(video.duration);
            },
            
            onVideoEnd() {
                this.isPlaying = false;
                this.trackWatchTime();
            },
            
            formatTime(seconds) {
                if (isNaN(seconds)) return '0:00';
                const mins = Math.floor(seconds / 60);
                const secs = Math.floor(seconds % 60);
                return `${mins}:${secs.toString().padStart(2, '0')}`;
            },
            
            countView() {
                fetch(`/api/videos/${this.videoId}/view`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
            },
            
            countPlay() {
                fetch(`/api/videos/${this.videoId}/play`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
            },
            
            trackWatchTime() {
                if (this.watchStartTime) {
                    const watchDuration = (Date.now() - this.watchStartTime) / 1000;
                    
                    fetch(`/api/videos/${this.videoId}/watch-time`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ watch_time: watchDuration })
                    });
                    
                    this.watchStartTime = null;
                }
            }
        };
    }
</script>

<style>
    .video-wrapper {
        position: relative;
        cursor: pointer;
    }
    
    .video-overlay {
        pointer-events: none;
    }
    
    .bg-gradient {
        background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
    }
    
    .video-controls {
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .video-wrapper:hover .video-controls {
        opacity: 1;
    }
</style>
@endpush --}}
