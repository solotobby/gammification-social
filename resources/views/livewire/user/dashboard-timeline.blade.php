{{-- livewire/user/dashboard-timeline.blade.php --}}

<?php $userLevel = userLevel(); ?>

{{--
    SINGLE ROOT ELEMENT — Livewire v3 requires exactly one root element.
    The <style> block was previously a second root alongside <div class="row">,
    which silently broke all wire:click / wire:model directives.
--}}
<div>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

:root {
    --ph-blue:       #1877f2;
    --ph-blue-h:     #166fe5;
    --ph-blue-light: #e7f3ff;
    --ph-white:      #ffffff;
    --ph-border:     #e4e6eb;
    --ph-text:       #050505;
    --ph-sub:        #65676b;
    --ph-hover:      #f0f2f5;
    --ph-bg:         #f0f2f5;
    --ph-r:          12px;
    --ph-shadow:     0 1px 3px rgba(0,0,0,.12), 0 1px 2px rgba(0,0,0,.08);
}

body { background: var(--ph-bg); font-family: 'Inter', system-ui, sans-serif; }

.ph-card {
    background: var(--ph-white); border-radius: var(--ph-r);
    box-shadow: var(--ph-shadow); padding: 16px; margin-bottom: 16px; font-size: 14px;
}
.ph-card h5 { font-size: 16px; font-weight: 700; color: var(--ph-text); margin-bottom: 6px; }
.ph-card p  { color: var(--ph-sub); margin-bottom: 12px; font-size: 14px; }

.ph-ref-row { display:flex; gap:8px; margin-bottom:10px; }
.ph-ref-row input {
    flex:1; border:1px solid var(--ph-border); border-radius:8px;
    padding:8px 12px; font-size:13px; background:var(--ph-hover); color:var(--ph-text); outline:none;
}
.ph-ref-row button {
    background:var(--ph-blue); color:#fff; border:none; border-radius:8px;
    padding:8px 14px; font-size:13px; font-weight:600; cursor:pointer; font-family:inherit;
}

.ph-earn {
    background:var(--ph-blue-light); border-radius:8px;
    padding:12px 14px; font-size:13px; color:#0a3d91; line-height:1.6;
}
.ph-earn a { color:var(--ph-blue); font-weight:600; text-decoration:none; }

