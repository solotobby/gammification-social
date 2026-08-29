<a href="{{ route('messages') }}"
    wire:poll.15s
    class="pk-n-btn pk-n-msg"
    aria-label="Messages{{ $unreadCount > 0 ? ' ('.$unreadCount.' unread)' : '' }}">
    <i class="fa fa-comment-dots"></i>
    @if ($unreadCount > 0)
        <span class="pk-n-count">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
    @endif
</a>
