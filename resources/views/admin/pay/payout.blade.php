@extends('layouts.admin')

@section('content')
    <div class="content content-full content-boxed">
        <!-- Dynamic Table with Export Buttons -->
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                    Payout List for {{ $currentMonth }} - ({{ $stats->count() }})</i>
                </h3>
            </div>

            <div class="block-content block-content-full">

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Level</th>
                            <th>Amount</th>
                            <th>Currency</th>
                            <th>StartDate</th>
                            <th>EndDate</th>
                            <th>When</th>

                            {{-- <th>Action</th> --}}
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($stats as $stat)
                            <tr>
                                <td>{{ $stat->user->name }}</td>
                                <td>{{ $stat->plan_name }}</td>
                                <td>{{ $stat->amount }}</td>
                                <td>{{ $stat->currency }}</td>
                                <td>{{ $stat->start_date }}</td>
                                <td>{{ $stat->end_date }}</td>
                                <td>{{ $stat->created_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