.ph-flash {
    border-radius:var(--ph-r); padding:12px 16px; font-size:14px;
    font-weight:500; margin-bottom:10px; box-shadow:var(--ph-shadow);
}
.ph-flash.success { background:#e6f4ea; color:#1b5e20; border-left:4px solid #43a047; }
.ph-flash.warning { background:#fff8e1; color:#7b5800; border-left:4px solid #ffca28; }
.ph-flash.danger  { background:#fdecea; color:#b71c1c; border-left:4px solid #e53935; }

.ph-composer {
    background: var(--ph-white); border-radius: var(--ph-r);
    box-shadow: var(--ph-shadow); overflow: hidden;
    margin-bottom: 16px; position: relative;
}
.ph-top { display:flex; align-items:flex-start; gap:10px; padding:16px 16px 10px; }
.ph-avatar {
    width:42px; height:42px; border-radius:50%; object-fit:cover;
    flex-shrink:0; border:1px solid var(--ph-border);
}
.ph-textarea-wrap { flex:1; }
.ph-textarea {
    width:100%; border:none; outline:none; resize:none;
    background:var(--ph-hover); border-radius:22px;
    padding:10px 16px; font-size:15px; color:var(--ph-text);
    font-family:inherit; line-height:1.5; min-height:44px; max-height:220px;
}
.ph-textarea:focus { background:#e8eaed; }
.ph-textarea::placeholder { color:var(--ph-sub); }
.ph-charcount { font-size:12px; color:var(--ph-sub); text-align:right; margin-top:4px; }

.ph-media-preview { padding:0 16px 10px; }
.ph-grid { display:grid; gap:3px; border-radius:10px; overflow:hidden; }
.ph-grid.g1 { grid-template-columns:1fr; }
.ph-grid.g2 { grid-template-columns:1fr 1fr; }
.ph-grid.g3 { grid-template-columns:1fr 1fr; }
.ph-grid.g3 .ph-thumb:first-child { grid-column:1/-1; }
.ph-grid.g4 { grid-template-columns:1fr 1fr; }
.ph-thumb { position:relative; overflow:hidden; background:#000; aspect-ratio:1; }
.ph-thumb.tall { aspect-ratio:16/9; }
.ph-thumb img { width:100%; height:100%; object-fit:cover; display:block; }
.ph-remove {
    position:absolute; top:6px; right:6px; width:28px; height:28px;
    border-radius:50%; background:rgba(0,0,0,.65); color:#fff; border:none;
    cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:16px;
}

.ph-vzone {
    margin:0 16px 10px; background:#f7f8fa; border:2px dashed var(--ph-border);
    border-radius:10px; padding:28px 16px; text-align:center; cursor:pointer;
    transition:border-color .2s, background .2s;
}
.ph-vzone:hover, .ph-vzone.dragover { border-color:var(--ph-blue); background:var(--ph-blue-light); }
.ph-vzone-icon {
    width:52px; height:52px; border-radius:50%; background:#ffdfe2;
    display:flex; align-items:center; justify-content:center; margin:0 auto 12px;
}
.ph-vzone h6 { font-size:15px; font-weight:600; color:var(--ph-text); margin:0 0 4px; }
.ph-vzone p  { font-size:13px; color:var(--ph-sub); margin:0; }

.ph-prog-wrap { padding:0 16px 10px; }
.ph-prog-meta { display:flex; justify-content:space-between; font-size:13px; color:var(--ph-sub); margin-bottom:6px; }
.ph-prog-track { height:6px; background:var(--ph-border); border-radius:99px; overflow:hidden; }
.ph-prog-fill  { height:100%; background:linear-gradient(90deg,var(--ph-blue),#42a5f5); border-radius:99px; transition:width .4s; }

.ph-done {
    margin:0 16px 10px; background:#e6f4ea; border:1px solid #a8d5b5;
    border-radius:10px; padding:12px 16px; display:flex; align-items:center; gap:10px;
}
.ph-done svg { flex-shrink:0; color:#2d7a3a; }
.ph-done .ttl { font-weight:600; font-size:14px; color:#1b4d23; }
.ph-done .sub { font-size:12px; color:#3a7d44; margin:0; }

.ph-err {
    margin:0 16px 10px; background:#fff0f0; border:1px solid #f5c2c2;
    border-radius:10px; padding:12px 16px; font-size:13px; color:#c0392b;
    display:flex; align-items:flex-start; gap:8px;
}
.ph-err button {
    margin-left:auto; background:none; border:none; color:var(--ph-blue);
    font-weight:600; cursor:pointer; font-size:13px; padding:0; font-family:inherit;
}

.ph-vthumb { margin:0 16px 10px; border-radius:10px; overflow:hidden; position:relative; background:#000; aspect-ratio:16/9; }
.ph-vthumb video { width:100%; height:100%; object-fit:cover; display:block; }
.ph-vthumb-change {
    position:absolute; top:8px; right:8px; background:rgba(0,0,0,.6);
    color:#fff; border:none; border-radius:6px; padding:4px 10px; font-size:12px; cursor:pointer;
}

.ph-divider { height:1px; background:var(--ph-border); margin:0 16px; }

.ph-actions { display:flex; align-items:center; padding:4px 8px; gap:2px; }
.ph-act {
    display:flex; align-items:center; gap:6px; padding:8px 12px; border:none;
    background:transparent; border-radius:8px; font-size:14px; font-weight:600;
    color:var(--ph-sub); cursor:pointer; transition:background .15s; white-space:nowrap; font-family:inherit;
}
.ph-act:hover { background:var(--ph-hover); color:var(--ph-text); }
.ph-act.photo  { color:#45bd62; }
.ph-act.video  { color:#f02849; }
.ph-act.cancel { color:#e41e3f; }

.ph-submit {
    margin-left:auto; background:var(--ph-blue); color:#fff; border:none;
    border-radius:8px; padding:8px 22px; font-size:15px; font-weight:600;
    cursor:pointer; transition:background .15s; font-family:inherit;
    display:flex; align-items:center; gap:6px;
}
.ph-submit:hover:not(:disabled) { background:var(--ph-blue-h); }
.ph-submit:disabled { background:#bcc0c4; cursor:not-allowed; }

.ph-more { text-align:center; margin:8px 0 20px; }
.ph-more button {
    background:var(--ph-white); border:1px solid var(--ph-border); border-radius:8px;
    padding:10px 28px; font-size:14px; font-weight:600; color:var(--ph-blue);
    cursor:pointer; box-shadow:var(--ph-shadow); font-family:inherit;
}
.ph-more button:hover { background:var(--ph-hover); }

[x-cloak] { display:none !important; }
</style>

<div class="row">
    <div class="col-md-8">

        {{-- Referral card --}}
        <div class="ph-card">
            <h5>👋 Invite friends to Payhankey</h5>
            <p>Share your link and grow your engagement network.</p>
            <div class="ph-ref-row">
                <input type="text" id="referralLink"
                       value="{{ url('/reg?referral_code='.auth()->user()->referral_code) }}" readonly>
                <button type="button" onclick="copyReferralLink()">
                    <i class="fa fa-copy me-1"></i>Copy
                </button>
            </div>
            <a href="{{ url('referral/list') }}"
               style="color:var(--ph-blue);font-size:13px;font-weight:600;text-decoration:none">
               View all referrals →
            </a>
        </div>

        {{-- Earn card --}}
        <div class="ph-card">
            <div class="ph-earn">
                <strong>💰 Earn from every post.</strong><br>
                Creator &amp; Influencer accounts earn up to
                <strong>{{ getCurrencyCode() }}{{ convertToBaseCurrency(1, auth()->user()->wallet->currency) }}</strong>
                per 1,000 engagements — text, images <strong>and short videos</strong>.
                <a href="https://payhankey.com/blog/how-payout-for-content-monetization-works-on-payhankey-social-media-69a6ec0f95475"
                   target="_blank">Learn more →</a>
            </div>
        </div>

        {{-- Flash messages --}}
        @if(session()->has('success'))
            <div class="ph-flash success">{{ session('success') }}</div>
        @endif
        @if(session()->has('info'))
            <div class="ph-flash warning">{{ session('info') }}</div>
        @endif
        @if(session()->has('error'))
            <div class="ph-flash danger">{{ session('error') }}</div>
        @endif

        {{-- Validation errors --}}
        @if($errors->any())
            <div class="ph-flash danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        {{-- ══════════════════════════════════════════════════════
             COMPOSER — x-data lives here, inside Livewire root
             ══════════════════════════════════════════════════════ --}}
        <div class="ph-composer"
             x-data="composerUI()"
             x-init="boot()">

            {{-- Hidden file inputs — always in DOM, never inside @if --}}
            @if(in_array($userLevel, ['Creator','Influencer']))
                <input type="file"
                       x-ref="imgInput"
                       wire:model="images"
                       accept="image/*"
                       multiple
                       style="position:absolute;width:1px;height:1px;opacity:0;pointer-events:none;top:0;left:0"
                       tabindex="-1"
                       @change="onImages($event)">

                <input type="file"
                       x-ref="vidInput"
                       wire:model="video"
                       accept="video/mp4,video/quicktime,video/webm,video/x-msvideo"
                       style="position:absolute;width:1px;height:1px;opacity:0;pointer-events:none;top:0;left:0"
                       tabindex="-1"
                       @change="onVideo($event)">
            @endif

            {{-- Avatar + Textarea --}}
            <div class="ph-top">
                <img class="ph-avatar"
                     src="{{ auth()->user()->avatar
                              ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=1877f2&color=fff&size=84' }}"
                     alt="{{ auth()->user()->name }}">

                <div class="ph-textarea-wrap">
                    {{--
                        THE KEY FIX:
                        wire:model.live — syncs to PHP on every keystroke.
                        Without .live, Livewire v3 only syncs on blur,
                        so $this->content is empty when Post is clicked.
                    --}}
                    <textarea
                        wire:model.live="content"
                        class="ph-textarea"
                        :placeholder="mode==='video'
                            ? 'Add a caption for your video…'
                            : `What\'s on your mind, {{ auth()->user()->name }}?`"
                        rows="2"
                        @if(!in_array($userLevel,['Creator','Influencer'])) maxlength="160" @endif
                        @input="grow($el)"></textarea>

                    @if(!in_array($userLevel,['Creator','Influencer']))
                        <div class="ph-charcount">
                            <span wire:text="strlen($content)"></span>/160
                        </div>
                    @endif
                </div>
            </div>

            {{-- Image previews --}}
            @if(in_array($userLevel,['Creator','Influencer']))
                <div class="ph-media-preview"
                     x-show="mode==='image' && previews.length"
                     x-cloak>
                    <div class="ph-grid" :class="'g'+Math.min(previews.length,4)">
                        <template x-for="(src,i) in previews.slice(0,4)" :key="i">
                            <div class="ph-thumb" :class="previews.length===1?'tall':''">
                                <img :src="src" alt="">
                                <button type="button" class="ph-remove"
                                        @click.prevent="rmImage(i)">×</button>
                            </div>
                        </template>
                    </div>
                </div>
            @endif

            {{-- Video zone --}}
            @if(in_array($userLevel,['Creator','Influencer']))

                {{-- Idle: drop zone --}}
                <div class="ph-vzone"
                     x-show="mode==='video' && vStatus==='idle'"
                     x-cloak
                     @click="$refs.vidInput.click()"
                     @dragover.prevent="$el.classList.add('dragover')"
                     @dragleave="$el.classList.remove('dragover')"
                     @drop.prevent="onDrop($event)">
                    <div style="pointer-events:none">
                        <div class="ph-vzone-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f02849" stroke-width="2.2">
                                <path d="M15 10l4.553-2.532A1 1 0 0121 8.382v7.236a1 1 0 01-1.447.894L15 14"/>
                                <rect x="2" y="6" width="13" height="12" rx="2"/>
                            </svg>
                        </div>
                        <h6>Add a video to your post</h6>
                        <p>Drag &amp; drop or click to browse · MP4, MOV, WEBM ·
                           @if($userLevel==='Creator') Max 20s @else Max 1m 20s @endif
                        </p>
                    </div>
                    <div wire:loading wire:target="video" style="margin-top:12px;font-size:13px;color:var(--ph-sub)">
                        <span class="spinner-border spinner-border-sm me-1"></span>Preparing…
                    </div>
                </div>

                {{-- Uploading --}}
                <div class="ph-prog-wrap" x-show="mode==='video' && vStatus==='uploading'" x-cloak>
                    <div class="ph-prog-meta">
                        <span x-text="vMsg||'Uploading to Cloudinary…'"></span>
                        <span x-text="vPct+'%'"></span>
                    </div>
                    <div class="ph-prog-track">
                        <div class="ph-prog-fill" :style="'width:'+vPct+'%'"></div>
                    </div>
                </div>

                {{-- Done --}}
                <div x-show="mode==='video' && vStatus==='done'" x-cloak>
                    @if($cloudinaryVideoUrl)
                        <div class="ph-vthumb">
                            <video src="{{ $cloudinaryVideoUrl }}" muted playsinline controls></video>
                            <button type="button" class="ph-vthumb-change"
                                    @click="$refs.vidInput.click()">Change video</button>
                        </div>
                    @endif
                    <div class="ph-done">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M20 6L9 17l-5-5"/>
                        </svg>
                        <div>
                            <div class="ttl">Upload complete!</div>
                            <p class="sub">Add a caption above then press <strong>Publish</strong>.</p>
                        </div>
                    </div>
                </div>

                {{-- Error --}}
                <div class="ph-err" x-show="mode==='video' && vStatus==='error'" x-cloak>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span x-text="vErr||'Upload failed. Please try again.'"></span>
                    <button type="button" @click="$refs.vidInput.click()">Retry</button>
                </div>

            @endif

            <div class="ph-divider"></div>

            {{-- Action bar --}}
            <div class="ph-actions">

                @if(in_array($userLevel,['Creator','Influencer']))
                    <button type="button" class="ph-act photo"
                            x-show="mode!=='video'" @click="openImg()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="3"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <path d="M21 15l-5-5L5 21"/>
                        </svg>
                        Photo
                    </button>

                    <button type="button" class="ph-act video"
                            x-show="mode!=='image'" @click="openVid()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 10l4.553-2.532A1 1 0 0121 8.382v7.236a1 1 0 01-1.447.894L15 14"/>
                            <rect x="2" y="6" width="13" height="12" rx="2"/>
                        </svg>
                        Video
                    </button>

                    <button type="button" class="ph-act cancel"
                            x-show="mode!=='none'" x-cloak @click="cancel()">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M18 6L6 18M6 6l12 12"/>
                        </svg>
                        Cancel
                    </button>
                @endif

                {{-- Publish Video button --}}
                @if(in_array($userLevel,['Creator','Influencer']))
                    <button type="button" class="ph-submit"
                            x-show="mode==='video'" x-cloak
                            :disabled="vStatus!=='done'"
                            wire:click="publishVideo">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="22" y1="2" x2="11" y2="13"/>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                        </svg>
                        <span wire:loading.remove wire:target="publishVideo">Publish</span>
                        <span wire:loading wire:target="publishVideo">
                            <span class="spinner-border spinner-border-sm"></span>
                        </span>
                    </button>
                @endif

                {{-- Post button --}}
                <button type="button" class="ph-submit"
                        x-show="mode!=='video'"
                        wire:click="createPost">
                    <span wire:loading.remove wire:target="createPost">Post</span>
                    <span wire:loading wire:target="createPost">
                        <span class="spinner-border spinner-border-sm"></span>
                    </span>
                </button>

            </div>
        </div>{{-- /.ph-composer --}}

        {{-- Feed --}}
        @foreach($posts as $post)
            <livewire:user.post-content :post="$post" :wire:key="'post-'.$post->id"/>
        @endforeach

        @if($isVideoOpen)
            <livewire:user.video-player
                :videoId="$activeVideoId"
                wire:key="video-player-{{ $activeVideoId }}"/>
        @endif

        @if($hasMore)
            <div class="ph-more">
                <button wire:click="loadNextPage" wire:loading.attr="disabled">
                    <span wire:loading.remove>Load more posts</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm me-1"></span>Loading…
                    </span>
                </button>
            </div>
        @endif

    </div>
    @include('layouts.engagement')
</div>

@script
<script>
Alpine.data('composerUI', () => ({

    mode:     'none',
    previews: [],
    vStatus:  'idle',
    vPct:     0,
    vMsg:     '',
    vErr:     '',

    boot() {
        Livewire.on('videoUploadStatus', ({ status, progress }) => {
            if (status === 'uploading') this.vStatus = 'uploading';
            if (status === 'done')    { this.vStatus = 'done'; this.vPct = 100; }
            if (status === 'error')    this.vStatus = 'error';
            if (status === 'idle')     this.vStatus = 'idle';
            if (progress != null)      this.vPct    = progress;
        });
        Livewire.on('postPublished', () => this.reset());
    },

    grow(el) {
        el.style.height = 'auto';
        el.style.height = el.scrollHeight + 'px';
    },

    openImg() {
        this.mode = 'image';
        this.$refs.imgInput.click();
    },

    onImages(e) {
        const files   = Array.from(e.target.files);
        const max     = {{ $userLevel === 'Creator' ? 1 : 4 }};
        const allowed = files.slice(0, max);
        this.previews = allowed.map(f => URL.createObjectURL(f));
        if (files.length > max) {
            const dt = new DataTransfer();
            allowed.forEach(f => dt.items.add(f));
            this.$refs.imgInput.files = dt.files;
            this.$refs.imgInput.dispatchEvent(new Event('change', { bubbles: true }));
        }
    },

    rmImage(i) {
        this.previews.splice(i, 1);
        Livewire.dispatch('removeImageAt', { index: i });
        const remaining = Array.from(this.$refs.imgInput.files || [])
                               .filter((_, idx) => idx !== i);
        const dt = new DataTransfer();
        remaining.forEach(f => dt.items.add(f));
        this.$refs.imgInput.files = dt.files;
        if (this.previews.length === 0) this.mode = 'none';
    },

    openVid() {
        this.mode    = 'video';
        this.vStatus = 'idle';
        this.vErr    = '';
        this.$refs.vidInput.click();
    },

    onVideo(e) {
        const file = e.target.files[0];
        if (!file) return;
        const okTypes = ['video/mp4','video/quicktime','video/webm','video/x-msvideo'];
        if (!okTypes.includes(file.type)) {
            this.vStatus = 'error';
            this.vErr    = 'Unsupported format. Please use MP4, MOV, or WEBM.';
            return;
        }
        const maxSecs = {{ $userLevel === 'Creator' ? 20 : 80 }};
        const url     = URL.createObjectURL(file);
        const tmp     = document.createElement('video');
        tmp.preload   = 'metadata';
        tmp.src       = url;
        tmp.onloadedmetadata = () => {
            URL.revokeObjectURL(url);
            if (tmp.duration > maxSecs) {
                this.vStatus = 'error';
                this.vErr    = `Video is ${Math.round(tmp.duration)}s — your limit is ${maxSecs}s.`;
                this.$refs.vidInput.value = '';
                return;
            }
            this.vStatus = 'uploading';
            this.vPct    = 5;
            this.vMsg    = 'Uploading to Cloudinary…';
        };
    },

    onDrop(e) {
        e.currentTarget.classList.remove('dragover');
        const file = e.dataTransfer?.files[0];
        if (!file) return;
        const dt = new DataTransfer();
        dt.items.add(file);
        this.$refs.vidInput.files = dt.files;
        this.$refs.vidInput.dispatchEvent(new Event('change', { bubbles: true }));
        this.onVideo({ target: this.$refs.vidInput });
    },

    cancel() {
        const wasVideo = this.mode === 'video';
        this.reset();
        if (wasVideo) Livewire.dispatch('cancelVideoUpload');
    },

    reset() {
        this.mode     = 'none';
        this.previews = [];
        this.vStatus  = 'idle';
        this.vPct     = 0;
        this.vErr     = '';
        this.vMsg     = '';
        if (this.$refs.imgInput) this.$refs.imgInput.value = '';
        if (this.$refs.vidInput) this.$refs.vidInput.value = '';
    },

}));
</script>
@endscript
</div>{{-- single Livewire root closes here --}}

