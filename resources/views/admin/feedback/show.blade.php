@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-dl { display: grid; grid-template-columns: 140px 1fr; gap: .5rem 1rem; margin: 0; }
        .dash-dl dt { margin: 0; font-weight: 600; color: var(--dash-muted); }
        .dash-dl dd { margin: 0; }
        .dash-field label { display: block; margin-bottom: .375rem; font-size: .8125rem; font-weight: 600; color: var(--dash-muted); }
        .dash-badge--danger { background: rgba(220, 53, 69, .12); color: #b42318; }
        .dash-badge--warn { background: rgba(245, 158, 11, .14); color: #b54708; }
        .dash-badge--info { background: rgba(59, 130, 246, .12); color: #1d4ed8; }
        .dash-badge--success { background: rgba(16, 185, 129, .12); color: #067647; }
        .fb-thread {
            display: grid;
            gap: .75rem;
            max-height: min(62vh, 560px);
            overflow-y: auto;
            padding: .25rem;
            margin-bottom: 1rem;
        }
        .fb-bubble {
            max-width: min(92%, 560px);
            padding: .85rem 1rem;
            border-radius: 12px;
            border: 1px solid var(--dash-border);
            background: #fff;
        }
        .fb-bubble--user { margin-right: auto; background: #f8fafc; }
        .fb-bubble--staff {
            margin-left: auto;
            background: rgba(59, 130, 246, .08);
            border-color: rgba(59, 130, 246, .25);
        }
        .fb-bubble__meta {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            justify-content: space-between;
            margin-bottom: .4rem;
            font-size: .75rem;
            font-weight: 600;
            color: var(--dash-muted);
        }
        .fb-bubble__body {
            margin: 0;
            white-space: pre-wrap;
            word-break: break-word;
            line-height: 1.5;
            font-size: .9rem;
        }
        @media (max-width: 960px) {
            .dash-feedback-grid { grid-template-columns: 1fr !important; }
        }
    </style>
@endsection

@section('content')
    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>{{ $feedback->subject }}</h1>
                    <p>Conversation with {{ $feedback->user?->username ? '@'.$feedback->user->username : 'user' }}</p>
                </div>
                <a href="{{ route('admin.feedback.index') }}" class="dash-btn dash-btn--ghost">← Back</a>
            </header>

            @if (session('success'))
                <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="dash-alert dash-alert--error">{{ session('error') }}</div>
            @endif

            <div class="dash-grid dash-feedback-grid" style="grid-template-columns: 1.5fr 1fr; gap: 1rem;">
                <div class="dash-card">
                    <div class="dash-card__head">
                        <h2 class="dash-card__title">Thread</h2>
                        <div style="display:flex;flex-wrap:wrap;gap:.5rem;">
                            <span class="dash-badge dash-badge--info">{{ $feedback->typeLabel() }}</span>
                            <span class="dash-badge dash-badge--warn">{{ $feedback->statusLabel() }}</span>
                        </div>
                    </div>
                    <div class="dash-card__body">
                        <div class="fb-thread" id="admin-fb-thread">
                            @forelse ($feedback->messages as $msg)
                                <div class="fb-bubble {{ $msg->is_staff ? 'fb-bubble--staff' : 'fb-bubble--user' }}">
                                    <div class="fb-bubble__meta">
                                        <span>
                                            @if ($msg->is_staff)
                                                Support · {{ $msg->user?->name ?? 'Admin' }}
                                            @else
                                                {{ '@'.($msg->user?->username ?? 'user') }}
                                            @endif
                                        </span>
                                        <span>{{ $msg->created_at?->format('M j, Y · H:i') }}</span>
                                    </div>
                                    <p class="fb-bubble__body">{{ $msg->body }}</p>
                                </div>
                            @empty
                                <p class="dash-muted">No messages in this thread yet.</p>
                            @endforelse
                        </div>

                        @if ($feedback->isOpen())
                            <form method="post" action="{{ route('admin.feedback.reply', $feedback) }}">
                                @csrf
                                <div class="dash-field" style="margin-bottom:.75rem;">
                                    <label for="body">Reply to user</label>
                                    <textarea id="body" name="body" class="dash-input" rows="4"
                                        placeholder="Visible to the user…" required>{{ old('body') }}</textarea>
                                    @error('body') <div class="dash-muted" style="color:#b42318;margin-top:.35rem;">{{ $message }}</div> @enderror
                                </div>
                                <button type="submit" class="dash-btn dash-btn--primary">Send reply</button>
                            </form>
                        @else
                            <div class="dash-alert dash-alert--error" style="margin:0;">Conversation is closed. Change status to reopen before replying.</div>
                        @endif
                    </div>
                </div>

                <div style="display:grid;gap:1rem;">
                    <div class="dash-card">
                        <div class="dash-card__head">
                            <h2 class="dash-card__title">User</h2>
                        </div>
                        <div class="dash-card__body">
                            @if ($feedback->user)
                                <dl class="dash-dl">
                                    <dt>Name</dt>
                                    <dd>{{ $feedback->user->name }}</dd>
                                    <dt>Username</dt>
                                    <dd>
                                        <a href="{{ route('admin.users.show', $feedback->user) }}" class="dash-link">
                                            {{ '@'.$feedback->user->username }}
                                        </a>
                                    </dd>
                                    <dt>Email</dt>
                                    <dd>{{ $feedback->user->email }}</dd>
                                </dl>
                            @else
                                <p class="dash-muted">User unavailable.</p>
                            @endif
                        </div>
                    </div>

                    <div class="dash-card">
                        <div class="dash-card__head">
                            <h2 class="dash-card__title">Status & internal note</h2>
                        </div>
                        <div class="dash-card__body">
                            <form method="post" action="{{ route('admin.feedback.update', $feedback) }}">
                                @csrf
                                @method('PUT')
                                <div class="dash-field" style="margin-bottom:1rem;">
                                    <label for="status">Status</label>
                                    <select id="status" name="status" class="dash-input" required>
                                        @foreach ($statuses as $value => $label)
                                            <option value="{{ $value }}" @selected(old('status', $feedback->status) === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="dash-field" style="margin-bottom:1rem;">
                                    <label for="admin_note">Internal note (not visible to user)</label>
                                    <textarea id="admin_note" name="admin_note" class="dash-input" rows="3"
                                        placeholder="Private team note…">{{ old('admin_note', $feedback->admin_note) }}</textarea>
                                </div>
                                @if ($feedback->reviewed_at)
                                    <p class="dash-muted" style="font-size:.8rem;margin-bottom:1rem;">
                                        Last reviewed {{ $feedback->reviewed_at->diffForHumans() }}
                                        @if ($feedback->reviewer)
                                            by {{ $feedback->reviewer->name }}
                                        @endif
                                    </p>
                                @endif
                                <button type="submit" class="dash-btn dash-btn--ghost">Save status</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const thread = document.getElementById('admin-fb-thread');
        if (thread) thread.scrollTop = thread.scrollHeight;
    </script>
@endsection
