@extends('general.master.body')

@section('content')
 {{-- <section class="pagehero">
  <div class="wrap pagehero__inner">
    <div class="crumbs"><a href="{{ url('/') }}">Home</a> / <span>Top earners</span></div>
    <span class="eyebrow">Top earners</span>
    <h1>The Payhankey leaderboard</h1>
    <p>Real members, ranked by what they've earned. Every name here started with a single free account — and so can you.</p>
  </div>
</section>
<section class="section">
  <div class="wrap">
    <div class="center reveal" style="margin-bottom:34px">
      <div class="lb-filters">
      
        <button class="is-active">This month</button>
        <button>All time</button>
      </div>
    </div>

    <div class="podium reveal">
      <div class="podium__card">
        <div class="podium__rank podium__rank--2">2</div>
        <div class="avatar avatar--lg" style="background:linear-gradient(135deg,#7C6FF2,#F25C8A)">KB</div>
        <div class="podium__name">Kwame B.</div><div class="podium__handle">@kwamecreates</div>
        <div class="podium__earn">$3,910</div>
        <span class="delta delta--up" style="justify-content:center;margin-top:6px"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>14%</span>
      </div>
      <div class="podium__card podium__card--1">
        <div class="podium__rank podium__rank--1">1</div>
        <div class="avatar avatar--lg" style="background:linear-gradient(135deg,#F5B73C,#F25C8A)">AO</div>
        <div class="podium__name">Amara O.</div><div class="podium__handle">@amaravibes</div>
        <div class="podium__earn">$5,240</div>
        <span class="delta delta--up" style="justify-content:center;margin-top:6px"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>21%</span>
      </div>
      <div class="podium__card">
        <div class="podium__rank podium__rank--3">3</div>
        <div class="avatar avatar--lg" style="background:linear-gradient(135deg,#12B886,#5A4FDC)">ZM</div>
        <div class="podium__name">Zinhle M.</div><div class="podium__handle">@zinhletalks</div>
        <div class="podium__earn">$3,180</div>
        <span class="delta delta--up" style="justify-content:center;margin-top:6px"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>8%</span>
      </div>
    </div>

    <div class="reveal" style="overflow-x:auto">
      <table class="lb-table">
        <thead><tr><th>Rank</th><th>Creator</th><th>Level</th><th>Growth</th><th>Earnings</th></tr></thead>
        <tbody><tr>
    <td><span class="lb-rank">4</span></td>
    <td><div class="lb-user"><div class="avatar" style="width:40px;height:40px;font-size:.85rem;background:linear-gradient(135deg,#5A4FDC,#7C6FF2)">TA</div>
      <div><b style="font-family:var(--font-display)">Tunde A.</b><div style="color:var(--ink-faint);font-size:.85rem">@tundedaily</div></div></div></td>
    <td><span class="tag-tier tag-tier--i">Influencer</span></td>
    <td><span class="delta delta--up"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>12%</span></td>
    <td><span class="lb-earn">$2,940</span></td>
  </tr><tr>
    <td><span class="lb-rank">5</span></td>
    <td><div class="lb-user"><div class="avatar" style="width:40px;height:40px;font-size:.85rem;background:linear-gradient(135deg,#F25C8A,#F5B73C)">FD</div>
      <div><b style="font-family:var(--font-display)">Fatima D.</b><div style="color:var(--ink-faint);font-size:.85rem">@fatimacreates</div></div></div></td>
    <td><span class="tag-tier tag-tier--c">Creator</span></td>
    <td><span class="delta delta--up"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>9%</span></td>
    <td><span class="lb-earn">$2,610</span></td>
  </tr><tr>
    <td><span class="lb-rank">6</span></td>
    <td><div class="lb-user"><div class="avatar" style="width:40px;height:40px;font-size:.85rem;background:linear-gradient(135deg,#12B886,#5A4FDC)">CN</div>
      <div><b style="font-family:var(--font-display)">Chidi N.</b><div style="color:var(--ink-faint);font-size:.85rem">@chidispeaks</div></div></div></td>
    <td><span class="tag-tier tag-tier--i">Influencer</span></td>
    <td><span class="delta delta--up"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>6%</span></td>
    <td><span class="lb-earn">$2,480</span></td>
  </tr><tr>
    <td><span class="lb-rank">7</span></td>
    <td><div class="lb-user"><div class="avatar" style="width:40px;height:40px;font-size:.85rem;background:linear-gradient(135deg,#7C6FF2,#F25C8A)">LK</div>
      <div><b style="font-family:var(--font-display)">Lerato K.</b><div style="color:var(--ink-faint);font-size:.85rem">@leratok</div></div></div></td>
    <td><span class="tag-tier tag-tier--c">Creator</span></td>
    <td><span class="delta delta--up"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>4%</span></td>
    <td><span class="lb-earn">$2,205</span></td>
  </tr><tr>
    <td><span class="lb-rank">8</span></td>
    <td><div class="lb-user"><div class="avatar" style="width:40px;height:40px;font-size:.85rem;background:linear-gradient(135deg,#F5B73C,#12B886)">YI</div>
      <div><b style="font-family:var(--font-display)">Yusuf I.</b><div style="color:var(--ink-faint);font-size:.85rem">@yusufi</div></div></div></td>
    <td><span class="tag-tier tag-tier--c">Creator</span></td>
    <td><span class="delta delta--down"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>3%</span></td>
    <td><span class="lb-earn">$1,990</span></td>
  </tr><tr>
    <td><span class="lb-rank">9</span></td>
    <td><div class="lb-user"><div class="avatar" style="width:40px;height:40px;font-size:.85rem;background:linear-gradient(135deg,#5A4FDC,#12B886)">NM</div>
      <div><b style="font-family:var(--font-display)">Naledi M.</b><div style="color:var(--ink-faint);font-size:.85rem">@naledim</div></div></div></td>
    <td><span class="tag-tier tag-tier--i">Influencer</span></td>
    <td><span class="delta delta--up"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>7%</span></td>
    <td><span class="lb-earn">$1,820</span></td>
  </tr><tr>
    <td><span class="lb-rank">10</span></td>
    <td><div class="lb-user"><div class="avatar" style="width:40px;height:40px;font-size:.85rem;background:linear-gradient(135deg,#F25C8A,#5A4FDC)">EO</div>
      <div><b style="font-family:var(--font-display)">Emeka O.</b><div style="color:var(--ink-faint);font-size:.85rem">@emekao</div></div></div></td>
    <td><span class="tag-tier tag-tier--c">Creator</span></td>
    <td><span class="delta delta--up"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>2%</span></td>
    <td><span class="lb-earn">$1,640</span></td>
  </tr></tbody>
      </table>
    </div>
    <p class="center" style="color:var(--ink-faint);font-size:.86rem;margin-top:18px">Leaderboard figures are illustrative and refresh monthly. Earnings depend on content, engagement and account level.</p>

    <div class="cta-band reveal" style="margin-top:48px">
      <h2>Want your name on this board?</h2>
      <p>Create your free account, start posting, and climb the ranks. Your first earnings could land today.</p>
      <div class="hero__cta"><a class="btn btn--white btn--lg" href="{{ url('/register') }}">Start earning free <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></a></div>
    </div>
  </div>
</section> --}}



