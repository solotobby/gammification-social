@extends('layouts.admin')

@section('content')
    <div class="content content-full content-boxed">
        <!-- Dynamic Table with Export Buttons -->
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                    Payout List for {{ $currentMonth }} - ({{ $count }})</i>
                </h3>


            </div>

            <div class="block-content block-content-full">
                <div class="mb-4">
                    <ol class="list-group list-group-numbered">
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold"> Total Influencers</div>

                            </div>
                            <span class="badge bg-primary rounded-pill">{{ $totalInfluncers }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">Influencer Revenue</div>

                            </div>
                            <span class="l">NGN {{ number_format($totalInfluncers * 7500,2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">Creator</div>

                            </div>
                            <span class="badge bg-primary rounded-pill">{{ $totalCreator }} </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">Creator Revenue</div>

                            </div>
                            <span class=""> NGN{{ number_format($totalCreator * 1500,2) }}</span>
                        </li>

                         <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">Total Revenue</div>
                            </div>

                            {{-- @php 
                                $totalRev = 0;
                                $totaCreatorRev = $totalCreator * 1500;
                                $totaInfluencerRev = $totalInfluencer * 7500;
                                $totalRev = $totaCreatorRev + $totaInfluencerRev;
                            
                            @endphp --}}
                            <span class="fw-bold"> NGN{{ number_format($totalRev,2) }}</span>
                        </li>

                    </ol>
                </div>



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
                <div class="d-flex justify-content-center mt-4">
                    {!! $stats->links('pagination::bootstrap-4') !!}
                </div>
            </div>
        </div>
    </div>
@endsection
