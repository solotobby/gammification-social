@extends('layouts.admin')


@section('content')
    <div class="content content-full content-boxed">
        <!-- Dynamic Table with Export Buttons -->
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                    Monthly Payout Overview (Pro-rata) </i>
                </h3>
            </div>
            <div class="block-content block-content-full">

                <form method="GET" class="mb-4">
                    <label for="month" class="form-label">Select Month</label>
                    <input type="month" name="month" id="month" value="{{ $monthParam }}" class="form-control"
                        style="max-width: 200px" onchange="this.form.submit()">
                </form>



                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Tier</th>
                            <th>Platform Pool ($)</th>
                            <th>Tier Pool ($)</th>
                            <th>Total Engagement</th>
                            <th>Total Payout ($)</th>
                            <th>Members</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($results as $level)
                            <tr>
                                <td>{{ $level['Tier'] }}</td>
                                <td>{{ number_format($level['Platform Pool'], 2) }}</td>
                                <td>{{ number_format($level['Tier Pool'], 2) }}</td>
                                <td>{{ number_format($level['Total Engagement']) }}</td>
                                <td>{{ number_format($level['Total Payout'], 2) }}</td>
                                <td>{{ $level['Members'] }}</td>
                                <td> <a href="{{ url('/payouts/monthly/levels/'.$level['Tier']) }}?month={{ request('month') }}" class="btn btn-info btn-sm"> View Users</a> </td>
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