<section class="pagehero">
    <div class="wrap pagehero__inner">
        <div class="crumbs">
            <a href="{{ url('/') }}">Home</a> / <span>Top earners</span>
        </div>

        <span class="eyebrow">Top earners</span>

        <h1>The Payhankey leaderboard</h1>

        <p>
            Real members, ranked by what they've earned. Every name here started with a single free account — and so can you.
        </p>
    </div>
</section>


<section class="section">

<div class="wrap">


<div class="center reveal" style="margin-bottom:34px">

<div class="lb-filters">

<button class="is-active" onclick="switchBoard('month',this)">
This month
</button>

<button onclick="switchBoard('all',this)">
All time
</button>

</div>

</div>



{{-- ================= LAST MONTH ================= --}}

<div id="monthBoard">


@php
$leaders = $lastMonthEarners;
@endphp


<div class="podium reveal">


@foreach([1,0,2] as $position)

@if(isset($leaders[$position]))

@php
$user = $leaders[$position];

$initials = strtoupper(
    substr($user->name,0,2)
);

@endphp



<div class="podium__card 
    {{ $position == 0 ? 'podium__card--1' : '' }}">


<div class="podium__rank podium__rank--{{ $position+1 }}">
{{ $position+1 }}
</div>



<div class="avatar avatar--lg"
style="background:linear-gradient(135deg,#7C6FF2,#F25C8A)">
{{ $initials }}
</div>



<div class="podium__name">
{{ $user->name }}
</div>


<div class="podium__handle">
@{{ $user->username }}
</div>


<div class="podium__earn">
₦{{ number_format($user->total_paid,2) }}
</div>


<span class="delta delta--up"
style="justify-content:center;margin-top:6px">

<svg viewBox="0 0 24 24"
fill="none"
stroke="currentColor"
stroke-width="2.4"
stroke-linecap="round"
stroke-linejoin="round">

<polyline points="18 15 12 9 6 15"/>

</svg>

</span>


</div>


@endif

@endforeach


</div>




<div class="reveal" style="overflow-x:auto">


<table class="lb-table">


<thead>

<tr>
<th>Rank</th>
<th>Creator</th>
<th>Level</th>
<th>Growth</th>
<th>Earnings</th>
</tr>

</thead>



<tbody>



