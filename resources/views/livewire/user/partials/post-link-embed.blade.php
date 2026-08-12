@php
    $platform = $embed['platform'] ?? '';
    $embedUrl = $embed['embed_url'] ?? '';
    $originalUrl = $embed['original_url'] ?? '';
@endphp

<div @class([
    'pk-link-embed',
    'pk-link-embed--youtube' => $platform === 'youtube',
    'pk-link-embed--instagram' => $platform === 'instagram',
    'pk-link-embed--tiktok' => $platform === 'tiktok',
])>
    <iframe
        src="{{ $embedUrl }}"
        title="{{ ucfirst($platform) }} embed"
        loading="lazy"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
        allowfullscreen
        referrerpolicy="strict-origin-when-cross-origin"
    ></iframe>
    <a href="{{ $originalUrl }}" target="_blank" rel="noopener noreferrer" class="pk-link-embed-fallback">
        Open on {{ ucfirst($platform) }}
    </a>
</div>
