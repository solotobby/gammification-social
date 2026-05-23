{{-- resources/views/livewire/user/rolls.blade.php --}}
{{--
    ARCHITECTURE (clean rewrite)
    ─────────────────────────────
    • One outer <div> = Livewire root
    • One inner x-data="rollsPlayer($wire)" = Alpine root
      · $wire is passed AS AN ARGUMENT so it is captured in closure scope
      · No @script top-level let/const issues
    • Alpine owns ALL ui state: muted, comments open/close, share, like animation
    • Livewire owns ALL server writes: likes, views, watch-time, comments
    • Comment text lives in Alpine (x-model). Passed directly to wire.call()
    • submitComment() in PHP takes text as a parameter — no wire:model race condition
--}}

<div>{{-- single Livewire root --}}

<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
    --acc:#6c3ce1;--acc2:#f02849;
    --glass:rgba(0,0,0,.55);
    --font:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
}
/* Override ANYTHING the app layout does to html/body */
html,body{
    height:100% !important;
    overflow:hidden !important;
    background:#000 !important;
    font-family:var(--font) !important;
    padding:0 !important;
    margin:0 !important;
}
[x-cloak]{display:none!important}

/* ── ROOT LAYER ──────────────────────────────────────────────
   position:fixed; inset:0 breaks out of EVERY app layout
   container (Bootstrap .container, .row, padding, etc.).
   z-index:9999 sits above the app chrome.
   The root is a flex container that holds the feed column.
────────────────────────────────────────────────────────────── */
.r-root{
    position:fixed !important;
    top:0 !important;
    left:0 !important;
    right:0 !important;
    bottom:0 !important;
    width:100vw !important;
    height:100vh !important;
    background:#000;
    z-index:9999;
    overflow:hidden;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:flex-start;
}

