@props([
    'eyebrow' => 'Start today',
    'title' => 'Post Once and Get Paid',
    'lead' => null,
    'primaryLabel' => 'Start Building Free',
    'primaryUrl' => null,
    'secondaryLabel' => 'Log in',
    'secondaryUrl' => null,
    'showSecondary' => true,
])

@php
    $lead = $lead ?? ('Join ' . config('payhankey.stats.creators', '32K+') . ' creators across Africa building audiences, communities and sustainable digital businesses.');
    $primaryUrl = $primaryUrl ?? url('/register');
    $secondaryUrl = $secondaryUrl ?? url('/login');
@endphp

<section class="apl-close">
  <div class="apl-close__card reveal">
    <div class="apl-close__glow" aria-hidden="true"></div>
    <p class="apl-close__eyebrow">{{ $eyebrow }}</p>
    <h2>{{ $title }}</h2>
    <p>{{ $lead }}</p>
    <div class="apl-close__cta">
      <a class="apl-btn apl-btn--fill apl-btn--light" href="{{ $primaryUrl }}">{{ $primaryLabel }}</a>
      @if ($showSecondary)
        <a class="apl-btn apl-btn--ghost" href="{{ $secondaryUrl }}">{{ $secondaryLabel }}</a>
      @endif
    </div>
  </div>
</section>
