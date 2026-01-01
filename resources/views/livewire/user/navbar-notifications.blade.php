<div wire:poll.15s>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}

    {{-- <div class="dropdown d-inline-block" wire:poll.10s> --}}

    <button class="btn btn-alt-secondary position-relative"
            data-bs-toggle="dropdown">
        <i class="fa fa-fw fa-bell"></i>

        @if($unreadCount > 0)
            <span class="badge bg-danger position-absolute top-0 end-0">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0">

        <div class="bg-primary-dark rounded-top fw-semibold text-white text-center p-3">
            Notifications

            @if($unreadCount)
                <button wire:click="markAllAsRead"
                        class="btn btn-sm btn-light float-end">
                    Mark all
                </button>
            @endif
        </div>

        <ul class="nav-items my-2">

            @forelse($notifications as $notification)
                <li>
                    <a class="d-flex text-dark py-2"
                       href="{{ $notification->data['url'] }}"
                       wire:click="markAsRead('{{ $notification->id }}')">

                        <div class="flex-shrink-0 mx-3">
                            <i class="fa fa-fw {{ $notification->data['icon'] }}"></i>
                        </div>

                        <div class="flex-grow-1 fs-sm pe-2">
                            <div class="fw-semibold">
                                {{ $notification->data['title'] }}
                            </div>
                            <div class="text-muted">
                                {{ $notification->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </a>
                </li>
            @empty
                <li class="text-center text-muted py-3">
                    No notifications
                </li>
            @endforelse

        </ul>

        {{-- <div class="p-2 border-top">
            <a class="btn btn-alt-primary w-100 text-center"
               href="{{ route('notifications.index') }}">
                <i class="fa fa-fw fa-eye opacity-50 me-1"></i> View All
            </a>
        </div> --}}

    </div>
{{-- </div> --}}



</div>