/* top bar */
.r-topbar{
    position:fixed;
    top:0;left:0;right:0;
    z-index:10200;
    display:flex;align-items:center;justify-content:space-between;
    padding:14px 16px;
    pointer-events:none;
    background:linear-gradient(to bottom,rgba(0,0,0,.65),transparent);
}
.r-topbar>*{pointer-events:all}
.r-logo{font-size:18px;font-weight:800;color:#fff;text-decoration:none;letter-spacing:-.5px}
.r-logo span{color:var(--acc)}
.r-ibtn{
    width:38px;height:38px;border-radius:50%;background:var(--glass);
    border:1px solid rgba(255,255,255,.15);color:#fff;display:flex;
    align-items:center;justify-content:center;cursor:pointer;
    backdrop-filter:blur(8px);font-size:15px;text-decoration:none;transition:background .15s;
}
.r-ibtn:hover{background:rgba(255,255,255,.2);color:#fff}

/* ── FEED ────────────────────────────────────────────────────
   Mobile: full screen.
   Desktop (>=1024px): centered phone-width column.
────────────────────────────────────────────────────────────── */
.r-feed{
    /* Fill the entire r-root */
    width:100%;
    height:100vh;
    /* Scroll-snap */
    overflow-y:scroll;
    scroll-snap-type:y mandatory;
    -webkit-overflow-scrolling:touch;
    scrollbar-width:none;
    background:#000;
    /* Nothing else — no margin, no transform, no max-width on mobile */
}
.r-feed::-webkit-scrollbar{display:none}

/* ── CARD ────────────────────────────────────────────────────
   Must be EXACTLY the same width as the feed and exactly 100vh.
────────────────────────────────────────────────────────────── */
.r-card{
    position:relative;
    width:100%;       /* fills feed width */
    height:100vh;     /* fills viewport height */
    scroll-snap-align:start;
    background:#000;
    overflow:hidden;
}

/* ── VIDEO ───────────────────────────────────────────────────
   position:absolute; inset:0 means it fills the card exactly.
   object-fit:cover fills without black bars.
────────────────────────────────────────────────────────────── */
.r-video{
    position:absolute;
    top:0;left:0;
    width:100%;
    height:100%;
    object-fit:cover;
    display:block;
    cursor:pointer;
}

/* gradients */
.r-grad-b{position:absolute;bottom:0;left:0;right:0;height:70%;pointer-events:none;
    background:linear-gradient(to top,rgba(0,0,0,.85) 0%,rgba(0,0,0,.3) 50%,transparent)}
.r-grad-t{position:absolute;top:0;left:0;right:0;height:25%;pointer-events:none;
    background:linear-gradient(to bottom,rgba(0,0,0,.5),transparent)}

/* progress wrap — holds both the bar and the timer */
.r-prog-wrap{
    position:absolute;bottom:0;left:0;right:0;z-index:20;
}

/* progress bar */
.r-prog{
    height:3px;cursor:pointer;
    background:rgba(255,255,255,.25);
    transition:height .15s;
    position:relative;
}
.r-prog:hover{height:5px}
.r-prog-fill{
    height:100%;width:0%;
    background:var(--acc);
    pointer-events:none;
    transition:none; /* updated every frame — no CSS transition needed */
}
.r-prog-thumb{
    position:absolute;top:50%;right:0;
    width:12px;height:12px;border-radius:50%;background:#fff;
    transform:translate(50%,-50%) scale(0);
    transition:transform .15s;pointer-events:none;
}
.r-prog:hover .r-prog-thumb{transform:translate(50%,-50%) scale(1)}

/* live timer — REMOVED per user request */

/* flash */
.r-flash{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
    width:72px;height:72px;border-radius:50%;background:rgba(0,0,0,.5);
    backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;
    color:#fff;font-size:26px;pointer-events:none;z-index:30;opacity:0}
@keyframes flashPop{
    0%{opacity:1;transform:translate(-50%,-50%) scale(.8)}
    40%{opacity:1;transform:translate(-50%,-50%) scale(1.1)}
    100%{opacity:0;transform:translate(-50%,-50%) scale(1.5)}}
.r-flash.pop{animation:flashPop .45s ease-out forwards}

/* info */
.r-info{position:absolute;bottom:20px;left:14px;right:80px;z-index:10}
.r-user-row{display:flex;align-items:center;gap:10px;margin-bottom:10px}
.r-av{width:42px;height:42px;border-radius:50%;object-fit:cover;
    border:2px solid rgba(255,255,255,.8);flex-shrink:0}
.r-name{font-size:15px;font-weight:700;color:#fff;display:flex;align-items:center;gap:5px;
    text-shadow:0 1px 3px rgba(0,0,0,.5)}
.r-name a{color:inherit;text-decoration:none}

/* Tablet: slightly wider */
@media(min-width:768px) and (max-width:1023px){
    .r-feed{
        max-width:600px;
        margin:0 auto;
        border-left:1px solid #222;
        border-right:1px solid #222;
    }
    /* Drawer: fixed width matching feed, centered with margin auto trick.
       We can't use margin:auto on position:fixed, so we use left/right set
       to the same offset to effectively center it. */
    .r-drawer{
        width:600px;
        max-width:100vw;
        left:50%;
        right:auto;
        transform:translateX(-50%) translateY(100%);
    }
    .r-drawer.open{
        transform:translateX(-50%) translateY(0);
    }
}

/* Desktop: wider video, still centered */
@media(min-width:1024px){
    .r-feed{
        max-width:680px;
        margin:0 auto;
        border-left:1px solid #222;
        border-right:1px solid #222;
    }
    /* Drawer uses explicit width matching feed max-width.
       left:50% + translateX(-50%) centers it correctly when
       width is explicit (not just max-width). */
    .r-drawer{
        width:680px;
        max-width:100vw;
        left:50%;
        right:auto;
        transform:translateX(-50%) translateY(100%);
    }
    .r-drawer.open{
        transform:translateX(-50%) translateY(0);
    }
    .r-share-scrim{ align-items:center; }
    .r-share-box{ border-radius:20px; padding-bottom:20px; max-width:640px; }
}
.r-level{font-size:10px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;
    color:rgba(255,255,255,.55);margin-top:2px}
.r-caption{font-size:14px;line-height:1.5;color:rgba(255,255,255,.9);
    text-shadow:0 1px 3px rgba(0,0,0,.4);
    display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
.r-caption.expanded{-webkit-line-clamp:unset;display:block}
.r-more-btn{font-size:13px;color:rgba(255,255,255,.55);cursor:pointer;
    background:none;border:none;padding:0;font-family:inherit}

/* sidebar */
.r-side{position:absolute;right:12px;bottom:28px;z-index:10;
    display:flex;flex-direction:column;align-items:center;gap:18px}
.r-sitem{display:flex;flex-direction:column;align-items:center;gap:4px;cursor:pointer}
.r-sbtn{width:50px;height:50px;border-radius:50%;background:var(--glass);
    backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.12);
    display:flex;align-items:center;justify-content:center;color:#fff;
    font-size:20px;transition:transform .1s,background .15s;cursor:pointer;
    -webkit-user-select:none;user-select:none}
.r-sitem:hover .r-sbtn{background:rgba(255,255,255,.18);transform:scale(1.08)}
.r-scount{font-size:11px;font-weight:700;color:rgba(255,255,255,.9);text-align:center;
    text-shadow:0 1px 3px rgba(0,0,0,.5)}
.r-sitem.liked .r-sbtn{background:rgba(240,40,73,.25);border-color:rgba(240,40,73,.4)}
.r-sitem.liked .r-sbtn i{color:var(--acc2)}
@keyframes heartPop{0%{transform:scale(1)}40%{transform:scale(1.5)}70%{transform:scale(.9)}100%{transform:scale(1)}}
.r-sitem.heart-pop .r-sbtn i{animation:heartPop .35s ease}

/* load more */
.r-loadmore{height:100vh;width:100%;scroll-snap-align:start;background:#0a0a0a;
    display:flex;align-items:center;justify-content:center}

/* comment drawer — mobile: full width */
.r-drawer{
    position:fixed;
    bottom:0;
    /* full width on mobile */
    left:0;
    right:0;
    width:100%;
    z-index:10300;
    background:#111;
    border-top:1px solid rgba(255,255,255,.08);
    border-top-left-radius:20px;
    border-top-right-radius:20px;
    max-height:70vh;
    display:flex;
    flex-direction:column;
    transform:translateY(100%);
    transition:transform .3s cubic-bezier(.32,0,.15,1);
}
.r-drawer.open{ transform:translateY(0); }
.r-drawer-handle{width:36px;height:4px;border-radius:2px;
    background:rgba(255,255,255,.2);margin:10px auto 0}
.r-drawer-head{display:flex;justify-content:space-between;align-items:center;
    padding:12px 16px 10px;border-bottom:1px solid rgba(255,255,255,.07);
    color:#fff;font-weight:700;font-size:15px}
.r-drawer-close{width:28px;height:28px;border-radius:50%;
    background:rgba(255,255,255,.1);border:none;color:#aaa;
    cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center}
.r-clist{flex:1;overflow-y:auto;padding:12px 16px;
    display:flex;flex-direction:column;gap:14px}
.r-clist::-webkit-scrollbar{width:3px}
.r-clist::-webkit-scrollbar-thumb{background:rgba(255,255,255,.15);border-radius:99px}
.r-citem{display:flex;gap:10px;align-items:flex-start}
.r-cav{width:32px;height:32px;border-radius:50%;object-fit:cover;flex-shrink:0}
.r-cname{font-size:13px;font-weight:700;color:#ddd}
.r-ctext{font-size:14px;color:#bbb;line-height:1.4;margin:2px 0 0}
.r-ctime{font-size:11px;color:#555;margin-top:3px}
.r-cempty{color:#555;text-align:center;padding:32px 0;font-size:14px}
.r-cinput-row{display:flex;gap:10px;align-items:center;
    padding:12px 16px;border-top:1px solid rgba(255,255,255,.07)}
.r-cav-sm{width:32px;height:32px;border-radius:50%;object-fit:cover;flex-shrink:0}
.r-cfield{flex:1;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.1);
    border-radius:99px;padding:9px 16px;color:#fff;font-size:14px;outline:none;
    font-family:inherit;transition:border-color .15s}
.r-cfield:focus{border-color:var(--acc)}
.r-cfield::placeholder{color:#555}
.r-csend{width:38px;height:38px;border-radius:50%;flex-shrink:0;
    background:var(--acc);color:#fff;border:none;cursor:pointer;
    display:flex;align-items:center;justify-content:center;font-size:15px;
    opacity:.4;transition:opacity .15s,background .15s}
.r-csend.active{opacity:1}
.r-csend:hover{background:#5a32c0}

/* share sheet */
.r-share-scrim{position:fixed;inset:0;z-index:10400;
    background:rgba(0,0,0,.6);display:flex;align-items:flex-end}
.r-share-box{width:100%;max-width:540px;margin:0 auto;
    background:#161616;border:1px solid rgba(255,255,255,.08);
    border-top-left-radius:20px;border-top-right-radius:20px;
    padding:20px 18px 32px;color:#fff}
.r-share-title{font-size:16px;font-weight:700;margin-bottom:14px}
.r-copy-row{display:flex;gap:8px;margin-bottom:16px}
.r-copy-input{flex:1;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);
    border-radius:8px;padding:9px 12px;color:#fff;font-size:13px;outline:none}
.r-copy-btn{background:var(--acc);color:#fff;border:none;border-radius:8px;
    padding:9px 16px;font-weight:700;cursor:pointer;font-size:13px;white-space:nowrap}
.r-share-btns{display:flex;gap:10px;flex-wrap:wrap}
.r-share-btns a{display:flex;align-items:center;gap:6px;padding:9px 14px;
    border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;color:#fff}

/* scrim for comment drawer */
.r-scrim{position:fixed;inset:0;z-index:10290;background:rgba(0,0,0,.35);display:none}
.r-scrim.on{display:block}
</style>

{{-- ══════════════════════════════════════════════════════════════
     ALPINE ROOT — $wire passed as argument, always available
     ══════════════════════════════════════════════════════════════ --}}
<div class="r-root" x-data="rollsPlayer($wire)" x-init="boot()" x-cloak>

    {{-- top bar --}}
    <div class="r-topbar">
        <a href="javascript:history.back()" class="r-ibtn">
            <i class="fas fa-arrow-left"></i>
        </a>
        <a class="r-logo" href="{{ url('/') }}">Pay<span>hankey</span></a>
        {{-- Mute button — icon driven by Alpine reactive `muted` property --}}
        <button class="r-ibtn" @click="toggleMute()">
            <i :class="muted ? 'fas fa-volume-mute' : 'fas fa-volume-up'"></i>
        </button>
    </div>

    {{-- scrim behind comment drawer --}}
    <div class="r-scrim" :class="{on: commentsOpen}" @click="closeComments()"></div>

    {{-- ── FEED ─────────────────────────────────────────────── --}}
    <div class="r-feed" x-ref="feed" @click="onTap($event)">

        @forelse($videos as $post)
            @php
                
                $vid      = $post->video;
                $user     = $post->user;

                $liked    = is_object($post->likes) && method_exists($post->likes,'contains')
                            ? $post->likes->contains('user_id', auth()->id()) : false;
                $likes    = is_object($post->likes) && method_exists($post->likes,'count')
                            ? $post->likes : (int)$post->likes;
                            
                $comments = sumCounter($post->comments, $post->comment_external);
                 
                            // is_object($post->comments) && method_exists($post->comments,'count')
                            // ? $post->comments : (int)$post->comments;

                $views    = $post->video?->view_count ?? 0;
                
                // (is_object($post->views) && method_exists($post->views,'count')
                //             ? $post->views : 0) + ($vid->view_count ?? 0);

                $src      = $vid->quality_versions['high'] ?? $vid->path;
                $poster   = $vid->thumbnail_path ?? '';
                $duration = $vid->formatted_duration ?? '';
                $caption  = strip_tags($post->content);
                $shareUrl = url('rolls/'.$vid->id);
                $level    = userLevel($user->id);

                // UUIDs must be json_encoded for safe inline JS
                $jsId  = json_encode((string)$post->id);
            @endphp

            <div class="r-card"
                 data-post-id="{{ $post->id }}"
                 data-src="{{ $src }}">

                <video class="r-video"
                       poster="{{ $poster }}"
                       playsinline preload="none" loop muted></video>

                <div class="r-grad-t"></div>
                <div class="r-grad-b"></div>

                {{-- progress bar --}}
                <div class="r-prog-wrap">
                    <div class="r-prog"
                         @mousedown="scrub($event, $el)"
                         @touchstart.prevent="scrub($event, $el)">
                        <div class="r-prog-fill" id="prog-{{ $post->id }}"></div>
                        <div class="r-prog-thumb"></div>
                    </div>
                </div>

                {{-- flash --}}
                <div class="r-flash" id="flash-{{ $post->id }}">
                    <i id="flash-ic-{{ $post->id }}" class="fas fa-play"></i>
                </div>

                {{-- info --}}
                <div class="r-info">
                    <div class="r-user-row">
                        <a href="{{ url('profile/'.$user->username) }}">
                            <img class="r-av" loading="lazy"
                                 src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=6c3ce1&color=fff&size=84' }}"
                                 alt="{{ $user->name }}">
                        </a>
                        <div>
                            <div class="r-name">
                                <a href="{{ url('profile/'.$user->username) }}">{{ $user->username }}</a>
                                @if(in_array($level,['Creator','Influencer']))
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="var(--acc)">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="r-level">{{ $level }}</div>
                        </div>
                    </div>

                    @if(strlen($caption) > 100)
                        <div class="r-caption" id="cap-{{ $post->id }}">{{ Str::limit($caption,280) }}</div>
                        <button class="r-more-btn"
                                @click.stop="expandCaption({{ $jsId }}, {{ json_encode($caption) }})">
                            more
                        </button>
                    @else
                        <div class="r-caption">{{ $caption }}</div>
                    @endif
                </div>

                {{-- ── sidebar ─────────────────────────────── --}}
                <div class="r-side">

                    {{-- Like --}}
                    <div class="r-sitem {{ $liked ? 'liked' : '' }}"
                         id="like-{{ $post->id }}"
                         @click.stop="likePost({{ $jsId }})">
                        <div class="r-sbtn"><i class="fas fa-heart"></i></div>
                        <span class="r-scount" id="lc-{{ $post->id }}">{{ number_format($likes) }}</span>
                    </div>

                    {{-- Comment --}}
                    <div class="r-sitem" @click.stop="openComments({{ $jsId }})">
                        <div class="r-sbtn"><i class="fas fa-comment-dots"></i></div>
                        <span class="r-scount" id="cc-{{ $post->id }}">{{ number_format($comments) }}</span>
                    </div>

                    {{-- Views --}}
                    <div class="r-sitem" style="cursor:default">
                        <div class="r-sbtn" style="cursor:default"><i class="fas fa-eye"></i></div>
                        <span class="r-scount" id="vc-{{ $post->id }}">{{ number_format($views) }}</span>
                    </div>

                    {{-- Share --}}
                    <div class="r-sitem" @click.stop="openShare('{{ $shareUrl }}')">
                        <div class="r-sbtn"><i class="fas fa-share"></i></div>
                        <span class="r-scount">Share</span>
                    </div>

                    {{-- Duration --}}
                    {{-- @if($duration)
                        <div class="r-sitem" style="cursor:default">
                            <div style="background:rgba(0,0,0,.5);border-radius:6px;padding:3px 8px;font-size:12px;font-weight:700;color:#fff">
                                {{ $duration }}
                            </div>
                        </div>
                    @endif --}}

                </div>
            </div>

        @empty
            <div class="r-loadmore">
                <p style="color:#555;font-size:15px">No videos yet.</p>
            </div>
        @endforelse

        @if($hasMore)
            <div class="r-loadmore">
                <button wire:click="loadMore"
                        style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.2);
                               color:#fff;border-radius:10px;padding:12px 28px;font-size:15px;cursor:pointer">
                    <span wire:loading.remove wire:target="loadMore">Load more</span>
                    <span wire:loading wire:target="loadMore">
                        <i class="fas fa-spinner fa-spin me-1"></i>Loading…
                    </span>
                </button>
            </div>
        @endif

    </div>{{-- /feed --}}

    {{-- ══════════════════════════════════════════════════════════
         COMMENT DRAWER — Alpine drives open/close, Livewire renders list
         ══════════════════════════════════════════════════════════ --}}
    <div class="r-drawer" :class="{open: commentsOpen}">
        <div class="r-drawer-handle"></div>
        <div class="r-drawer-head">
            <span>Comments <span style="color:#555;font-size:13px;font-weight:400" x-text="commentCount > 0 ? '· ' + commentCount : ''"></span></span>
            <button class="r-drawer-close" @click="closeComments()">×</button>
        </div>

        {{-- Comment list rendered by Livewire --}}
        <div class="r-clist" id="commentList">
            @if($showComments && $activeComments && $activeComments->count())
                @foreach($activeComments as $c)
                    <div class="r-citem">
                        <img class="r-cav"
                             src="{{ $c->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($c->user->name ?? 'U').'&size=32&background=333&color=fff' }}"
                             alt="">
                        <div>
                            <div class="r-cname"> <span>@</span>{{ $c->user->username ?? 'User' }}</div>
                            <p class="r-ctext">{{ $c->message }}</p>
                            <div class="r-ctime">{{ $c->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @endforeach
            @elseif($showComments)
                <div class="r-cempty">
                    <i class="fas fa-comment-slash" style="font-size:28px;opacity:.2;display:block;margin-bottom:10px"></i>
                    No comments yet. Be the first!
                </div>
            @endif
        </div>

        {{-- Input row — x-model keeps text in Alpine, send passes it directly --}}
        <div class="r-cinput-row">
            <img class="r-cav-sm"
                 src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&size=32&background=6c3ce1&color=fff' }}"
                 alt="">
            <input class="r-cfield"
                   x-model="commentText"
                   placeholder="Add a comment…"
                   @keydown.enter.prevent="submitComment()"
                   maxlength="500">
            <button class="r-csend"
                    :class="{active: commentText.trim().length > 0}"
                    @click="submitComment()">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         SHARE SHEET — pure Alpine, no server needed
         ══════════════════════════════════════════════════════════ --}}
    <div class="r-share-scrim"
         x-show="shareOpen"
         x-transition.opacity
         @click.self="shareOpen = false"
         style="display:none">
        <div class="r-share-box" @click.stop>
            <div class="r-share-title">Share this video</div>
            <div class="r-copy-row">
                <input class="r-copy-input" :value="shareUrl" readonly x-ref="shareInput">
                <button class="r-copy-btn" @click="copyLink()" x-text="copyLabel"></button>
            </div>
            <div class="r-share-btns">
                <a :href="'https://wa.me/?text='+encodeURIComponent(shareUrl)"
                   target="_blank" style="background:#25d366">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
                <a :href="'https://twitter.com/intent/tweet?url='+encodeURIComponent(shareUrl)"
                   target="_blank" style="background:#000;border:1px solid #333">
                    <i class="fab fa-x-twitter"></i> Twitter
                </a>
                <a :href="'https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent(shareUrl)"
                   target="_blank" style="background:#1877f2">
                    <i class="fab fa-facebook"></i> Facebook
                </a>
                <a :href="'https://t.me/share/url?url='+encodeURIComponent(shareUrl)"
                   target="_blank" style="background:#229ed9">
                    <i class="fab fa-telegram"></i> Telegram
                </a>
            </div>
        </div>
    </div>

</div>{{-- /Alpine root --}}

{{-- ══════════════════════════════════════════════════════════════
     @script — ONE LINE only: register Alpine.data with $wire captured
     ══════════════════════════════════════════════════════════════ --}}
@script
<script>
Alpine.data('rollsPlayer', function(wire) {

    /*
        wire = $wire captured at evaluation time, inside Livewire DOM
        All wire.call() from here always reach the right component instance
    */

    return {

        /* ── State ─────────────────────────────────────────── */
        muted:        false,
        commentsOpen: false,
        commentPostId:null,
        commentText:  '',
        commentCount: 0,
        shareOpen:    false,
        shareUrl:     '',
        copyLabel:    'Copy link',

        /* internals (not reactive, just plain vars) */
        _feed:        null,
        _activeCard:  null,
        _activeVideo: null,
        _activePostId:null,
        _watchStart:  null,
        _watchSent:   false,
        _isFirstPlay: {},
        _seen:        null,
        _raf:         null,
        _observer:    null,

        /* ── Boot ───────────────────────────────────────────── */
        boot() {
            this._feed = this.$refs.feed;
            this._seen = new WeakSet();

            this._setupObserver();
            this._setupKeys();
            this._setupLivewireEvents();

            /* fallback: force first card if observer hasn't fired */
            setTimeout(() => {
                if (!this._activeCard) {
                    const first = this._feed.querySelector('.r-card[data-post-id]');
                    if (first) this._activate(first);
                }
            }, 600);

            /* re-observe after loadMore morphs new cards in */
            Livewire.hook('morph.updated', () => {
                this._feed.querySelectorAll('.r-card[data-post-id]').forEach(c => {
                    if (!this._seen.has(c)) {
                        this._seen.add(c);
                        this._observer.observe(c);
                    }
                });
                /* re-play if morph paused the active video */
                if (this._activeCard && this._activeVideo && this._activeVideo.paused) {
                    this._doPlay(this._activeVideo);
                }
            });
        },

        /* ── Observer ───────────────────────────────────────── */
        _setupObserver() {
            this._observer = new IntersectionObserver((entries) => {
                entries.forEach(e => {
                    if (e.isIntersecting) this._activate(e.target);
                    else                  this._deactivate(e.target);
                });
            }, { root: this._feed, threshold: 0.5 });

            this._feed.querySelectorAll('.r-card[data-post-id]').forEach(c => {
                this._seen.add(c);
                this._observer.observe(c);
            });
        },

        /* ── Activate card (video enters viewport) ──────────── */
        _activate(card) {
            if (this._activeCard === card && this._activeVideo && !this._activeVideo.paused) return;
            if (this._activeCard && this._activeCard !== card) this._deactivate(this._activeCard);

            this._activeCard   = card;
            this._activePostId = card.dataset.postId;
            const video = card.querySelector('.r-video');
            this._activeVideo = video;

            if (this._isFirstPlay[this._activePostId] === undefined) {
                this._isFirstPlay[this._activePostId] = true;
            }

            this._loadAndPlay(card, video);

            this._watchStart = Date.now();
            this._watchSent  = false;

            /* Record view — viewCounted event fires only if it's a new view */
            wire.call('recordView', this._activePostId);

            this._startProgress(video, this._activePostId);
        },

        /* ── Deactivate card ────────────────────────────────── */
        _deactivate(card) {
            const video  = card.querySelector('.r-video');
            const postId = card.dataset.postId;
            if (video && !video.paused) video.pause();
            this._flushWatch(postId);
            this._stopProgress();   // no argument — cancels the single global RAF
        },

        /* ── Load and play ──────────────────────────────────────
           NEVER call play() right after setting .src
           Wait for canplay event first — then play is safe
        ───────────────────────────────────────────────────────── */
        _loadAndPlay(card, video) {
            video.muted = this.muted;

            if (!card.dataset.loaded) {
                card.dataset.loaded = '1';
                video.src = card.dataset.src;

                /* use { once: true } — no need to manually removeEventListener */
                video.addEventListener('canplay',    () => { if (this._activeCard === card) this._doPlay(video); }, { once: true });
                video.addEventListener('loadeddata', () => { if (this._activeCard === card && video.paused) this._doPlay(video); }, { once: true });
                video.load();
            } else {
                this._doPlay(video);
            }
        },

        /* ── doPlay ──────────────────────────────────────────── */
        _doPlay(video) {
            video.muted = this.muted;
            const p = video.play();
            if (p && typeof p.then === 'function') {
                p.catch(() => {
                    /* browser blocked unmuted autoplay — force mute */
                    video.muted = true;
                    this.muted  = true;   /* ← updates Alpine reactive state → icon updates */
                    video.play().catch(() => {});
                });
            }
        },

        /* ── Tap feed to play/pause ─────────────────────────── */
        onTap(e) {
            const card = e.target.closest('.r-card');
            if (!card) return;
            if (e.target.closest('.r-side') ||
                e.target.closest('.r-info') ||
                e.target.closest('.r-prog')) return;

            const video  = card.querySelector('.r-video');
            const postId = card.dataset.postId;

            if (video.paused) {
                this._doPlay(video);
                this._flash(postId, 'fa-play');
                this._watchStart = Date.now();
            } else {
                video.pause();
                this._flash(postId, 'fa-pause');
                this._flushWatch(postId);
            }
        },

        /* ── Mute toggle ────────────────────────────────────────
           FIX: set video.muted from the NEW value after toggling.
           Do NOT read this.muted after toggle — read the param.
        ───────────────────────────────────────────────────────── */
        toggleMute() {
            this.muted = !this.muted;
            if (this._activeVideo) {
                this._activeVideo.muted = this.muted;
            }
            /* Icon updates automatically via :class="muted ? ... : ..." */
        },

        /* ── Like ────────────────────────────────────────────── */
        likePost(postId) {
            const item    = document.getElementById('like-' + postId);
            const countEl = document.getElementById('lc-'   + postId);
            if (!item || !countEl) return;

            const wasLiked = item.classList.contains('liked');

            /* Parse current count — handles plain numbers and K/M formatted */
            const current = this._parseCount(countEl.textContent);

            /* Optimistic update */
            item.classList.toggle('liked', !wasLiked);
            countEl.textContent = this._fmt(Math.max(0, wasLiked ? current - 1 : current + 1));

            /* Heart pop */
            item.classList.add('heart-pop');
            setTimeout(() => item.classList.remove('heart-pop'), 400);

            /* Server — likeConfirmed will correct any mismatch */
            wire.call('toggleLike', postId);
        },

        /* ── Comments ────────────────────────────────────────── */
        openComments(postId) {
            this.commentPostId = postId;
            this.commentText   = '';
            this.commentsOpen  = true;

            /* Read count directly from the sidebar span — single source of truth */
            const ccEl = document.getElementById('cc-' + postId);
            this.commentCount = ccEl ? (parseInt(ccEl.textContent.replace(/\D/g,''), 10) || 0) : 0;

            wire.call('openComments', postId);

            /* Pause video while drawer is open */
            this.$nextTick(() => {
                this._feed.querySelectorAll('.r-video').forEach(v => v.pause());
            });
        },

        closeComments() {
            this.commentsOpen = false;
            wire.call('closeComments');
            if (this._activeVideo && this._activeVideo.paused) {
                this._doPlay(this._activeVideo);
            }
        },

        submitComment() {
            const text = this.commentText.trim();
            if (!text) return;
            const postId = this.commentPostId;
            wire.call('submitComment', text, postId);
            this.commentText = '';

            /* Optimistically bump the count in the drawer heading and sidebar */
            this.commentCount += 1;
            const ccEl = document.getElementById('cc-' + postId);
            if (ccEl) ccEl.textContent = this._fmt(this.commentCount);
        },

        /* ── Share ───────────────────────────────────────────── */
        openShare(url) {
            this.shareUrl   = url;
            this.copyLabel  = 'Copy link';
            this.shareOpen  = true;
        },

        copyLink() {
            navigator.clipboard.writeText(this.shareUrl)
                .then(() => { this.copyLabel = '✓ Copied!'; })
                .catch(() => {
                    if (this.$refs.shareInput) {
                        this.$refs.shareInput.select();
                        document.execCommand('copy');
                        this.copyLabel = '✓ Copied!';
                    }
                });
        },

        /* ── Caption expand ─────────────────────────────────── */
        expandCaption(postId, full) {
            const el = document.getElementById('cap-' + postId);
            if (!el) return;
            el.textContent = full;
            el.classList.add('expanded');
            if (el.nextElementSibling) el.nextElementSibling.remove();
        },

        /* ── Progress bar ────────────────────────────────────────
           Waits for loadedmetadata so video.duration is valid
           before computing the fill percentage.
        ─────────────────────────────────────────────────────── */
        _startProgress(video, postId) {
            this._stopProgress();

            const bar = document.getElementById('prog-' + postId);

            /* RAF loop — updates fill width every animation frame */
            const tick = () => {
                if (this._activePostId !== postId) return;
                if (bar) {
                    const pct = video.duration
                        ? (video.currentTime / video.duration) * 100
                        : 0;
                    bar.style.width = pct + '%';
                }
                this._raf = requestAnimationFrame(tick);
            };

            this._raf = requestAnimationFrame(tick);
        },

        _stopProgress() {
            if (this._raf) {
                cancelAnimationFrame(this._raf);
                this._raf = null;
            }
        },

        scrub(e, progBar) {
            e.stopPropagation();
            if (!this._activeVideo || !this._activeVideo.duration) return;
            const rect    = progBar.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            this._activeVideo.currentTime =
                Math.min(Math.max((clientX - rect.left) / rect.width, 0), 1)
                * this._activeVideo.duration;
        },

        /* ── Flash play/pause indicator ─────────────────────── */
        _flash(postId, icon) {
            const el = document.getElementById('flash-' + postId);
            const ic = document.getElementById('flash-ic-' + postId);
            if (!el || !ic) return;
            ic.className = 'fas ' + icon;
            el.classList.remove('pop');
            void el.offsetWidth;
            el.classList.add('pop');
        },

        /* ── Watch time ─────────────────────────────────────── */
        _flushWatch(postId) {
            if (!this._watchSent && this._watchStart && postId === this._activePostId) {
                const s           = (Date.now() - this._watchStart) / 1000;
                const wasFirst    = this._isFirstPlay[postId] === true;
                if (s > 0.5) {
                    wire.call('recordWatch', postId, s, wasFirst).catch(() => {});
                    this._isFirstPlay[postId] = false;
                    this._watchSent = true;
                }
            }
            this._watchStart = null;
        },

        /* ── Livewire server events ──────────────────────────── */
        _setupLivewireEvents() {

            /* New view recorded — bump the view counter on the card */
            Livewire.on('viewCounted', (payload) => {
                const postId = payload.postId ?? payload[0]?.postId;
                if (!postId) return;
                const el = document.getElementById('vc-' + postId);
                if (!el) return;
                el.textContent = this._fmt(this._parseCount(el.textContent) + 1);
            });

            /*
             * Like confirmed — server is the source of truth.
             * Livewire v3 dispatch() with named params sends:
             *   payload = { postId, liked, count }  as first argument
             * We destructure safely with fallback.
             */
            Livewire.on('likeConfirmed', (payload) => {
                const { postId, liked, count } = payload[0] ?? payload;
                if (!postId) return;
                const item    = document.getElementById('like-' + postId);
                const countEl = document.getElementById('lc-'   + postId);
                if (item)    item.classList.toggle('liked', !!liked);
                if (countEl) countEl.textContent = this._fmt(count ?? 0);
            });

            /* Comment count updated after submitComment */
            Livewire.on('commentCountUpdated', (payload) => {
                const { postId, count } = payload[0] ?? payload;
                if (!postId) return;
                const ccEl = document.getElementById('cc-' + postId);
                if (ccEl) ccEl.textContent = this._fmt(count ?? 0);
                if (this.commentPostId === postId) {
                    this.commentCount = count ?? 0;
                }
            });
        },

        /* ── Keyboard shortcuts ─────────────────────────────── */
         _setupKeys() {
            document.addEventListener('keydown', e => {
                /* Do NOT intercept keypresses when the user is typing */
                const tag = document.activeElement?.tagName;
                if (tag === 'INPUT' || tag === 'TEXTAREA') return;
 
                const h = window.innerHeight;
                if (e.key === 'ArrowDown') this._feed.scrollBy({ top:  h, behavior: 'smooth' });
                if (e.key === 'ArrowUp')   this._feed.scrollBy({ top: -h, behavior: 'smooth' });
                if (e.key === ' ') {
                    e.preventDefault();
                    if (this._activeVideo) {
                        if (this._activeVideo.paused) {
                            this._doPlay(this._activeVideo);
                            this._watchStart = Date.now();
                        } else {
                            this._activeVideo.pause();
                            this._flushWatch(this._activePostId);
                        }
                    }
                }
                if (e.key === 'm')      this.toggleMute();
                if (e.key === 'Escape') { this.closeComments(); this.shareOpen = false; }
            });


            /* Flush watch time when tab is hidden */
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden || !this._watchStart || !this._activePostId || this._watchSent) return;
                const s = (Date.now() - this._watchStart) / 1000;
                if (s < 0.5) return;
                const token = (document.querySelector('meta[name="csrf-token"]') || {}).content;
                const data  = JSON.stringify({
                    post_id:       this._activePostId,
                    watch_seconds: s,
                    is_first_play: this._isFirstPlay[this._activePostId] === true,
                    _token:        token
                });
                navigator.sendBeacon(
                    '{{ url("api/rolls/watch") }}',
                    new Blob([data], { type: 'application/json' })
                );
                this._watchSent = true;
            });
        },

        /* ── Helpers ────────────────────────────────────────── */
        _fmt(n) {
            n = parseInt(n, 10) || 0;
            if (n >= 1000000) return (n/1000000).toFixed(1).replace(/\.0$/,'') + 'M';
            if (n >= 1000)    return (n/1000).toFixed(1).replace(/\.0$/,'') + 'K';
            return n.toString();
        },

        /* Parse a display string back to a number: "1.2K" → 1200, "3M" → 3000000 */
        _parseCount(str) {
            str = (str || '').toString().trim();
            if (str.endsWith('M')) return Math.round(parseFloat(str) * 1000000);
            if (str.endsWith('K')) return Math.round(parseFloat(str) * 1000);
            return parseInt(str.replace(/,/g, ''), 10) || 0;
        },

    }; /* end return */
}); /* end Alpine.data */
</script>
@endscript

</div>{{-- /Livewire root --}}