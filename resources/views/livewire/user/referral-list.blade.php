<div>
    {{-- Stop trying to control. --}}
    <div class="content content-full content-boxed">

        <h2 class="content-heading">Referral List</h2>
        <div class="row">
            @if (session()->has('status_refresh'))
                <div class="alert alert-success" role="alert">
                    {{ session('status_refresh') }}
                </div>
            @endif

            <div class="col-md-6 col-xl-6">

                <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                    <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                        <div>
                            <i class="fa fa-2x fa-list text-primary"></i>
                        </div>
                        <div class="ms-3 text-end">
                            <p class="fs-3 fw-medium mb-0">
                                {{ $totalReferrals }}
                            </p>
                            <p class="text-muted mb-0">
                                Total Referrals
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
                            <i class="fa fa-2x fa-users text-primary"></i>
                        </div>
                        <div class="ms-3 text-end">
                            <p class="fs-3 fw-medium mb-0">
                                {{ $monthlyReferralsCount }}
                            </p>
                            <p class="text-muted mb-0">
                                Referral This Month
                                {{-- <span><small>(Earnings from inviting friends on Payhankey)</small></span> --}}
                            </p>
                        </div>
                    </div>
                </a>
            </div>

        </div>
        {{-- <div class="alert alert-info">Refer 500 People and and get One month free creator subscription</div> --}}
        <!-- Dynamic Table with Export Buttons -->
        <div class="block block-rounded">

            <div class="block-content block-content-full">


                <table class="table table-hover table-striped table-borderless table-vcenter mb-0 table-responsive">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Following</th>
                            <th>Followers</th>
                            <th>Date Registered</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($referralList as $user)
                        <tr>
                            <td class="text-center" style="width: 140px;">
                                <img class="img-avatar img-avatar-thumb" 
                               src="{{ $user->avatar ?? asset('src/assets/media/avatars/avatar10.jpg') }}"
                                    alt="">
                            </td>
                            <td style="min-width: 200px;">
                                <div class="py-4">
                                    <p class="mb-0">
                                        <a class="link-fx fw-bold d-inline-block" href="{{ url('profile/'.$user->username) }}">
                                            {{$user->name}}</a>
                                    </p>
                                    <p class="mb-0">
                                        <a class="fs-sm fw-bold text-lowercase text-muted me-3"
                                            href="javascript:void(0)">@<span>{{ $user->username }}</span></a>
                                    </p>
                                </div>
                            </td>
                            <td style="min-width: 200px;">
                                Following: {{ $user->following }}
                            </td>
                            <td class="text-end" style="min-width: 160px;">
                                Followers: {{ $user->followers}}
                            </td>
                             <td> {{ \Carbon\Carbon::parse($user->created_at)->format('F d, Y') }}</td>
                        </tr>


                            {{-- <tr>
                                <td><a href="{{ url('profile/' . $list->username) }}"> {{ $list->name }}</a></td>
                                <td>@<span>{{ $list->username }}</span></td>

                                <td> {{ \Carbon\Carbon::parse($list->created_at)->format('F d, Y') }}</td>
                            </tr> --}}
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
