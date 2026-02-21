<div>

    <div>

        <div class="card shadow-sm border-0 mb-4">
            <form wire:submit.prevent="createPost">
                <div class="card-body">

                    @php $userLevel = userLevel(); @endphp

                    {{-- TEXT AREA ALWAYS VISIBLE --}}
                    <div class="mb-3">
                        <textarea wire:model.defer="content" class="form-control border-0 shadow-none" rows="4"
                            placeholder="What’s happening on Payhankey?" style="font-size:16px; resize:none;"
                            @if (!in_array($userLevel, ['Creator', 'Influencer'])) maxlength="160" @endif required></textarea>

                        @if (!in_array($userLevel, ['Creator', 'Influencer']))
                            <div class="text-end">
                                <small class="text-muted">
                                    Max 160 characters
                                </small>
                            </div>
                        @endif
                    </div>

                    {{-- MEDIA TABS --}}
                    @if (in_array($userLevel, ['Creator', 'Influencer']))
                        <ul class="nav nav-pills nav-justified mb-3 small fw-semibold">

                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#imageTab"
                                    type="button">
                                    <i class="fa fa-image me-1"></i> Images
                                </button>
                            </li>

                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#videoTab" type="button">
                                    <i class="fa fa-video me-1"></i> Video
                                </button>
                            </li>

                        </ul>

                        <div class="tab-content">

                            {{-- IMAGE TAB --}}
                            <div class="tab-pane fade show active" id="imageTab">

                                <div class="mb-3">
                                    <input type="file" wire:model="images" multiple accept="image/*"
                                        class="form-control">

                                    <small class="text-muted d-block mt-1">
                                        {{ $userLevel === 'Creator' ? 'Max 1 image' : 'Max 4 images' }}
                                    </small>
                                </div>

                                <div class="row">
                                    @foreach ($images as $index => $image)
                                        <div class="col-4 col-md-3 mb-2 position-relative">

                                            <img src="{{ $image->temporaryUrl() }}" class="img-fluid rounded shadow-sm">

                                            <button type="button" wire:click="removeImage({{ $index }})"
                                                class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1">
                                                &times;
                                            </button>

                                        </div>
                                    @endforeach
                                </div>

                            </div>

                            {{-- VIDEO TAB --}}
                            {{-- <div class="tab-pane fade" id="videoTab" wire:ignore>

                                <div class="d-grid">
                                    <button type="button" id="video_upload_btn" class="btn btn-outline-primary">
                                        <i class="fa fa-upload me-1"></i> Upload Video
                                    </button>
                                </div>

                                @if ($videoData)
                                    <div class="mt-3">
                                        <video class="w-100 rounded shadow-sm" controls>
                                            <source src="{{ $videoData['secure_url'] }}" type="video/mp4">
                                        </video>
                                    </div>
                                @endif

                            </div> --}}

                            {{-- VIDEO TAB --}}
                            <div class="tab-pane fade" id="videoTab">

                                {{-- Hidden File Input --}}
                                <input type="file" id="videoInput" wire:model="video" accept="video/*"
                                    class="d-none">

                                <div class="d-grid">
                                    <button type="button" class="btn btn-outline-primary"
                                        onclick="document.getElementById('videoInput').click();">
                                        <i class="fa fa-upload me-1"></i> Upload Video
                                    </button>
                                </div>

                                {{-- Upload Loading Indicator --}}
                                <div wire:loading wire:target="video" class="text-center mt-2">
                                    <small class="text-muted">Uploading video...</small>
                                </div>

                                {{-- Preview Before Cloudinary --}}
                                @if ($video)
                                    <div class="mt-3">
                                        <video class="w-100 rounded shadow-sm" controls>
                                            <source src="{{ $video->temporaryUrl() }}">
                                        </video>

                                        <button type="button" wire:click="$set('video', null)"
                                            class="btn btn-sm btn-danger mt-2">
                                            Remove Video
                                        </button>
                                    </div>
                                @endif

                                 

                                {{-- Preview After Cloudinary Upload --}}
                                @if ($videoData)
                                    <div class="mt-3">
                                        <video class="w-100 rounded shadow-sm" controls>
                                            <source src="{{ $videoData['secure_url'] }}" type="video/mp4">
                                        </video>
                                    </div>
                                @endif

                            </div>


                        </div>
                    @endif

                </div>

                {{-- FOOTER --}}
                <div class="card-footer bg-white border-0">
                    <button class="btn btn-primary w-100">
                        <i class="fa fa-paper-plane me-1"></i> Post
                    </button>
                </div>

            </form>
        </div>

         {{-- @foreach ($posts as $post)
                <livewire:user.post-content :post="$post" :wire:key="'post-'.$post->id" />
            @endforeach --}}


        {{-- CLOUDINARY VIDEO WIDGET --}}
        <script src="https://widget.cloudinary.com/v2.0/global/all.js"></script>

        <script>
            document.addEventListener('livewire:load', function() {

                let widget = cloudinary.createUploadWidget({
                    cloudName: "{{ config('cloudinary.cloud_name') }}",
                    uploadPreset: "payhankey_videos",
                    resourceType: "video",
                    chunk_size: 6000000,
                    clientAllowedFormats: ["mp4", "mov", "webm"],
                    maxFileSize: 100000000, // 100MB
                    multiple: false
                }, (error, result) => {

                    if (!error && result && result.event === "success") {

                        Livewire.emit('videoUploaded', result.info);

                    }

                    if (error) {
                        console.log("Cloudinary Error:", error);
                    }
                });

                document.addEventListener("click", function(e) {
                    if (e.target && e.target.id === "video_upload_btn") {
                        widget.open();
                    }
                });

            });
        </script>


    </div>


