<div wire:poll.15s class="dropdown d-inline-flex">
    <button type="button"
        class="pk-n-btn pk-n-bell"
        data-bs-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
        aria-label="Notifications">
        <i class="fa fa-bell"></i>
        @if ($unreadCount > 0)
            <span class="pk-n-dot" aria-hidden="true"></span>
        @endif
    </button>

    <div class="dropdown-menu dropdown-menu-end pk-n-notify">
        <div class="pk-n-notify-h">
            <span>Notifications</span>
            @if ($unreadCount)
                <button type="button" wire:click="markAllAsRead">Mark all read</button>
            @endif
        </div>

        <ul class="pk-n-notify-list">
            @forelse ($notifications as $notification)
                <li>
                    <a href="{{ $notification->data['url'] ?? '#' }}"
                        wire:click="markAsRead('{{ $notification->id }}')">
                        <div class="t">{{ $notification->data['title'] ?? 'Notification' }}</div>
                        <div class="m">{{ $notification->created_at->diffForHumans() }}</div>
                    </a>
                </li>
            @empty
                <li class="pk-n-notify-empty">No notifications yet</li>
            @endforelse
        </ul>
    </div>
</div>
