@extends('layouts.admin')

@section('content')
    <div class="content content-full content-boxed">
        <!-- Dynamic Table with Export Buttons -->
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                    Pro-Rata Payout Overview </i>
                </h3>
            </div>

            <div class="card mb-4">

    {{-- =========================
        ERROR / EMPTY HANDLING
    ========================== --}}
    @if(empty($payouts) || !is_iterable($payouts))

        <div class="card-body">
            <div class="alert alert-warning mb-0">
                No payout data available for {{ $level }} – {{ $lastmonth }}.
            </div>
        </div>

    @else

        {{-- =========================
            SUMMARY
        ========================== --}}
        <div class="card-body">
            <h6 class="card-title mb-3">
                {{ $level }} – Monthly Payout Summary
            </h6>

            <div class="row">
                <div class="col-md-6">
                    <p><strong>Month:</strong> {{ $lastmonth }}</p>
                    <p><strong>Members:</strong> {{ $memberCount ?? 0 }}</p>
                    <p><strong>Total Engagement:</strong> {{ number_format($totalEngagement ?? 0) }}</p>
                    <p>
                        <strong>Level Pool:</strong>
                        &#8358;{{ number_format(convertToBaseCurrency($levelPool ?? 0, 'NGN'), 2) }}
                    </p>
                </div>
            </div>
        </div>

        {{-- =========================
            TABLE
        ========================== --}}
        <div class="block-content block-content-full">

            @if(count($payouts) === 0)
                <div class="alert alert-info">
                    No eligible users for payout this month.
                </div>
            @else

                <table class="table table-bordered table-striped mt-3">
                    <thead class="table-dark">
                        <tr>
                            <th>Name</th>
                            <th>Engagement</th>
                            <th>Engagement %</th>
                            <th>Currency</th>
                            <th>Payout</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($payouts as $user)
                            <tr>
                                <td>{{ $user['name'] ?? 'N/A' }}</td>
                                <td>{{ number_format($user['engagement'] ?? 0) }}</td>
                                <td>{{ $user['userPercentage'] ?? 0 }}%</td>
                                <td>{{ $user['userWallet'] ?? 'N/A' }}</td>
                                <td>
                                    &#8358;{{ number_format(
                                        convertToBaseCurrency($user['userPayout'] ?? 0, 'NGN'),
                                        2
                                    ) }}
                                </td>
                                <td>
                                    <span class="badge bg-{{ ($user['status'] ?? '') === 'Paid' ? 'secondary' : 'warning' }}">
                                        {{ $user['status'] ?? 'Pending' }}
                                    </span>
                                </td>
                                <td>
                                    @if(($user['status'] ?? '') !== 'Queued')
                                        <a href="{{ url('user/queue/payout/' . $user['id']) }}"
                                           class="btn btn-sm btn-primary">
                                            Queue Payout
                                        </a>
                                    @elseif(($user['status'] ?? '') !== 'Paid')
                                        <span class="text-muted">Processed</span>
                                    @else
                                    <a href="{{ url('view/payout/info/' . $user['id']) }}"
                                           class="btn btn-sm btn-secondary">
                                            View Info
                                        </a>
                                       
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            @endif
        </div>

    @endif
</div>



            {{-- <div class="card mb-4">
                <div class="card-body">
                    <h6 class="card-title mb-3">
                        {{ $level }} – Monthly Payout Summary
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Month:</strong> {{ $lastmonth }}</p>
                            <p><strong>Members:</strong> {{ $memberCount }}</p>
                            <p><strong>Total Engagement:</strong> {{ number_format($totalEngagement) }}</p>
                            <p><strong>Level Pool:</strong> {{ number_format(convertToBaseCurrency($levelPool, 'NGN'), 2) }}
                            </p>
                        </div>

                    </div>
                </div>

                <div class="block-content block-content-full">

                    <table class="table table-bordered table-striped mt-3">
                        <thead class="table-dark">
                            <tr>
                                <th>Name</th>
                                <th>Engagement</th>
                                <th>Engagement %</th>
                                <th>Currency</th>
                                <th>Payout ($)</th>
                                 <th>Status </th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($payouts as $user)
                                <tr>
                                    <td>{{ $user['name'] }}</td>
                                    <td>{{ number_format($user['engagement']) }}</td>
                                    <td>{{ $user['userPercentage'] }}%</td>
                                    <td>{{ $user['userWallet'] }}</td>
                                    <td>
                                       
                                        &#8358;{{ number_format(convertToBaseCurrency($user['userPayout'], 'NGN'), 2) }}
                                    </td>
                                    <td>{{ $user['status'] }}</td>
                                    <td>
                                        <a href="{{ url('user/queue/payout/' . $user['id']) }}"
                                            class="btn btn-sm btn-primary"> Queue Payout </a> 
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>




                </div>




            </div> --}}
        </div>
    @endsection
