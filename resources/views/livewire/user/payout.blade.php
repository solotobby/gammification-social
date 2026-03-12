<div>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}
    <div class="content content-full content-boxed">

        <h2 class="content-heading">Your Payhankey Payouts</h2>

        <div class="row">
            <div class="alert alert-info">
                Note: Minimum Payment for NGN is 100. Payment less than 100, will be carried over to the next month until it is more than minimum payout.    
            </div> 

             <div class="col-md-6 col-xl-6">

                <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                    <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                        <div>
                            <i class="fa fa-2x fa-usd text-primary"></i>
                        </div>
                        <div class="ms-3 text-end">
                            <p class="fs-3 fw-medium mb-0">
                                {{number_format($totalPaid,2)}}
                            </p>
                            <p class="text-muted mb-0">
                                Paid
                                {{-- <span><small>(Earnings from signup bonus and content
                                    monetization)</small></span> --}}
                            </p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-xl-6">
                <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                    <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                        <div>
                            <i class="fa fa-2x fa-usd text-primary"></i>
                        </div>
                        <div class="ms-3 text-end">
                            <p class="fs-3 fw-medium mb-0">
                               {{number_format($totalQueued,2)}}
                            </p>
                            <p class="text-muted mb-0">
                               Queued
                                {{-- <span><small>(Earnings from inviting friends on Payhankey)</small></span> --}}
                            </p>
                        </div>
                    </div>
                </a>
            </div>


        </div>


        @if (count($payouts) > 0)

            <div class="block block-rounded">

                <div class="block-content block-content-full">

                    <table class="table table-hover table-striped table-borderless table-vcenter mb-0 table-responsive">
                        <thead>
                            <tr>
                                <th>Period</th>
                                <th>Level</th>
                                <th>Amount</th>
                                <th>Currency</th>
                                 <th>Status</th>
                                <th>Date Paid</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($payouts as $py)
                                <tr>

                                    <td> {{  \Carbon\Carbon::parse($py->month)->format('M, Y');  }} </td>
                                    <td> {{ $py->level }} </td>
                                    <td> {{ $py->amount }} </td>
                                    <td> {{ $py->currency }} </td>
                                     <td> {{ $py->status }} </td>
                                    <td> {{ $py->created_at }} </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>

                </div>
            </div>
        @else
            <div class="alert alert-info ">
                You do not have payouts yet
            </div>

        @endif

    </div>
</div>
