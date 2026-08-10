@extends('general.master.apple')

@section('title', 'Top earners · Payhankey')

@section('apple_content')
@include('general.partials.apl-pagehero', [
    'crumb' => 'Top earners',
    'eyebrow' => 'Top earners',
    'title' => 'The Payhankey leaderboard',
    'lead' => 'Real members, ranked by what they\'ve earned. Every name here started with a single free account.',
])

<section class="apl-section apl-section--soft">
        <div class="apl-wrap">


            <div class="center reveal" style="margin-bottom:34px">
                <div class="apl-lb-filters lb-filters">

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


                    @foreach ([1, 0, 2] as $position)
                        @if (isset($leaders[$position]))
                            @php
                                $user = $leaders[$position];

                                $initials = strtoupper(substr($user->name, 0, 2));

                            @endphp



                            <div class="podium__card 
    {{ $position == 0 ? 'podium__card--1' : '' }}">


                                <div class="podium__rank podium__rank--{{ $position + 1 }}">
                                    {{ $position + 1 }}
                                </div>



                                @include('general.partials.leader-avatar', ['user' => $user])



                                <div class="podium__name">
                                  @<span>{{ $user->username }}</span>
                                    {{-- {{ $user->name }} --}}
                                </div>


                                {{-- <div class="podium__handle">
                                    @{{ $user->username }}
                                </div> --}}


                                <div class="podium__earn">
                                    ₦{{ number_format($user->total_paid, 2) }}
                                </div>


                                <span class="delta delta--up" style="justify-content:center;margin-top:6px">

                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"
                                        stroke-linecap="round" stroke-linejoin="round">

                                        <polyline points="18 15 12 9 6 15" />

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
                                {{-- <th>Level</th> --}}
                                {{-- <th>Growth</th> --}}
                                <th>Earnings</th>
                            </tr>

                        </thead>



                        <tbody>



                            @foreach ($lastMonthEarners->slice(3) as $key => $user)
                                @php

                                    $rank = $key + 1;

                                    $initials = strtoupper(substr($user->name, 0, 2));

                                @endphp



                                <tr>


                                    <td>
                                        <span class="lb-rank">
                                            {{ $rank }}
                                        </span>
                                    </td>



                                    <td>

                                        <div class="lb-user">


                                            @include('general.partials.leader-avatar', ['user' => $user, 'size' => 'sm'])



                                            <div>

                                                <b style="font-family:var(--font-display)">
                                                    @<span>{{ $user->username }}</span>
                                                </b>


                                                {{-- <div style="color:var(--ink-faint);font-size:.85rem">

                                                    @<span>{{ $user->username }}</span>

                                                </div> --}}


                                            </div>


                                        </div>

                                    </td>



                                    {{-- <td>

                                        <span class="tag-tier tag-tier--c">

                                            {{ $user->level ?? 'Creator' }}

                                        </span>

                                    </td> --}}



                                    {{-- <td>

                                        <span class="delta delta--up">

                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"
                                                stroke-linecap="round" stroke-linejoin="round">

                                                <polyline points="18 15 12 9 6 15" />

                                            </svg>

                                        </span>

                                    </td> --}}



                                    <td>

                                        <span class="lb-earn">

                                            ₦{{ number_format($user->total_paid, 2) }}

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



                    @foreach ([1, 0, 2] as $position)
                        @if (isset($leaders[$position]))
                            @php

                                $user = $leaders[$position];

                                $initials = strtoupper(substr($user->name, 0, 2));

                            @endphp



                            <div class="podium__card 
{{ $position == 0 ? 'podium__card--1' : '' }}">


                                <div class="podium__rank podium__rank--{{ $position + 1 }}">

                                    {{ $position + 1 }}

                                </div>



                                @include('general.partials.leader-avatar', ['user' => $user])



                                <div class="podium__name">
                                    @<span>{{ $user->username }}</span>
                                </div>


                                {{-- <div class="podium__handle">
                                    @{{ $user->username }}
                                </div> --}}



                                <div class="podium__earn">

                                    ₦{{ number_format($user->total_paid, 2) }}

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
                                {{-- <th>Level</th> --}}
                                {{-- <th>Growth</th> --}}
                                <th>Earnings</th>

                            </tr>

                        </thead>



                        <tbody>



                            @foreach ($allTimeEarners->slice(3) as $key => $user)
                                @php

                                    $rank = $key + 1;

                                    $initials = strtoupper(substr($user->name, 0, 2));

                                @endphp



                                <tr>


                                    <td>

                                        <span class="lb-rank">

                                            {{ $rank }}

                                        </span>

                                    </td>



                                    <td>

                                        <div class="lb-user">


                                            @include('general.partials.leader-avatar', ['user' => $user, 'size' => 'sm'])



                                            <div>

                                                <b style="font-family:var(--font-display)">
                                                    @<span>{{ $user->username }}</span>
                                                </b>


                                                {{-- <div style="color:var(--ink-faint);font-size:.85rem">

                                                    @{{ $user->username }}

                                                </div> --}}


                                            </div>


                                        </div>


                                    </td>



                                    {{-- <td>

                                        <span class="tag-tier tag-tier--c">

                                            {{ $user->level ?? 'Creator' }}

                                        </span>

                                    </td> --}}


                                    {{-- <td>

                                        <span class="delta delta--up">

                                            ↑

                                        </span>

                                    </td> --}}



                                    <td>

                                        <span class="lb-earn">

                                            ₦{{ number_format($user->total_paid, 2) }}

                                        </span>

                                    </td>


                                </tr>
                            @endforeach



                        </tbody>


                    </table>


                </div>



            </div>







            <p class="center" style="color:var(--ink-faint);font-size:.86rem;margin-top:18px">

                Leaderboard figures refresh monthly. Earnings depend on content, engagement and account level.

            </p>






            <div class="cta-band reveal" style="margin-top:48px;display:none">


                <h2>
                    Want your name on this board?
                </h2>


                <p>
                    Create your free account, start posting, and climb the ranks.
                </p>



                <div class="hero__cta">

                    <a class="btn btn--white btn--lg" href="{{ url('/register') }}">

                        Start earning free

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                            stroke-linecap="round" stroke-linejoin="round">

                            <line x1="5" y1="12" x2="19" y2="12" />
                            <polyline points="12 5 19 12 12 19" />

                        </svg>

                    </a>


                </div>


            </div>



        </div>

    </section>

<section class="apl-close">
    <h2 class="reveal">Want your name on this board?</h2>
    <p class="reveal">Create your free account, start posting, and climb the ranks.</p>
    <div class="apl-close__cta reveal">
        <a class="apl-btn apl-btn--fill" href="{{ url('/register') }}">Start earning free</a>
    </div>
</section>

@push('scripts')
    <script>
        function switchBoard(type, button) {
            document.querySelectorAll('.lb-filters button')
                .forEach(btn => btn.classList.remove('is-active'));
            button.classList.add('is-active');
            document.getElementById('monthBoard').style.display =
                type === 'month' ? 'block' : 'none';
            document.getElementById('allBoard').style.display =
                type === 'all' ? 'block' : 'none';
        }
    </script>
@endpush
@endsection
