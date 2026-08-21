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
        .pk-badge--wait {
            background: #FFFBEB;
            color: #B45309;
        }
        .pk-form .pk-field label {
            display: block;
            margin-bottom: 6px;
            font-size: .84rem;
            font-weight: 600;
            color: var(--pk-ink);
        }
        .fb-row {
            display: block;
            border: 1px solid var(--pk-line);
            border-radius: var(--pk-r-sm);
            padding: 12px 14px;
            background: var(--pk-white);
            text-decoration: none;
            color: inherit;
            transition: border-color .15s ease, background .15s ease;
        }
        .fb-row:hover {
            border-color: #C7D2FE;
            background: #FAFBFF;
        }
    </style>

    <div class="pk-app-hero">
        <div class="pk-app-hero-inner">
            <span class="pk-app-kicker">Support</span>
            <h1>Send feedback</h1>
            <p>Share a complaint, suggestion, improvement idea, or bug — then continue the conversation with the team.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="pk-alert pk-alert--success" style="margin-bottom:16px;">{{ session('success') }}</div>
    @endif

    <div class="pk-panel" style="margin-bottom:20px;">
        <div class="pk-panel-body">
            <form wire:submit="submit" class="pk-form">
                <div class="pk-field">
                    <label for="feedback-type">Type</label>
                    <select id="feedback-type" wire:model="type" class="pk-input" required>
                        @foreach ($types as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type') <span class="pk-error">{{ $message }}</span> @enderror
                </div>

                <div class="pk-field">
                    <label for="feedback-subject">Subject</label>
                    <input id="feedback-subject" type="text" wire:model="subject" class="pk-input"
                        maxlength="120" placeholder="Short summary" required>
                    @error('subject') <span class="pk-error">{{ $message }}</span> @enderror
                </div>

                <div class="pk-field">
                    <label for="feedback-message">Details</label>
                    <textarea id="feedback-message" wire:model="message" class="pk-input" rows="6"
                        maxlength="5000" placeholder="Tell us what happened or what you’d like improved…" required></textarea>
                    @error('message') <span class="pk-error">{{ $message }}</span> @enderror
                </div>

                <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;justify-content:space-between;">
                    <p class="pk-hint" style="margin:0">Starts a conversation the admin team can reply to.</p>
                    <button type="submit" class="pk-btn pk-btn--primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="submit">Start conversation</span>
                        <span wire:loading wire:target="submit">Sending…</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if ($recent->isNotEmpty())
        <div class="pk-panel">
            <div class="pk-panel-body">
                <h2 style="margin:0 0 12px;font-size:1.05rem;">Your conversations</h2>
                <div style="display:grid;gap:10px;">
                    @foreach ($recent as $item)
                        <a href="{{ route('feedback.show', $item) }}" class="fb-row" wire:navigate>
                            <div style="display:flex;flex-wrap:wrap;gap:8px;justify-content:space-between;margin-bottom:6px;">
                                <strong>{{ $item->subject }}</strong>
                                <span class="pk-hint" style="margin:0;">
                                    {{ ($item->last_message_at ?? $item->created_at)?->diffForHumans() }}
                                </span>
                            </div>
                            <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:8px;">
                                <span class="pk-badge">{{ $item->typeLabel() }}</span>
                                <span class="pk-badge">{{ $item->statusLabel() }}</span>
                                <span class="pk-badge">{{ $item->messages_count }} {{ \Illuminate\Support\Str::plural('message', $item->messages_count) }}</span>
                                @if ($item->last_message_by === 'staff' && $item->isOpen())
                                    <span class="pk-badge pk-badge--wait">Support replied</span>
                                @endif
                            </div>
                            <p class="pk-hint" style="margin:0;">{{ \Illuminate\Support\Str::limit($item->message, 140) }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if (auth()->user()->email_verified_at == null)
        @include('layouts.accesscode_verification')
    @else
        @include('layouts.onboarding')
    @endif
</div>
