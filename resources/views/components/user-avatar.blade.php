@props([
    'user' => null,
    'userId' => null,
    'src' => null,
    'alt' => null,
    'size' => 44,
    'href' => null,
    'showTick' => true,
])

@php
    $resolvedUserId = $userId ?? ($user->id ?? null);
    $level = $resolvedUserId ? userLevel($resolvedUserId) : 'Basic';
    $isCreator = $level === 'Creator';
    $isInfluencer = $level === 'Influencer';
    $avatarSrc = $src
        ?: ($user->avatar ?? null)
        ?: asset('src/assets/media/avatars/avatar13.jpg');
    $avatarAlt = $alt ?? ($user->name ?? 'User');
    $linkEnabled = $href !== false;
    $profileHref = null;
    if ($linkEnabled) {
        $profileHref = $href;
        if ($profileHref === null && $user && ! empty($user->username)) {
            $profileHref = url('profile/' . $user->username);
        }
    }

    // Named sizes for convenience
    $sizeMap = [
        'xs' => 28,
        'sm' => 32,
        'md' => 44,
        'lg' => 64,
        'xl' => 96,
        'hero' => 168,
    ];
    $px = is_numeric($size) ? (int) $size : ($sizeMap[$size] ?? 44);
@endphp

@once
    <style>
        .ua {
            --ua-size: 44px;
            position: relative;
            display: inline-flex;
            width: var(--ua-size);
            height: var(--ua-size);
            flex: none;
            vertical-align: middle;
        }

        .ua__media {
            display: block;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            overflow: hidden;
            background: #e5e7eb;
            line-height: 0;
        }

        .ua__img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            border-radius: 50%;
        }

        .ua--influencer .ua__media {
            padding: 2px;
            background: linear-gradient(135deg, #1d9bf0 0%, #5A4FDC 55%, #7C6FF2 100%);
            box-sizing: border-box;
        }

        .ua--influencer .ua__img {
            border: 2px solid #fff;
            box-sizing: border-box;
        }

        .ua__tick {
            position: absolute;
            right: -1px;
            bottom: -1px;
            width: max(14px, calc(var(--ua-size) * .34));
            height: max(14px, calc(var(--ua-size) * .34));
            border-radius: 50%;
            background: #fff;
            display: grid;
            place-items: center;
            z-index: 2;
            pointer-events: none;
            box-shadow: 0 0 0 1px rgba(15, 17, 23, .04);
        }

        .ua__tick svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        /* Hero profile size: keep tick proportionally visible */
        .ua--hero .ua__tick {
            right: 4px;
            bottom: 4px;
            width: max(22px, calc(var(--ua-size) * .22));
            height: max(22px, calc(var(--ua-size) * .22));
        }

        .ua--influencer.ua--hero .ua__media {
            padding: 4px;
        }

        .ua--influencer.ua--hero .ua__img {
            border-width: 3px;
        }
    </style>
@endonce

<span {{ $attributes->class([
        'ua',
        'ua--creator' => $isCreator,
        'ua--influencer' => $isInfluencer,
        'ua--hero' => $px >= 120,
    ])->merge(['style' => '--ua-size:' . $px . 'px']) }}>
    @if ($profileHref)
        <a href="{{ $profileHref }}" class="ua__media" aria-label="{{ $avatarAlt }}">
            <img class="ua__img" src="{{ $avatarSrc }}" alt="{{ $avatarAlt }}" loading="lazy">
        </a>
    @else
        <span class="ua__media">
            <img class="ua__img" src="{{ $avatarSrc }}" alt="{{ $avatarAlt }}" loading="lazy">
        </span>
    @endif

    @if ($showTick && ($isCreator || $isInfluencer))
        <span class="ua__tick" aria-label="{{ $isInfluencer ? 'Verified Influencer' : 'Verified Creator' }}">
            <svg viewBox="0 0 22 22" fill="none" aria-hidden="true">
                <circle cx="11" cy="11" r="11" fill="{{ $isInfluencer ? '#5A4FDC' : '#1d9bf0' }}" />
                <path d="M7 11l3 3 5-5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </span>
    @endif
</span>
