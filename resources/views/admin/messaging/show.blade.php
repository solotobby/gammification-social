@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-thread {
            display:flex;
            flex-direction:column;
            gap:1rem;
            max-width:860px;
            margin:0 auto;
        }
        .dash-thread-msg {
            display:flex;
            gap:.75rem;
            align-items:flex-start;
        }
        .dash-thread-msg.is-mine { flex-direction:row-reverse; }
        .dash-thread-msg img.avatar {
            width:36px;
            height:36px;
            border-radius:50%;
            object-fit:cover;
            flex:none;
        }
        .dash-thread-bubble {
            max-width:min(100%, 520px);
            padding:.75rem .9rem;
            border-radius:14px;
            background:#f8fafc;
            border:1px solid var(--dash-border);
        }
        .dash-thread-msg.is-mine .dash-thread-bubble {
            background: var(--dash-accent-soft);
            border-color:#c7d2fe;
        }
        .dash-thread-meta {
            font-size:.72rem;
            color:var(--dash-muted);
            margin-bottom:.35rem;
        }
        .dash-thread-body {
            font-size:.875rem;
            line-height:1.5;
            white-space:pre-wrap;
            word-break:break-word;
        }
        .dash-thread-images {
            display:grid;
            grid-template-columns:repeat(auto-fill, minmax(120px, 1fr));
            gap:.5rem;
            margin-top:.5rem;
        }
        .dash-thread-images a {
            display:block;
            border-radius:10px;
            overflow:hidden;
            border:1px solid var(--dash-border);
        }
        .dash-thread-images img {
            display:block;
            width:100%;
            aspect-ratio:1;
            object-fit:cover;
        }
        .dash-participant-grid {
            display:flex;
            flex-wrap:wrap;
            gap:.75rem;
        }
        .dash-participant {
            display:flex;
            align-items:center;
            gap:.55rem;
            padding:.55rem .75rem;
            border:1px solid var(--dash-border);
            border-radius:12px;
            background:#fff;
            text-decoration:none;
            color:inherit;
        }
        .dash-participant img {
            width:32px;
            height:32px;
            border-radius:50%;
            object-fit:cover;
        }
    </style>
@endsection

@section('content')
@php
    $participants = $conversation->participants;
@endphp
<div class="content p-0"><div class="dash">
    <header class="dash-header">
        <div>
            <h1>Conversation</h1>
            <p>{{ $messaging->participantLabel($conversation) }} · {{ number_format($message_count) }} messages · {{ number_format($attachment_count) }} media</p>
        </div>
        <div style="display:flex;gap:.5rem;flex-wrap:wrap">
            <a href="{{ route('admin.messaging.index', ['tab' => 'conversations']) }}" class="dash-btn dash-btn--ghost">
                <i class="fa fa-arrow-left"></i> All conversations
            </a>
            <a href="{{ route('admin.messaging.index', ['tab' => 'media', 'q' => $conversation->id]) }}" class="dash-btn dash-btn--ghost">
                <i class="fa fa-image"></i> Media in thread
            </a>
        </div>
    </header>

    <section class="dash-section">
        <div class="dash-card">
            <div class="dash-card__head"><h2 class="dash-card__title">Participants</h2></div>
            <div class="dash-card__body">
                <div class="dash-participant-grid">
                    @foreach ($participants as $participant)
                        @if ($participant->user)
                            <a href="{{ route('admin.users.show', $participant->user) }}" class="dash-participant">
                                <img src="{{ $participant->user->avatar ?? asset('src/assets/media/avatars/avatar13.jpg') }}" alt="">
                                <div>
                                    <div style="font-weight:600;font-size:.875rem">{{ displayName($participant->user->name) }}</div>
                                    <div class="dash-muted" style="font-size:.75rem">{{ '@'.$participant->user->username }}</div>
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
                <div class="dash-muted" style="margin-top:.75rem;font-size:.8125rem">
                    Conversation ID: <code>{{ $conversation->id }}</code>
                    @if ($conversation->last_message_at)
                        · Last activity {{ $conversation->last_message_at->diffForHumans() }}
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="dash-section">
        <div class="dash-card">
            <div class="dash-card__head">
                <h2 class="dash-card__title">Message thread</h2>
                <span class="dash-muted">Read-only admin view</span>
            </div>
            <div class="dash-card__body">
                <div class="dash-thread">
                    @php
                        $ordered = $messages->getCollection()->sortBy('created_at');
                    @endphp
                    @forelse ($ordered as $message)
                        @php
                            $sender = $message->user;
                            $isFirstParticipant = $participants->first()?->user_id === $message->user_id;
                        @endphp
                        <div class="dash-thread-msg @if($isFirstParticipant) is-mine @endif">
                            <img class="avatar" src="{{ $sender?->avatar ?? asset('src/assets/media/avatars/avatar13.jpg') }}" alt="">
                            <div class="dash-thread-bubble">
                                <div class="dash-thread-meta">
                                    <strong>{{ $sender ? displayName($sender->name) : 'Unknown' }}</strong>
                                    · {{ $message->created_at?->format('M d, Y g:i A') }}
                                    · {{ $messaging->typeLabel($message->type) }}
                                </div>
                                @if ($message->body)
                                    <div class="dash-thread-body">{{ $message->body }}</div>
                                @endif
                                @if ($message->attachments->isNotEmpty())
                                    <div class="dash-thread-images">
                                        @foreach ($message->attachments->sortBy('sort_order') as $attachment)
                                            <a href="{{ $attachment->url() }}" target="_blank" rel="noopener">
                                                <img src="{{ $attachment->url() }}" alt="Attachment" loading="lazy">
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="dash-empty">This conversation has no messages.</div>
                    @endforelse
                </div>

                @if ($messages->hasPages())
                    <div class="dash-pagination" style="margin-top:1.25rem">{{ $messages->links('pagination::bootstrap-5') }}</div>
                @endif
            </div>
        </div>
    </section>
</div></div>
@endsection