@foreach($lastMonthEarners->slice(3) as $key=>$user)


@php

$rank = $key + 4;

$initials = strtoupper(
substr($user->name,0,2)
);

@endphp



<tr>


<td>
<span class="lb-rank">
{{ $rank }}
</span>
</td>



<td>

<div class="lb-user">


<div class="avatar"
style="width:40px;height:40px;font-size:.85rem;background:linear-gradient(135deg,#5A4FDC,#7C6FF2)">

{{ $initials }}

</div>



<div>

<b style="font-family:var(--font-display)">
{{ $user->name }}
</b>


<div style="color:var(--ink-faint);font-size:.85rem">

@{{ $user->username }}

</div>


</div>


</div>

</td>



<td>

<span class="tag-tier tag-tier--c">

{{ $user->level ?? 'Creator' }}

</span>

</td>



<td>

<span class="delta delta--up">

<svg viewBox="0 0 24 24"
fill="none"
stroke="currentColor"
stroke-width="2.4"
stroke-linecap="round"
stroke-linejoin="round">

<polyline points="18 15 12 9 6 15"/>

</svg>

</span>

</td>



<td>

<span class="lb-earn">

₦{{ number_format($user->total_paid,2) }}

</span>

</td>


</tr>


@endforeach



</tbody>


</table>


</div>



</div>









{{-- ================= ALL TIME ================= --}}


<div id="allBoard" style="display:none">



@php
$leaders = $allTimeEarners;
@endphp



<div class="podium reveal">



@foreach([1,0,2] as $position)



@if(isset($leaders[$position]))


@php

$user = $leaders[$position];

$initials = strtoupper(
substr($user->name,0,2)
);

@endphp



<div class="podium__card 
{{ $position == 0 ? 'podium__card--1' : '' }}">


<div class="podium__rank podium__rank--{{ $position+1 }}">

{{ $position+1 }}

</div>



<div class="avatar avatar--lg"
style="background:linear-gradient(135deg,#F5B73C,#F25C8A)">

{{ $initials }}

</div>



<div class="podium__name">
{{ $user->name }}
</div>


<div class="podium__handle">
@{{ $user->username }}
</div>



<div class="podium__earn">

₦{{ number_format($user->total_paid,2) }}

</div>


</div>


@endif


@endforeach



</div>




<div class="reveal" style="overflow-x:auto">


<table class="lb-table">


<thead>

<tr>

<th>Rank</th>
<th>Creator</th>
<th>Level</th>
<th>Growth</th>
<th>Earnings</th>

</tr>

</thead>



<tbody>



@foreach($allTimeEarners->slice(3) as $key=>$user)


@php

$rank=$key+4;

$initials=strtoupper(substr($user->name,0,2));

@endphp



<tr>


<td>

<span class="lb-rank">

{{ $rank }}

</span>

</td>



<td>

<div class="lb-user">


<div class="avatar"
style="width:40px;height:40px;font-size:.85rem;background:linear-gradient(135deg,#12B886,#5A4FDC)">

{{ $initials }}

</div>



<div>

<b style="font-family:var(--font-display)">
{{ $user->name }}
</b>


<div style="color:var(--ink-faint);font-size:.85rem">

@{{ $user->username }}

</div>


</div>


</div>


</td>



<td>

<span class="tag-tier tag-tier--c">

{{ $user->level ?? 'Creator' }}

</span>

</td>


<td>

<span class="delta delta--up">

↑

</span>

</td>



<td>

<span class="lb-earn">

₦{{ number_format($user->total_paid,2) }}

</span>

</td>


</tr>



@endforeach



</tbody>


</table>


</div>



</div>







<p class="center"
style="color:var(--ink-faint);font-size:.86rem;margin-top:18px">

Leaderboard figures refresh monthly. Earnings depend on content, engagement and account level.

</p>






<div class="cta-band reveal" style="margin-top:48px">


<h2>
Want your name on this board?
</h2>


<p>
Create your free account, start posting, and climb the ranks.
</p>



<div class="hero__cta">

<a class="btn btn--white btn--lg"
href="{{ url('/register') }}">

Start earning free

<svg viewBox="0 0 24 24"
fill="none"
stroke="currentColor"
stroke-width="2.2"
stroke-linecap="round"
stroke-linejoin="round">

<line x1="5" y1="12" x2="19" y2="12"/>
<polyline points="12 5 19 12 12 19"/>

</svg>

</a>


</div>


</div>



</div>

</section>



<script>

function switchBoard(type, button){

document.querySelectorAll('.lb-filters button')
.forEach(btn=>btn.classList.remove('is-active'));


button.classList.add('is-active');


document.getElementById('monthBoard').style.display =
type === 'month' ? 'block':'none';


document.getElementById('allBoard').style.display =
type === 'all' ? 'block':'none';

}

</script>
@endsection
