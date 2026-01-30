@extends('layouts.admin')


@section('content')
    <div class="content content-full content-boxed">
        <!-- Dynamic Table with Export Buttons -->
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                    Monthly Payout Overview (Pro-rata) - <i>{{ \Carbon\Carbon::parse($startMonth)->format('jS \\of M') }} to
                        {{ \Carbon\Carbon::parse($endMonth)->format('jS \\of M Y') }} </i>
                </h3>
            </div>
            <div class="block-content block-content-full">

                {{-- <form method="GET" class="mb-4">
                    <label for="month" class="form-label">Select Month</label>
                    <input type="month" name="month" id="month" value="{{ $monthParam }}" class="form-control"
                        style="max-width: 200px" onchange="this.form.submit()">
                </form> --}}



                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Level</th>
                            <th>Total Rev. ($)</th>
                            <th>Platform Rev ($){30%}</th>
                            <th>Level Rev ($){50%}</th>
                            <th>Savings ($){10%}</th>
                            <th>Fremium ($){10%}</th>
                            <th>Total Engagement</th>
                            <th>Members</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($results as $level)
                            <?php
                            $totalRev = $level['totalRev'];
                            $platformRev = $level['platformRev'];
                            $levelPool = $level['levelPool'];
                            $savingsPool = $level['savingsPool'];
                            $totalEngagement = $level['totalEngagement'];
                            $fremiumPool = $level['fremiumPool'];
                            ?>
                            <tr>
                                <td>{{ $level['level'] }}</td>
                                <td>&#8358;{{ number_format(convertToBaseCurrency($totalRev, 'NGN'), 2) }}</td>
                                <td>&#8358;{{ number_format(convertToBaseCurrency($platformRev, 'NGN'), 2) }}</td>
                                <td>&#8358;{{ number_format(convertToBaseCurrency($levelPool, 'NGN'), 2) }}</td>
                                <td> &#8358;{{ number_format(convertToBaseCurrency($savingsPool, 'NGN'), 2) }}</td>

                                <td> &#8358;{{ number_format(convertToBaseCurrency($fremiumPool, 'NGN'), 2) }}</td>
                                <td>{{ number_format($level['totalEngagement']) }}</td>
                                <td>{{ $level['memberCount'] }}</td>
                                <td> 
                                    <a href="{{ url('/payouts/monthly/levels/' . $level['level']) }}"
                                        class="btn btn-info btn-sm"> View Users</a> 
                                </td>
                                {{-- <td> 
                                    <a href="{{ url('/process/payouts/monthly/levels/' . $level['level']) }}"
                                        class="btn btn-info btn-sm"> Process Payout</a> 
                                </td> --}}

                                {{-- <td> <a href="{{ url('/payouts/monthly/levels/' . $level['level']) }}?month={{ $monthParam }}"
                                        class="btn btn-info btn-sm"> View Users</a> </td> --}}
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    No payout data available for this month.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <ol class="list-group list-group-numbered">
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Total Revenue</div>
                            This is the total revenue generated from each level
                        </div>
                        <span class="badge bg-primary rounded-pill">100%</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Level Revenue</div>
                            This is the revenue generated from each level (This will be shared on a pro-rata basis)
                        </div>
                        <span class="badge bg-primary rounded-pill">50%</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Platform Revenue</div>
                            This is the revenue generated from the platform
                        </div>
                        <span class="badge bg-primary rounded-pill">30%</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Platform Savings</div>
                            This is the savings generated from the platform
                        </div>
                        <span class="badge bg-primary rounded-pill">10%</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Fremium</div>
                            This is 10% Revenue from both Creators and Influencers levels. This is shared among Basic users
                            on a pro-rata basis.
                        </div>
                        <span class="badge bg-primary rounded-pill">10%</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Total Engagement</div>
                            The total engagement from all members in that level
                        </div>
                        {{-- <span class="badge bg-primary rounded-pill">10%</span> --}}
                    </li>
                </ol>


            </div>
        </div>
    </div>
@endsection
