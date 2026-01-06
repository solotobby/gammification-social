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
            <div class="card mb-4">
    <div class="card-body">
        <h4 class="card-title mb-3">
            {{ $tier }} – Monthly Payout Summary
        </h4>

        <div class="row">
            <div class="col-md-6">
                <p><strong>Month:</strong> {{ $month }}</p>
                <p><strong>Members:</strong> {{ $memberCount }}</p>
                <p><strong>Total Engagement:</strong> {{ number_format($totalEngagement) }}</p>
            </div>
            <?php 
                $revTotal = $totalRevenue; 
                $pltpool = $platformPool; 
                $tpool = $tierPool;
            
            ?>
            <div class="col-md-6">
                <p><strong>Total Revenue:</strong> &#8358;{{ number_format(convertToBaseCurrency($revTotal, 'NGN'), 2) }}</p>
                <p><strong>Platform Cut (30%):</strong> &#8358;{{ number_format(convertToBaseCurrency($pltpool, 'NGN'), 2) }}</p>
                <p><strong>Tier Pool (70%):</strong> &#8358;{{ number_format(convertToBaseCurrency($tpool, 'NGN'), 2) }}</p> 
                
            </div>

            
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
                            <th>Payout ($)</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                              
                                <td>{{ $user['name'] }}</td>
                                <td>{{ number_format($user['engagement']) }}</td>
                                <td>{{ $user['percentage'] }}%</td>
                                <td>{{ number_format($user['payout'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>




            </div>
        </div>
    </div>
@endsection
