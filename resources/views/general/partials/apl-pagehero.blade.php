@props(['eyebrow' => '', 'title' => '', 'lead' => '', 'compact' => false, 'crumb' => ''])
<section class="apl-pagehero {{ $compact ? 'apl-pagehero--compact' : '' }}">
  <div class="apl-pagehero__inner reveal">
    @if ($crumb)
      <div class="apl-crumbs"><a href="{{ url('/') }}">Home</a> / <span>{{ $crumb }}</span></div>
    @endif
    @if ($eyebrow)
      <p class="apl-pagehero__eyebrow">{{ $eyebrow }}</p>
    @endif
    @if ($title)
      <h1>{!! $title !!}</h1>
    @endif
    @if ($lead)
      <p class="apl-pagehero__lead">{{ $lead }}</p>
    @endif
  </div>
</section>
