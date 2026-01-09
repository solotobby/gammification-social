@extends('layouts.admin')


@section('content')
    <div class="content content-full content-boxed">
        <!-- Dynamic Table with Export Buttons -->
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                    Monthly Payout Overview (Pro-rata) -  <i>{{ \Carbon\Carbon::parse($startMonth)->format('jS \\of M') }} to  {{ \Carbon\Carbon::parse($endMonth)->format('jS \\of M Y') }} </i>
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
                            <th>Tier</th>
                            <th>Total Rev. ($)</th>
                            <th>Platform Rev ($)</th>
                            <th>Level Rev ($)</th>
                            <th>Savings ($)</th>
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
                            ?>
                            <tr>
                                <td>{{ $level['level'] }}</td>
                                <td>&#8358;{{ number_format(convertToBaseCurrency($totalRev, 'NGN'), 2) }}</td>
                                <td>&#8358;{{ number_format(convertToBaseCurrency($platformRev, 'NGN'), 2) }}</td>
                                <td>&#8358;{{ number_format(convertToBaseCurrency($levelPool, 'NGN'), 2) }}</td>
                                <td> &#8358;{{ number_format(convertToBaseCurrency($savingsPool, 'NGN'), 2) }}</td>
                                <td>{{ number_format($level['totalEngagement']) }}</td>
                                <td>{{ $level['memberCount'] }}</td>
                                <td> <a href="{{ url('/payouts/monthly/levels/'.$level['level']) }}"
                                        class="btn btn-info btn-sm"> View Users</a> </td>

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


            </div>
        </div>
    </div>
@endsection
