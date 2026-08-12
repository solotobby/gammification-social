@php
    $cardUrl = $card['url'] ?? '';
    $cardHost = $card['host'] ?? '';
    $cardPath = $card['path'] ?? '';
@endphp

<a href="{{ $cardUrl }}" target="_blank" rel="noopener noreferrer nofollow" class="pk-link-card">
    <div class="pk-link-card-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
        </svg>
    </div>
    <div class="pk-link-card-body">
        <div class="pk-link-card-host">{{ $cardHost }}</div>
        @if ($cardPath !== '')
            <div class="pk-link-card-path">{{ $cardPath }}</div>
        @endif
    </div>
</a>
