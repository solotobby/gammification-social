@php
    $sizeClass = $size ?? 'lg';
    $px = ($sizeClass === 'sm') ? 40 : 72;
@endphp

<x-user-avatar :user="$user" :size="$px" :href="isset($link) && $link ? url('profile/'.$user->username) : false" />
