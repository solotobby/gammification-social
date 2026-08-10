@php
    $initials = strtoupper(substr($user->name ?? $user->username ?? 'PK', 0, 2));
    $sizeClass = $size ?? 'lg';
    $inlineStyle = ($sizeClass === 'sm')
        ? 'width:40px;height:40px;font-size:.85rem;background:linear-gradient(135deg,#5A4FDC,#7C6FF2);overflow:hidden'
        : 'background:linear-gradient(135deg,#7C6FF2,#F25C8A);overflow:hidden';
@endphp

<div @class(['avatar', 'avatar--lg' => $sizeClass === 'lg']) style="{{ $inlineStyle }}">
    @if (! empty($user->avatar))
        <img src="{{ $user->avatar }}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:inherit">
    @else
        {{ $initials }}
    @endif
</div>
