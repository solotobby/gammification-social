@php
    $logoUrl = $community->image
        ? Illuminate\Support\Facades\Storage::disk('spaces')->url($community->image)
        : null;
    $priceLabel = $community->type === 'paid' && $community->member_charge !== null
        ? getCurrencyCode($community->currency) . number_format((float) $community->member_charge, 2) . $community->price_suffix
        : null;
    $joinLabel = match ($community->type) {
        'paid' => ($community->billing_type === 'one_off' ? 'Pay once' : 'Subscribe'),
        'approval' => 'Request to join',
        default => 'Join',
    };
    $joinUrl = auth()->check()
        ? route('community.public', $community)
        : route('community.auth', $community);
    $publicUrl = route('community.public', $community);
    $owner = $community->user;
@endphp

<article class="apl-comm-card">
  <div class="apl-comm-card__top">
    <a href="{{ $publicUrl }}" class="apl-comm-card__identity">
      <div class="apl-comm-card__icon" style="background:{{ $community->color }}">
        @if ($logoUrl)
          <img src="{{ $logoUrl }}" alt="{{ $community->name }}" loading="lazy" decoding="async">
        @else
          {{ $community->initials }}
        @endif
      </div>
      <div class="apl-comm-card__head">
        <div class="apl-comm-card__name">{{ $community->name }}</div>
        <div class="apl-comm-card__sub">
          {{ $community->category->name ?? 'Uncategorised' }} ·
          {{ number_format($community->members_count) }} members
        </div>
      </div>
    </a>

    @switch($community->type)
      @case('paid')
        <span class="apl-comm-pill apl-comm-pill--paid">{{ $priceLabel }}</span>
      @break
      @case('approval')
        <span class="apl-comm-pill apl-comm-pill--approval">Approval</span>
      @break
      @default
        <span class="apl-comm-pill apl-comm-pill--public">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" aria-hidden="true">
            <circle cx="12" cy="12" r="9" />
            <path d="M3 12h18M12 3a15 15 0 0 1 0 18 15 15 0 0 1 0-18Z" />
          </svg>
          Public
        </span>
    @endswitch
  </div>

  <p class="apl-comm-card__desc">{{ Str::limit(strip_tags($community->description ?? ''), 100) ?: 'A creator community on Payhankey.' }}</p>

  <div class="apl-comm-card__owner">
    Owner: <strong>{{ $owner->name ?? 'Creator' }}</strong>
    @if ($priceLabel)
      <span class="apl-comm-card__owner-sep">·</span>
      <span>{{ $priceLabel }}@if($community->billing_label) <em>({{ $community->billing_label }})</em>@endif</span>
    @endif
  </div>

  <div class="apl-comm-card__foot">
    <span class="apl-comm-card__members">{{ number_format($community->members_count) }} members</span>
    <div class="apl-comm-card__actions">
      <a href="{{ $publicUrl }}" class="apl-comm-btn apl-comm-btn--outline">View page</a>
      <a href="{{ $joinUrl }}" class="apl-comm-btn apl-comm-btn--solid">{{ $joinLabel }}</a>
    </div>
  </div>
</article>
