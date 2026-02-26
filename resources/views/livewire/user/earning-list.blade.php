<div>
    {{-- Stop trying to control. --}}

    <h2 class="content-heading">Top Earners</h2>
    <div class="row">
        <div class="col-12">
            <div class="block block-rounded row g-0">

                {{-- Vertical Tabs (Months) --}}
                <ul class="nav nav-tabs nav-tabs-block flex-md-column col-md-4 col-xxl-2" role="tablist">
                    @php $first = true; @endphp

                    @foreach ($earnings as $monthKey => $earners)
                        <li class="nav-item d-md-flex flex-md-column">
                            <button class="nav-link text-md-start {{ $first ? 'active' : '' }}"
                                id="tab-{{ $monthKey }}" data-bs-toggle="tab"
                                data-bs-target="#content-{{ $monthKey }}" type="button" role="tab">

                                <i class="fa fa-fw fa-calendar opacity-50 me-1 d-none d-sm-inline-block"></i>

                                {{-- Format 2026-01 to January 2026 --}}
                                <span>
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $monthKey)->format('F Y') }}
                                </span>

                                <span class="d-none d-md-block fs-xs fw-medium opacity-75 mt-md-2">
                                    Top 10 Paid Earners
                                </span>
                            </button>
                        </li>

                        @php $first = false; @endphp
                    @endforeach
                </ul>

                {{-- Tab Content --}}
                <div class="tab-content col-md-8 col-xxl-10">
                    @php $first = true; @endphp

                    @foreach ($earnings as $monthKey => $earners)
                        <div class="block-content tab-pane {{ $first ? 'active' : '' }}"
                            id="content-{{ $monthKey }}" role="tabpanel">

                            <h4 class="fw-semibold mb-4">
                                {{ \Carbon\Carbon::createFromFormat('Y-m', $monthKey)->format('F Y') }} Top Earners
                            </h4>

                            <ul class="list-group list-group-flush">
                                @foreach ($earners as $index => $earner)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">

                                        <div>

                                            #{{ $earner->rank_position ?? $loop->iteration }}


                                            <i>@<span>{{ $earner->username }}</span></i>
                                        </div>


                                        ₦{{ number_format($earner->total_paid, 2) }}

                                    </li>
                                @endforeach
                            </ul>

                        </div>

                        @php $first = false; @endphp
                    @endforeach
                </div>

            </div>
        </div>



        {{-- <div class="col-12">
              <!-- Vertical Block Tabs Default Style With Extra Info -->
              <div class="block block-rounded row g-0">
                <ul class="nav nav-tabs nav-tabs-block flex-md-column col-md-4 col-xxl-2" role="tablist">
                  <li class="nav-item d-md-flex flex-md-column">
                    <button class="nav-link text-md-start active" id="btabs-vertical-info-home-tab" data-bs-toggle="tab" data-bs-target="#btabs-vertical-info-home" role="tab" aria-controls="btabs-vertical-info-home" aria-selected="true">
                      <i class="fa fa-fw fa-home opacity-50 me-1 d-none d-sm-inline-block"></i>
                      <span>Home</span>
                      <span class="d-none d-md-block fs-xs fw-medium opacity-75 mt-md-2">
                        Check out your main activity and any pending notifications
                      </span>
                    </button>
                  </li>
                  <li class="nav-item d-md-flex flex-md-column">
                    <button class="nav-link text-md-start" id="btabs-vertical-info-profile-tab" data-bs-toggle="tab" data-bs-target="#btabs-vertical-info-profile" role="tab" aria-controls="btabs-vertical-info-profile" aria-selected="false">
                      <i class="fa fa-fw fa-user-circle opacity-50 me-1 d-none d-sm-inline-block"></i>
                      <span>Profile</span>
                      <span class="d-none d-md-block fs-xs fw-medium opacity-75 mt-md-2">
                        Update your public information and promote your projects
                      </span>
                    </button>
                  </li>
                  <li class="nav-item d-md-flex flex-md-column">
                    <button class="nav-link text-md-start" id="btabs-vertical-info-settings-tab" data-bs-toggle="tab" data-bs-target="#btabs-vertical-info-settings" role="tab" aria-controls="btabs-vertical-info-settings" aria-selected="false">
                      <i class="fa fa-fw fa-cog opacity-50 me-1 d-none d-sm-inline-block"></i>
                      <span>Settings</span>
                      <span class="d-none d-md-block fs-xs fw-medium opacity-75 mt-md-2">
                        Update your email, password and set up your recovery options
                      </span>
                    </button>
                  </li>
                </ul>
                <div class="tab-content col-md-8 col-xxl-10">
                  <div class="block-content tab-pane active" id="btabs-vertical-info-home" role="tabpanel" aria-labelledby="btabs-vertical-info-home-tab">
                    <h4 class="fw-semibold">Home Content</h4>
                    <p class="fs-sm">
                      Enim elit sollicitudin orci, eget dictum leo mi nec lectus. Nam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus? Etiam egestas fringilla enim, id convallis lectus laoreet at. Fusce purus nisi, gravida sed consectetur ut, interdum quis nisi. Quisque egestas nisl id lectus facilisis scelerisque? Proin rhoncus dui at ligula vestibulum ut facilisis ante sodales! Suspendisse potenti. Aliquam tincidunt sollicitudin sem nec ultrices. Sed at mi velit. Ut egestas tempor est, in cursus enim venenatis eget! Nulla quis ligula ipsum.
                    </p>
                    <p class="fs-sm">
                      Enim elit sollicitudin orci, eget dictum leo mi nec lectus. Nam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus? Etiam egestas fringilla enim, id convallis lectus laoreet at. Fusce purus nisi, gravida sed consectetur ut, interdum quis nisi. Quisque egestas nisl id lectus facilisis scelerisque? Proin rhoncus dui at ligula vestibulum ut facilisis ante sodales! Suspendisse potenti. Aliquam tincidunt sollicitudin sem nec ultrices. Sed at mi velit. Ut egestas tempor est, in cursus enim venenatis eget! Nulla quis ligula ipsum.
                    </p>
                    <p class="fs-sm">
                      Enim elit sollicitudin orci, eget dictum leo mi nec lectus. Nam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus? Etiam egestas fringilla enim, id convallis lectus laoreet at. Fusce purus nisi, gravida sed consectetur ut, interdum quis nisi. Quisque egestas nisl id lectus facilisis scelerisque? Proin rhoncus dui at ligula vestibulum ut facilisis ante sodales! Suspendisse potenti. Aliquam tincidunt sollicitudin sem nec ultrices. Sed at mi velit. Ut egestas tempor est, in cursus enim venenatis eget! Nulla quis ligula ipsum.
                    </p>
                  </div>
                  <div class="block-content tab-pane" id="btabs-vertical-info-profile" role="tabpanel" aria-labelledby="btabs-vertical-info-profile-tab">
                    <h4 class="fw-semibold">Profile Content</h4>
                    <p class="fs-sm">
                      Mauris tincidunt tincidunt turpis in porta. Integer fermentum tincidunt auctor. Vestibulum ullamcorper, odio sed rhoncus imperdiet, enim elit sollicitudin orci, eget dictum leo mi nec lectus. Nam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus? Etiam egestas fringilla enim, id convallis lectus laoreet at. Fusce purus nisi, gravida sed consectetur ut, interdum quis nisi. Quisque egestas nisl id lectus facilisis scelerisque? Proin rhoncus dui at ligula vestibulum ut facilisis ante sodales! Suspendisse potenti. Aliquam tincidunt.
                    </p>
                    <p class="fs-sm">
                      Mauris tincidunt tincidunt turpis in porta. Integer fermentum tincidunt auctor. Vestibulum ullamcorper, odio sed rhoncus imperdiet, enim elit sollicitudin orci, eget dictum leo mi nec lectus. Nam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus? Etiam egestas fringilla enim, id convallis lectus laoreet at. Fusce purus nisi, gravida sed consectetur ut, interdum quis nisi. Quisque egestas nisl id lectus facilisis scelerisque? Proin rhoncus dui at ligula vestibulum ut facilisis ante sodales! Suspendisse potenti. Aliquam tincidunt.
                    </p>
                    <p class="fs-sm">
                      Mauris tincidunt tincidunt turpis in porta. Integer fermentum tincidunt auctor. Vestibulum ullamcorper, odio sed rhoncus imperdiet, enim elit sollicitudin orci, eget dictum leo mi nec lectus. Nam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus? Etiam egestas fringilla enim, id convallis lectus laoreet at. Fusce purus nisi, gravida sed consectetur ut, interdum quis nisi. Quisque egestas nisl id lectus facilisis scelerisque? Proin rhoncus dui at ligula vestibulum ut facilisis ante sodales! Suspendisse potenti. Aliquam tincidunt.
                    </p>
                  </div>
                  <div class="block-content tab-pane" id="btabs-vertical-info-settings" role="tabpanel" aria-labelledby="btabs-vertical-info-settings-tab">
                    <h4 class="fw-semibold">Settings Content</h4>
                    <p class="fs-sm">
                      Integer fermentum tincidunt auctor. Vestibulum ullamcorper, odio sed rhoncus imperdiet, enim elit sollicitudin orci, eget dictum leo mi nec lectus. Nam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus? Etiam egestas fringilla enim, id convallis lectus laoreet at. Fusce purus nisi, gravida sed consectetur ut, interdum quis nisi. Quisque egestas nisl id lectus facilisis scelerisque? Proin rhoncus dui at ligula vestibulum ut facilisis ante sodales! Suspendisse potenti. Aliquam tincidunt sollicitudin sem nec ultrices. Sed at mi velit.
                    </p>
                    <p class="fs-sm">
                      Integer fermentum tincidunt auctor. Vestibulum ullamcorper, odio sed rhoncus imperdiet, enim elit sollicitudin orci, eget dictum leo mi nec lectus. Nam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus? Etiam egestas fringilla enim, id convallis lectus laoreet at. Fusce purus nisi, gravida sed consectetur ut, interdum quis nisi. Quisque egestas nisl id lectus facilisis scelerisque? Proin rhoncus dui at ligula vestibulum ut facilisis ante sodales! Suspendisse potenti. Aliquam tincidunt sollicitudin sem nec ultrices. Sed at mi velit.
                    </p>
                    <p class="fs-sm">
                      Integer fermentum tincidunt auctor. Vestibulum ullamcorper, odio sed rhoncus imperdiet, enim elit sollicitudin orci, eget dictum leo mi nec lectus. Nam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus? Etiam egestas fringilla enim, id convallis lectus laoreet at. Fusce purus nisi, gravida sed consectetur ut, interdum quis nisi. Quisque egestas nisl id lectus facilisis scelerisque? Proin rhoncus dui at ligula vestibulum ut facilisis ante sodales! Suspendisse potenti. Aliquam tincidunt sollicitudin sem nec ultrices. Sed at mi velit.
                    </p>
                  </div>
                </div>
              </div>
              <!-- END Vertical Block Tabs Default Style With Extra Info -->
            </div> --}}
    </div>




</div>
