<div class="td-actions">
    <style>
        .td-actions {
            display: flex;
            align-items: center;
            padding: 8px 8px 10px;
            gap: 4px;
        }
        .td-action {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 8px;
            border: none;
            background: transparent;
            border-radius: 999px;
            font-family: inherit;
            font-size: .84rem;
            font-weight: 600;
            color: #536471;
            cursor: pointer;
            text-decoration: none;
            transition: background .15s, color .15s;
        }
        .td-action:hover { background: rgba(15,17,23,.05); }
        .td-action svg { width: 18px; height: 18px; flex-shrink: 0; }
        .td-action.td-liked { color: #f91880; }
        .td-action.td-liked svg { fill: #f91880; stroke: #f91880; }
        .td-action.td-like:hover { color: #f91880; background: rgba(249,24,128,.08); }
        .td-action.td-view { cursor: default; }
        .td-action.td-view:hover { background: rgba(0,186,124,.08); color: #00ba7c; }
        .td-action.td-share:hover { color: var(--td-violet, #5A4FDC); background: rgba(90,79,220,.08); }
        .td-action:disabled { opacity: .6; cursor: not-allowed; }
    </style>

    <button type="button"
        class="td-action td-like {{ $likedByMe ? 'td-liked' : '' }}"
        wire:click="toggleLike"
        wire:loading.attr="disabled"
        wire:target="toggleLike">
        <svg viewBox="0 0 24 24" fill="{{ $likedByMe ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2">
            <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
        </svg>
        {{ number_format($likesCount) }}
    </button>

    <span class="td-action td-view">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
        </svg>
        {{ number_format($commentsCount) }}
    </span>

    <span class="td-action td-view">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
        </svg>
        {{ number_format($viewsCount) }}
    </span>

    <button type="button" class="td-action td-share"
        data-bs-toggle="modal"
        data-bs-target="#modal-block-fromright-{{ $post->id }}"
        aria-label="Share">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
            <path d="M8.59 13.51l6.82 3.98M15.41 6.51l-6.82 3.98"/>
        </svg>
        Share
    </button>
</div>
