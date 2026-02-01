@extends('layouts.admin')

@section('content')

<div class="content content-full content-boxed">
        <!-- Dynamic Table with Export Buttons -->
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                    Payout Overview </i>
                </h3>
            </div>

             <div class="card mb-4">
                <div class="card-body">
                    <h6 class="card-title mb-3">
                        {{ $level }} – Monthly Payout Summary
                    </h6>
                      <div class="row">
                        <div class="col-md-6">
                            <p><strong>Month:</strong> {{ $lastmonth }}</p>
                            <p><strong>Members:</strong> {{ $memberCount }}</p>
                            <p><strong>Total Engagement:</strong> {{ number_format($totalEngagement) }}</p>
                             <p><strong>Level Pool:</strong> {{ number_format(convertToBaseCurrency($levelPool, 'NGN'), 2) }}</p>
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
                                    {{-- $user['userWallet'] --}}
                                    &#8358;{{ number_format(convertToBaseCurrency($user['userPayout'], 'NGN'), 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>




            </div>




        </div>
</div>
@endsection

