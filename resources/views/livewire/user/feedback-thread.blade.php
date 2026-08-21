<div class="pk-app">
    @include('livewire.user.partials.pk-app-ui')
    <style>
        .pk-badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 700;
            background: var(--pk-violet-soft);
            color: var(--pk-violet-dark);
        }
        .pk-form .pk-field label {
            display: block;
            margin-bottom: 6px;
            font-size: .84rem;
            font-weight: 600;
            color: var(--pk-ink);
        }
        .fb-thread {
            display: grid;
            gap: 12px;
            margin-bottom: 16px;
            max-height: min(60vh, 520px);
            overflow-y: auto;
            padding: 4px 2px;
        }
        .fb-bubble {
            max-width: min(92%, 520px);
            padding: 12px 14px;
            border-radius: 14px;
            border: 1px solid var(--pk-line);
            background: #fff;
        }
        .fb-bubble--user {
            margin-left: auto;
            background: var(--pk-violet-soft);
            border-color: #C7D2FE;
        }
        .fb-bubble--staff {
            margin-right: auto;
            background: #F8FAFC;
        }
        .fb-bubble-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: .75rem;
            color: var(--pk-muted);
            font-weight: 600;
        }
        .fb-bubble-body {
            margin: 0;
            white-space: pre-wrap;
            word-break: break-word;
            font-size: .92rem;
            line-height: 1.5;
            color: var(--pk-ink);
        }
    </style>

    <div class="pk-app-hero">
        <div class="pk-app-hero-inner">
            <span class="pk-app-kicker">Support thread</span>
            <h1>{{ $feedback->subject }}</h1>
            <p>{{ $feedback->typeLabel() }} · {{ $feedback->statusLabel() }}</p>
        </div>
    </div>

    <div style="margin-bottom:14px;">
        <a href="{{ route('feedback') }}" class="pk-btn pk-btn--ghost">← All feedback</a>
    </div>

    @if (session('success'))
        <div class="pk-alert pk-alert--success" style="margin-bottom:16px;">{{ session('success') }}</div>
    @endif

    <div class="pk-panel" style="margin-bottom:16px;">
        <div class="pk-panel-body">
            <div class="fb-thread" id="fb-thread">
                @forelse ($messages as $msg)
                    <div class="fb-bubble {{ $msg->is_staff ? 'fb-bubble--staff' : 'fb-bubble--user' }}">
                        <div class="fb-bubble-meta">
                            <span>
                                @if ($msg->is_staff)
                                    Payhankey Support
                                @else
                                    You
                                @endif
                            </span>
                            <span>{{ $msg->created_at?->format('M j, Y · g:i A') }}</span>
                        </div>
                        <p class="fb-bubble-body">{{ $msg->body }}</p>
                    </div>
                @empty
                    <p class="pk-hint">No messages yet.</p>
                @endforelse
            </div>

            @if ($feedback->isOpen())
                <form wire:submit="sendReply" class="pk-form">
                    <div class="pk-field" style="margin-bottom:10px;">
                        <label for="reply">Your reply</label>
                        <textarea id="reply" wire:model="reply" class="pk-input" rows="4"
                            maxlength="5000" placeholder="Continue the conversation…" required></textarea>
                        @error('reply') <span class="pk-error">{{ $message }}</span> @enderror
                    </div>
                    <div style="display:flex;justify-content:flex-end;">
                        <button type="submit" class="pk-btn pk-btn--primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="sendReply">Send reply</span>
                            <span wire:loading wire:target="sendReply">Sending…</span>
                        </button>
                    </div>
                </form>
            @else
                <div class="pk-alert pk-alert--info">This conversation is closed. Open a new feedback item if you need more help.</div>
            @endif
        </div>
    </div>

    @if (auth()->user()->email_verified_at == null)
        @include('layouts.accesscode_verification')
    @else
        @include('layouts.onboarding')
    @endif
</div>

<script>
    document.addEventListener('livewire:navigated', () => {
        const el = document.getElementById('fb-thread');
        if (el) el.scrollTop = el.scrollHeight;
    });
    document.addEventListener('DOMContentLoaded', () => {
        const el = document.getElementById('fb-thread');
        if (el) el.scrollTop = el.scrollHeight;
    });
</script>
