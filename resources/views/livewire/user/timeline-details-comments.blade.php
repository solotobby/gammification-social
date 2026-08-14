<div class="td-comments">
    <style>
        .td-comments-head {
            font-size: .88rem;
            font-weight: 700;
            color: #0F1117;
            margin-bottom: 14px;
        }
        .td-comment-form {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 16px;
        }
        .td-comment-form img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }
        .td-comment-input-wrap { flex: 1; }
        .td-comment-input {
            width: 100%;
            border: none;
            background: #fff;
            box-shadow: 0 1px 2px rgba(15,17,23,.06);
            border-radius: 20px;
            padding: 10px 16px;
            font-family: inherit;
            font-size: .88rem;
            outline: none;
        }
        .td-comment-input:focus {
            box-shadow: 0 0 0 2px rgba(90,79,220,.15), 0 1px 4px rgba(15,17,23,.08);
        }
        .td-comment-send {
            flex-shrink: 0;
            border: none;
            background: #5A4FDC;
            color: #fff;
            font-family: inherit;
            font-weight: 600;
            font-size: .82rem;
            padding: 10px 16px;
            border-radius: 999px;
            cursor: pointer;
        }
        .td-comment-send:disabled { opacity: .6; }
        .td-comment-row {
            display: flex;
            gap: 10px;
            margin-bottom: 12px;
        }
        .td-comment-av {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }
        .td-comment-bubble {
            background: #fff;
            border-radius: 18px;
            padding: 10px 14px;
            box-shadow: 0 1px 2px rgba(15,17,23,.06);
            flex: 1;
            min-width: 0;
        }
        .td-comment-name {
            font-weight: 700;
            font-size: .84rem;
            color: #0F1117;
            text-decoration: none;
        }
        .td-comment-name:hover { text-decoration: underline; }
        .td-comment-time {
            font-size: .72rem;
            color: #8B90A5;
            margin-left: 6px;
        }
        .td-comment-text {
            font-size: .88rem;
            color: #3D4254;
            margin: 4px 0 0;
            line-height: 1.45;
            word-break: break-word;
        }
        .td-load-more {
            display: inline-block;
            font-size: .84rem;
            font-weight: 600;
            color: #5A4FDC;
            text-decoration: none;
            margin-top: 4px;
        }
        .td-load-more:hover { text-decoration: underline; }
        .td-no-comments {
            font-size: .86rem;
            color: #8B90A5;
            text-align: center;
            padding: 12px 0;
        }
    </style>

    <div class="td-comments-head">
        Comments @if($commentsCount > 0)<span style="color:#8B90A5;font-weight:600">({{ number_format($commentsCount) }})</span>@endif
    </div>

    <form wire:submit.prevent="commentFeed" class="td-comment-form">
        <x-user-avatar :user="auth()->user()" size="sm" :href="false" />
        <div class="td-comment-input-wrap">
            <input type="text" wire:model="message" maxlength="500"
                class="td-comment-input" placeholder="Write a comment…">
        </div>
        <button type="submit" class="td-comment-send"
            wire:loading.attr="disabled" wire:target="commentFeed">
            <span wire:loading.remove wire:target="commentFeed">Post</span>
            <span wire:loading wire:target="commentFeed">…</span>
        </button>
    </form>

    @error('message')
        <p style="font-size:.78rem;color:#EF4444;margin:-8px 0 12px 46px">{{ $message }}</p>
    @enderror

    @if ($comments->isNotEmpty())
        @foreach ($comments as $comment)
            <div class="td-comment-row" wire:key="comment-{{ $comment['id'] ?? $loop->index }}">
                <x-user-avatar
                    :user-id="$comment['user_id'] ?? null"
                    :src="$comment['avatar'] ?? null"
                    :alt="$comment['name'] ?? 'User'"
                    :href="isset($comment['username']) ? url('profile/' . $comment['username']) : false"
                    size="sm"
                />
                <div class="td-comment-bubble">
                    <a class="td-comment-name" href="{{ url('profile/' . $comment['username']) }}">
                        {{ displayName($comment['name']) }}
                    </a>
                    <span class="td-comment-time">
                        {{ \Carbon\Carbon::parse($comment['created_at'])->diffForHumans() }}
                    </span>
                    <p class="td-comment-text mb-0">{{ $comment['message'] }}</p>
                </div>
            </div>
        @endforeach
    @else
        <p class="td-no-comments">No comments yet. Start the conversation.</p>
    @endif

    @if ($hasMoreComments)
        <a href="javascript:void(0)" wire:click="loadMore" class="td-load-more"
            wire:loading.attr="disabled" wire:target="loadMore">
            <span wire:loading.remove wire:target="loadMore">Load more comments</span>
            <span wire:loading wire:target="loadMore">Loading…</span>
        </a>
    @endif
</div>
