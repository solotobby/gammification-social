@php
    $active = $thread['meta'];
    $messages = $thread['messages'];
    $unreadTotal = collect($conversations)->sum(fn ($c) => (int) ($c['unread'] ?? 0));
@endphp

<div class="msg-page"
    x-data="{
        mobilePane: {{ $active ? "'chat'" : "'list'" }},
        lightbox: null,
        emojiOpen: false,
        attachOpen: false,
        infoOpen: false,
        openChat() { this.mobilePane = 'chat' },
        openList() { this.mobilePane = 'list' },
        openLightbox(src) { this.lightbox = src },
        closeLightbox() { this.lightbox = null },
    }"
    x-bind:data-pane="mobilePane"
    @keydown.escape.window="lightbox = null; infoOpen = false; emojiOpen = false; attachOpen = false">

    <style>
        [x-cloak] { display: none !important; }

        .msg-page {
            --msg-violet: #5A4FDC;
            --msg-ink: #111827;
            --msg-mute: #6b7280;
            --msg-line: #e8eaed;
            --msg-soft: #f5f6f8;
            --msg-mine: #5A4FDC;
            --msg-theirs: #ffffff;
            --msg-radius: 18px;
            --msg-h: calc(100dvh - 52px - 2.5rem);
            margin: -0.25rem -12px 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: var(--msg-ink);
        }

        @media (min-width: 992px) {
            .msg-page { margin-inline: 0; }
        }

        .msg-shell {
            display: grid;
            grid-template-columns: 1fr;
            height: var(--msg-h);
            min-height: 420px;
            max-height: 860px;
            background: #fff;
            border: 1px solid var(--msg-line);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(17, 24, 39, .04);
        }

        @media (min-width: 900px) {
            .msg-shell {
                grid-template-columns: minmax(300px, 360px) 1fr;
            }
        }

        .msg-pane {
            display: none;
            flex-direction: column;
            min-width: 0;
            min-height: 0;
            height: 100%;
            background: #fff;
        }

        .msg-list-pane { border-right: 1px solid var(--msg-line); }

        /* Mobile: show one pane at a time via data-pane on .msg-page */
        @media (max-width: 899.98px) {
            .msg-page[data-pane="list"] .msg-list-pane,
            .msg-page:not([data-pane]) .msg-list-pane {
                display: flex;
            }
            .msg-page[data-pane="chat"] .msg-chat-pane {
                display: flex;
            }
        }

        @media (min-width: 900px) {
            .msg-list-pane,
            .msg-chat-pane {
                display: flex !important;
            }
        }

        .msg-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 14px 16px;
            border-bottom: 1px solid var(--msg-line);
            flex: none;
        }

        .msg-top h1 {
            margin: 0;
            font-size: 1.15rem;
            font-weight: 750;
            letter-spacing: -.02em;
        }

        .msg-top-actions {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .msg-icon {
            width: 36px;
            height: 36px;
            border: 0;
            border-radius: 10px;
            background: transparent;
            color: #374151;
            display: inline-grid;
            place-items: center;
            cursor: pointer;
            text-decoration: none;
            transition: background .15s ease;
        }

        .msg-icon:hover { background: var(--msg-soft); color: var(--msg-ink); }

        .msg-search {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 12px 10px;
            padding: 0 12px;
            height: 40px;
            border-radius: 12px;
            background: var(--msg-soft);
            border: 1px solid transparent;
        }

        .msg-search:focus-within {
            background: #fff;
            border-color: #d7dbe3;
        }

        .msg-search i { color: var(--msg-mute); font-size: 13px; }

        .msg-search input {
            flex: 1;
            border: 0;
            background: transparent;
            outline: none;
            font-size: 14px;
            color: var(--msg-ink);
            min-width: 0;
        }

        .msg-filters {
            display: flex;
            gap: 6px;
            padding: 0 12px 12px;
            flex: none;
        }

        .msg-chip {
            border: 0;
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 650;
            background: var(--msg-soft);
            color: var(--msg-mute);
            cursor: pointer;
        }

        .msg-chip.is-on {
            background: rgba(90, 79, 220, .1);
            color: var(--msg-violet);
        }

        .msg-convos {
            flex: 1;
            overflow-y: auto;
            min-height: 0;
            padding: 0 6px 10px;
        }

        .msg-convo {
            width: 100%;
            display: grid;
            grid-template-columns: 48px 1fr auto;
            gap: 10px;
            align-items: center;
            padding: 10px;
            border: 0;
            border-radius: 14px;
            background: transparent;
            text-align: left;
            cursor: pointer;
            transition: background .15s ease;
        }

        .msg-convo:hover { background: var(--msg-soft); }
        .msg-convo.is-active { background: rgba(90, 79, 220, .08); }

        .msg-av-wrap {
            position: relative;
            width: 44px;
            height: 44px;
        }

        .msg-av {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            background: #e5e7eb;
        }

        .msg-online {
            position: absolute;
            right: 0;
            bottom: 0;
            width: 11px;
            height: 11px;
            border-radius: 50%;
            background: #22c55e;
            border: 2px solid #fff;
        }

        .msg-convo-main { min-width: 0; }

        .msg-convo-row {
            display: flex;
            align-items: center;
            gap: 6px;
            min-width: 0;
        }

        .msg-convo-name {
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .msg-pin,
        .msg-mute {
            color: var(--msg-mute);
            font-size: 11px;
        }

        .msg-convo-preview {
            margin-top: 2px;
            font-size: 12.5px;
            color: var(--msg-mute);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .msg-convo.is-unread .msg-convo-preview {
            color: var(--msg-ink);
            font-weight: 600;
        }

        .msg-typing {
            color: var(--msg-violet);
            font-weight: 650;
            font-style: italic;
        }

        .msg-convo-meta {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 6px;
            flex: none;
        }

        .msg-convo-time {
            font-size: 11px;
            color: var(--msg-mute);
            white-space: nowrap;
        }

        .msg-unread {
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            border-radius: 999px;
            background: var(--msg-violet);
            color: #fff;
            font-size: 10px;
            font-weight: 750;
            display: grid;
            place-items: center;
        }

        .msg-receipt {
            display: inline-flex;
            color: #9ca3af;
            font-size: 12px;
        }

        .msg-receipt.is-read { color: #38bdf8; }

        .msg-chat-body {
            display: flex;
            flex-direction: column;
            min-height: 0;
            height: 100%;
            flex: 1;
            width: 100%;
        }

        .msg-chat-head {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-bottom: 1px solid var(--msg-line);
            flex: none;
            background: #fff;
        }

        .msg-back { display: inline-grid; }

        @media (min-width: 900px) {
            .msg-back { display: none !important; }
        }

        .msg-chat-head-info {
            flex: 1;
            min-width: 0;
        }

        .msg-chat-head-info strong {
            display: block;
            font-size: 14.5px;
            font-weight: 750;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .msg-chat-head-info span {
            font-size: 12px;
            color: var(--msg-mute);
        }

        .msg-chat-head-info .is-online { color: #16a34a; font-weight: 600; }

        .msg-thread {
            flex: 1;
            overflow-y: auto;
            min-height: 0;
            padding: 18px 14px 10px;
            background:
                radial-gradient(circle at top right, rgba(90, 79, 220, .04), transparent 40%),
                #fafafb;
        }

        .msg-date {
            display: flex;
            justify-content: center;
            margin: 10px 0 14px;
        }

        .msg-date span {
            font-size: 11px;
            font-weight: 650;
            color: var(--msg-mute);
            background: rgba(255, 255, 255, .9);
            border: 1px solid var(--msg-line);
            border-radius: 999px;
            padding: 4px 10px;
        }

        .msg-row {
            display: flex;
            margin-bottom: 8px;
        }

        .msg-row.is-mine { justify-content: flex-end; }
        .msg-row.is-theirs { justify-content: flex-start; }

        .msg-bubble {
            max-width: min(78%, 460px);
            border-radius: var(--msg-radius);
            padding: 10px 12px;
            position: relative;
            box-shadow: 0 1px 1px rgba(17, 24, 39, .04);
        }

        .msg-row.is-mine .msg-bubble {
            background: var(--msg-mine);
            color: #fff;
            border-bottom-right-radius: 6px;
        }

        .msg-row.is-theirs .msg-bubble {
            background: var(--msg-theirs);
            color: var(--msg-ink);
            border: 1px solid var(--msg-line);
            border-bottom-left-radius: 6px;
        }

        .msg-sender {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: var(--msg-violet);
            margin-bottom: 3px;
        }

        .msg-text {
            font-size: 14px;
            line-height: 1.45;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .msg-meta {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 5px;
            margin-top: 4px;
        }

        .msg-time {
            font-size: 10.5px;
            opacity: .72;
        }

        .msg-row.is-theirs .msg-time { color: var(--msg-mute); opacity: 1; }

        .msg-images {
            display: grid;
            gap: 4px;
            margin-bottom: 6px;
            border-radius: 14px;
            overflow: hidden;
        }

        .msg-images.g1 { grid-template-columns: 1fr; }
        .msg-images.g2 { grid-template-columns: 1fr 1fr; }
        .msg-images.g3,
        .msg-images.g4 { grid-template-columns: 1fr 1fr; }

        .msg-images button {
            border: 0;
            padding: 0;
            background: #111;
            cursor: zoom-in;
            aspect-ratio: 1;
            overflow: hidden;
        }

        .msg-images.g1 button { aspect-ratio: 4/3; max-height: 280px; }

        .msg-images img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform .2s ease;
        }

        .msg-images button:hover img { transform: scale(1.03); }

        .msg-compose {
            flex: none;
            border-top: 1px solid var(--msg-line);
            background: #fff;
            padding: 10px 12px 12px;
        }

        .msg-preview {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 8px;
        }

        .msg-preview-item {
            position: relative;
            width: 64px;
            height: 64px;
            border-radius: 10px;
            overflow: hidden;
            flex: none;
            background: var(--msg-soft);
        }

        .msg-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .msg-preview-item button {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 18px;
            height: 18px;
            border: 0;
            border-radius: 50%;
            background: rgba(0,0,0,.55);
            color: #fff;
            font-size: 11px;
            line-height: 1;
            cursor: pointer;
        }

        .msg-compose-row {
            display: flex;
            align-items: flex-end;
            gap: 6px;
        }

        .msg-compose-box {
            flex: 1;
            min-width: 0;
            display: flex;
            align-items: flex-end;
            gap: 4px;
            background: var(--msg-soft);
            border-radius: 16px;
            padding: 6px 8px;
            border: 1px solid transparent;
        }

        .msg-compose-box:focus-within {
            background: #fff;
            border-color: #d7dbe3;
        }

        .msg-compose-box textarea {
            flex: 1;
            border: 0;
            background: transparent;
            resize: none;
            outline: none;
            font-size: 14px;
            line-height: 1.4;
            max-height: 120px;
            min-height: 24px;
            padding: 6px 4px;
            color: var(--msg-ink);
        }

        .msg-send {
            width: 40px;
            height: 40px;
            border: 0;
            border-radius: 50%;
            background: var(--msg-violet);
            color: #fff;
            display: grid;
            place-items: center;
            cursor: pointer;
            flex: none;
            box-shadow: 0 6px 16px rgba(90, 79, 220, .28);
            transition: filter .15s ease, transform .15s ease;
        }

        .msg-send:hover { filter: brightness(1.05); }
        .msg-send:active { transform: scale(.96); }
        .msg-send:disabled {
            opacity: .45;
            cursor: not-allowed;
            box-shadow: none;
        }

        .msg-empty {
            flex: 1;
            display: grid;
            place-items: center;
            text-align: center;
            padding: 24px;
            color: var(--msg-mute);
        }

        .msg-empty h3 {
            margin: 0 0 6px;
            color: var(--msg-ink);
            font-size: 1.05rem;
        }

        .msg-empty p { margin: 0; font-size: 13px; max-width: 280px; }

        .msg-menu {
            position: absolute;
            right: 12px;
            top: 52px;
            width: 200px;
            background: #fff;
            border: 1px solid var(--msg-line);
            border-radius: 12px;
            box-shadow: 0 12px 30px rgba(17, 24, 39, .12);
            padding: 6px;
            z-index: 5;
        }

        .msg-menu button {
            width: 100%;
            border: 0;
            background: transparent;
            text-align: left;
            padding: 9px 10px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 550;
            color: var(--msg-ink);
            cursor: pointer;
        }

        .msg-menu button:hover { background: var(--msg-soft); }

        .msg-lightbox {
            position: fixed;
            inset: 0;
            z-index: 2000;
            background: rgba(10, 10, 12, .88);
            display: grid;
            place-items: center;
            padding: 20px;
        }

        .msg-lightbox img {
            max-width: min(920px, 100%);
            max-height: min(85dvh, 100%);
            border-radius: 12px;
            object-fit: contain;
        }

        .msg-lightbox-close {
            position: absolute;
            top: 16px;
            right: 16px;
            width: 40px;
            height: 40px;
            border: 0;
            border-radius: 50%;
            background: rgba(255,255,255,.12);
            color: #fff;
            font-size: 20px;
            cursor: pointer;
        }

        .msg-badge-dot {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ef4444;
            border: 1.5px solid #fff;
        }

        .msg-proto {
            margin: 8px 16px 0;
            font-size: 11px;
            color: var(--msg-mute);
            text-align: center;
        }

        @media (max-width: 479.98px) {
            .msg-page { --msg-h: calc(100dvh - 52px - 1rem); }
            .msg-shell {
                border-radius: 0;
                border-left: 0;
                border-right: 0;
                max-height: none;
            }
            .msg-bubble { max-width: 86%; }
        }
    </style>

    <div class="msg-shell" wire:poll.2s="pollMessages">
        {{-- ===== LIST ===== --}}
        <aside class="msg-pane msg-list-pane">
            <div class="msg-top">
                <h1>Messages @if ($unreadTotal > 0)<span style="color:var(--msg-violet);font-size:.9rem">({{ $unreadTotal }})</span>@endif</h1>
                <div class="msg-top-actions">
                    <button type="button" class="msg-icon" title="New message" aria-label="New message" wire:click="openNewModal">
                        <i class="fa fa-edit"></i>
                    </button>
                </div>
            </div>

            <label class="msg-search">
                <i class="fa fa-search"></i>
                <input type="search"
                    wire:model.live.debounce.200ms="search"
                    placeholder="Search conversations"
                    aria-label="Search conversations">
            </label>

            <div class="msg-filters">
                <button type="button" class="msg-chip {{ $listFilter === 'all' ? 'is-on' : '' }}" wire:click="setFilter('all')">All</button>
                <button type="button" class="msg-chip {{ $listFilter === 'unread' ? 'is-on' : '' }}" wire:click="setFilter('unread')">Unread</button>
            </div>

            <div class="msg-convos">
                @forelse ($conversations as $c)
                    <button type="button"
                        class="msg-convo {{ $activeId === $c['id'] ? 'is-active' : '' }} {{ ($c['unread'] ?? 0) > 0 ? 'is-unread' : '' }}"
                        wire:click="selectConversation('{{ $c['id'] }}')"
                        @click="openChat()">
                        <div class="msg-av-wrap">
                            <img class="msg-av" src="{{ $c['avatar'] }}" alt="">
                            @if (! empty($c['online']))
                                <span class="msg-online" title="Online"></span>
                            @endif
                        </div>
                        <div class="msg-convo-main">
                            <div class="msg-convo-row">
                                <div class="msg-convo-name">{{ $c['name'] }}</div>
                                @if (! empty($c['pinned'])) <i class="fa fa-thumbtack msg-pin" title="Pinned"></i> @endif
                                @if (! empty($c['muted'])) <i class="fa fa-bell-slash msg-mute" title="Muted"></i> @endif
                                @if (! empty($c['official'])) <i class="fa fa-check-circle" style="color:#1d9bf0;font-size:12px" title="Official"></i> @endif
                            </div>
                            <div class="msg-convo-preview">
                                @if (! empty($c['has_image']))
                                    <i class="fa fa-image" style="margin-right:3px"></i>{{ $c['last_message'] }}
                                @elseif (! empty($c['last_from_me']))
                                    <span class="msg-receipt {{ ($c['last_status'] ?? '') === 'read' ? 'is-read' : '' }}">
                                        @if (($c['last_status'] ?? '') === 'sent')
                                            <i class="fa fa-check"></i>
                                        @else
                                            <i class="fa fa-check-double"></i>
                                        @endif
                                    </span>
                                    You: {{ $c['last_message'] }}
                                @else
                                    {{ $c['last_message'] }}
                                @endif
                            </div>
                        </div>
                        <div class="msg-convo-meta">
                            <span class="msg-convo-time">{{ $c['last_at'] }}</span>
                            @if (($c['unread'] ?? 0) > 0)
                                <span class="msg-unread">{{ $c['unread'] }}</span>
                            @endif
                        </div>
                    </button>
                @empty
                    <div class="msg-empty">
                        <h3>No conversations</h3>
                        <p>Try another search or start a new message.</p>
                    </div>
                @endforelse
            </div>
        </aside>

        {{-- ===== CHAT ===== --}}
        <section class="msg-pane msg-chat-pane">
            @if ($active)
            <div class="msg-chat-body">
            <div class="msg-chat-head">
                <button type="button" class="msg-icon msg-back" @click="openList()" aria-label="Back to chats">
                    <i class="fa fa-arrow-left"></i>
                </button>

                <div class="msg-av-wrap" style="width:40px;height:40px">
                    <img class="msg-av" style="width:40px;height:40px" src="{{ $active['avatar'] }}" alt="">
                    @if (! empty($active['online']))
                        <span class="msg-online"></span>
                    @endif
                </div>

                <div class="msg-chat-head-info">
                    <strong>{{ $active['name'] }}</strong>
                    <span>
                        @if (! empty($active['online']))
                            <span class="is-online">Active now</span>
                        @else
                            {{ '@' . ($active['username'] ?? 'user') }}
                        @endif
                    </span>
                </div>

                <div class="msg-top-actions" style="position:relative">
                    <button type="button" class="msg-icon" @click="infoOpen = !infoOpen" aria-label="More">
                        <i class="fa fa-ellipsis-v"></i>
                    </button>
                    <div class="msg-menu" x-show="infoOpen" x-cloak @click.outside="infoOpen = false">
                        @if (! empty($active['username']))
                            <a href="{{ url('profile/'.$active['username']) }}">View profile</a>
                        @endif
                        <button type="button" wire:click="toggleMute" @click="infoOpen = false">
                            {{ ! empty($active['muted']) ? 'Unmute notifications' : 'Mute notifications' }}
                        </button>
                        <button type="button" wire:click="togglePin" @click="infoOpen = false">
                            {{ ! empty($active['pinned']) ? 'Unpin conversation' : 'Pin conversation' }}
                        </button>
                        <button type="button" wire:click="blockActiveUser" @click="infoOpen = false" style="color:#b91c1c">
                            {{ ! empty($active['blocked']) ? 'Unblock user' : 'Block user' }}
                        </button>
                        <button type="button" wire:click="deleteChat" @click="infoOpen = false" style="color:#b91c1c">Delete chat</button>
                    </div>
                </div>
            </div>

            <div class="msg-thread" id="msg-thread">
                @foreach ($messages as $m)
                    @if (($m['type'] ?? '') === 'date')
                        <div class="msg-date"><span>{{ $m['label'] }}</span></div>
                        @continue
                    @endif

                    <div class="msg-row {{ ! empty($m['mine']) ? 'is-mine' : 'is-theirs' }}">
                        <div class="msg-bubble">
                            @if (! empty($m['sender']) && empty($m['mine']))
                                <span class="msg-sender">{{ $m['sender'] }}</span>
                            @endif

                            @if (($m['type'] ?? '') === 'image')
                                @php $imgs = $m['images'] ?? []; $count = count($imgs); @endphp
                                <div class="msg-images g{{ min($count, 4) }}">
                                    @foreach (array_slice($imgs, 0, 4) as $img)
                                        <button type="button" @click="openLightbox('{{ $img }}')">
                                            <img src="{{ $img }}" alt="Shared image" loading="lazy">
                                        </button>
                                    @endforeach
                                </div>
                                @if (! empty($m['caption']))
                                    <div class="msg-text">{{ $m['caption'] }}</div>
                                @endif
                            @else
                                <div class="msg-text">{{ $m['body'] ?? '' }}</div>
                            @endif

                            <div class="msg-meta">
                                <span class="msg-time">{{ $m['at'] ?? '' }}</span>
                                @if (! empty($m['mine']))
                                    @php $st = $m['status'] ?? 'sent'; @endphp
                                    <span class="msg-receipt {{ $st === 'read' ? 'is-read' : '' }}" title="{{ ucfirst($st) }}">
                                        @if ($st === 'sent')
                                            <i class="fa fa-check"></i>
                                        @else
                                            <i class="fa fa-check-double"></i>
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="msg-compose">
                @if ($errors->has('draft'))
                    <div style="padding:.35rem .75rem;color:#b91c1c;font-size:.82rem;">{{ $errors->first('draft') }}</div>
                @endif

                @if (count($uploads) > 0)
                    <div class="msg-preview">
                        @foreach ($uploads as $index => $upload)
                            <div class="msg-preview-item">
                                <img src="{{ $upload->temporaryUrl() }}" alt="">
                                <button type="button" wire:click="removeUpload({{ $index }})" aria-label="Remove">&times;</button>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="msg-compose-row">
                    <div class="msg-compose-box">
                        <label class="msg-icon" title="Attach image" style="cursor:pointer">
                            <i class="fa fa-image"></i>
                            <input type="file" accept="image/*" multiple wire:model="uploads" hidden>
                        </label>
                        <textarea
                            rows="1"
                            placeholder="Message…"
                            wire:model.live="draft"
                            @keydown.enter="if ($event.shiftKey) return; $event.preventDefault(); $wire.sendMessage()"
                        ></textarea>
                    </div>
                    <button type="button" class="msg-send" wire:click="sendMessage" wire:loading.attr="disabled" aria-label="Send">
                        <i class="fa fa-paper-plane" wire:loading.remove wire:target="sendMessage"></i>
                        <i class="fa fa-spinner fa-spin" wire:loading wire:target="sendMessage"></i>
                    </button>
                </div>
            </div>
            </div>
            @else
                <div class="msg-empty" style="flex:1;display:grid;place-items:center;">
                    <div style="text-align:center;padding:2rem;">
                        <h3>Select a conversation</h3>
                        <p class="dash-muted">Pick a chat or start a new message.</p>
                        <button type="button" class="msg-chip is-on" wire:click="openNewModal" style="margin-top:1rem;">New message</button>
                    </div>
                </div>
            @endif
        </section>
    </div>

    @if ($showNewModal)
        <div style="position:fixed;inset:0;background:rgba(15,23,42,.45);z-index:1200;display:grid;place-items:center;padding:1rem;" wire:click.self="closeNewModal">
            <div style="background:#fff;border-radius:16px;max-width:420px;width:100%;padding:1.25rem;border:1px solid #e5e7eb;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
                    <h2 style="margin:0;font-size:1.05rem;">New message</h2>
                    <button type="button" wire:click="closeNewModal" style="border:0;background:transparent;font-size:1.25rem;">&times;</button>
                </div>
                <input type="search" wire:model.live.debounce.250ms="newUserQuery" placeholder="Search by name or @username" class="dash-input" style="width:100%;margin-bottom:.75rem;">
                <div style="max-height:260px;overflow:auto;">
                    @forelse ($searchUsers as $user)
                        <button type="button" wire:click="startConversation('{{ $user->username }}')" style="width:100%;display:flex;align-items:center;gap:.65rem;padding:.65rem;border:0;background:transparent;text-align:left;border-radius:10px;">
                            <img src="{{ $user->avatar ?: asset('src/assets/media/avatars/avatar13.jpg') }}" alt="" style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                            <span>
                                <strong style="display:block;">{{ $user->name }}</strong>
                                <span style="color:#6b7280;font-size:.82rem;">{{ '@'.$user->username }}</span>
                            </span>
                        </button>
                    @empty
                        <p style="color:#6b7280;font-size:.88rem;margin:0;">Type at least 2 characters to find someone.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    {{-- Lightbox --}}
    <template x-if="lightbox">
        <div class="msg-lightbox" @click.self="closeLightbox()">
            <button type="button" class="msg-lightbox-close" @click="closeLightbox()" aria-label="Close">&times;</button>
            <img :src="lightbox" alt="Full size">
        </div>
    </template>
</div>

<script>
    function scrollMessageThread(force = false) {
        const el = document.getElementById('msg-thread');
        if (!el) return;
        const nearBottom = el.scrollHeight - el.scrollTop - el.clientHeight < 120;
        if (force || nearBottom) {
            el.scrollTop = el.scrollHeight;
        }
    }

    document.addEventListener('livewire:navigated', () => scrollMessageThread(true));
    document.addEventListener('DOMContentLoaded', () => scrollMessageThread(true));
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('message-thread-scroll', () => scrollMessageThread(true));
        Livewire.hook('message.processed', ({ component }) => {
            if (component?.name === 'user.messages') {
                scrollMessageThread();
            }
        });
    });
</script>