</div>
{{-- 

<div>
    <form wire:submit.prevent="createPost">
        <div class="card-body">
            <?php $userLevel = userLevel(); ?>
            
            {{-- Content Textarea --
            <div x-data="{ content: @entangle('content') }">
                <textarea x-model="content" class="form-control" placeholder="Say something amazing" rows="4"
                    @if (!in_array($userLevel, ['Creator', 'Influencer'])) maxlength="160" @endif required></textarea>
                @if (!in_array($userLevel, ['Creator', 'Influencer']))
                    <small class="text-muted" x-text="content.length + '/160 characters'"></small>
                @endif
            </div>

            {{-- Media Upload Section (Only for Creators and Influencers) --
            @if (in_array($userLevel, ['Creator', 'Influencer']))
                <div class="mt-3" x-data="{ 
                    images: @entangle('images'), 
                    videos: @entangle('videos'),
                    activeTab: 'images'
                }">
                    {{-- Tab Navigation --
                    <div class="btn-group mb-3" role="group">
                        <button type="button" 
                            class="btn btn-sm"
                            :class="activeTab === 'images' ? 'btn-primary' : 'btn-outline-primary'"
                            @click="activeTab = 'images'; videos = []">
                            <i class="fas fa-image"></i> Images
                        </button>
                        <button type="button" 
                            class="btn btn-sm"
                            :class="activeTab === 'videos' ? 'btn-primary' : 'btn-outline-primary'"
                            @click="activeTab = 'videos'; images = []">
                            <i class="fas fa-video"></i> Videos
                        </button>
                    </div>

                    {{-- Image Upload Section --
                    <div x-show="activeTab === 'images'" x-transition>
                        <label class="btn btn-light">
                            <i class="fas fa-image"></i> Add Images
                            <input type="file" wire:model="images" multiple accept="image/*" hidden
                                @if ($userLevel === 'Creator') x-bind:disabled="images.length >= 1" @endif
                                @if ($userLevel === 'Influencer') x-bind:disabled="images.length >= 4" @endif>
                        </label>

                        <small class="text-muted d-block mt-1">
                            {{ $userLevel === 'Creator' ? 'Max 1 image (100MB)' : 'Max 4 images (100MB each)' }}
                        </small>

                        {{-- Image Preview --
                        <div class="row mt-3">
                            @foreach ($images as $index => $image)
                                <div class="col-3 position-relative mb-2">
                                    <img src="{{ $image->temporaryUrl() }}" class="img-fluid rounded">
                                    <button type="button" wire:click="removeImage({{ $index }})"
                                        class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1">
                                        &times;
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Video Upload Section --
                    <div x-show="activeTab === 'videos'" x-transition>
                        <label class="btn btn-light">
                            <i class="fas fa-video"></i> Add Video
                            <input type="file" wire:model="videos" accept="video/mp4,video/avi,video/mpeg,video/quicktime,video/webm" hidden
                                x-bind:disabled="videos.length >= 1">
                        </label>

                        <small class="text-muted d-block mt-1">
                            Max 1 video (1GB) • Recommended: MP4, vertical format (9:16)
                        </small>

                        {{-- Video Preview --
                        <div class="row mt-3">
                            @foreach ($videos as $index => $video)
                                <div class="col-6 position-relative mb-2">
                                    <video class="w-100 rounded" controls>
                                        <source src="{{ $video->temporaryUrl() }}" type="{{ $video->getMimeType() }}">
                                        Your browser does not support the video tag.
                                    </video>
                                    <button type="button" wire:click="removeVideo({{ $index }})"
                                        class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1">
                                        &times;
                                    </button>
                                    <div class="mt-2 text-center">
                                        <small class="text-muted">
                                            Size: {{ number_format($video->getSize() / 1024 / 1024, 2) }} MB
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Upload Progress --
            @if ($isUploading)
                <div class="mt-3">
                    <div class="d-flex align-items-center">
                        <div class="spinner-border spinner-border-sm me-2" role="status">
                            <span class="visually-hidden">Uploading...</span>
                        </div>
                        <span class="text-muted">Processing your post...</span>
                    </div>
                </div>
            @endif
        </div>

        {{-- Submit Button --
        <div class="card-footer">
            <button class="btn btn-primary btn-block" type="submit" 
                @if ($isUploading) disabled @endif>
                <i class="fa fa-pencil-alt opacity-50 me-1"></i>
                {{ $isUploading ? 'Posting...' : 'Post' }}
            </button>
        </div>
    </form>

    {{-- Flash Messages --
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('warning'))
        <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('info'))
        <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Network detection for adaptive streaming
    document.addEventListener('livewire:load', function () {
        if ('connection' in navigator) {
            const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
            
            function updateNetworkStrength() {
                let strength = 'medium';
                
                if (connection.effectiveType) {
                    strength = connection.effectiveType; // '4g', '3g', '2g', 'slow-2g'
                } else if (connection.downlink) {
                    // Estimate based on downlink speed (Mbps)
                    if (connection.downlink > 10) strength = '5g';
                    else if (connection.downlink > 5) strength = '4g';
                    else if (connection.downlink > 1) strength = '3g';
                    else strength = '2g';
                }
                
                // Store in session or send to backend
                sessionStorage.setItem('networkStrength', strength);
            }
            
            updateNetworkStrength();
            connection.addEventListener('change', updateNetworkStrength);
        }
    });
</script>
@endpush  --}}
