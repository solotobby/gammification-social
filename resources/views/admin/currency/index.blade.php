@extends('layouts.admin')

@section('styles')
    <link rel="stylesheet" href="{{ asset('src/assets/js/plugins/datatables-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('src/assets/js/plugins/datatables-buttons-bs5/css/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('src/assets/js/plugins/datatables-responsive-bs5/css/responsive.bootstrap5.min.css') }}">
@endsection

@section('content')
    <div class="content">
        {{-- <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                    Currency Management
                </h3>
            </div>

            <div class="block-content block-content-full">
                <div class="block-content">
                    <form method="POST" action="{{ route('immaculate') }}">
                        @csrf

                        <!-- Text -->
                        {{-- <h2 class="content-heading pt-0">Send Access Code</h2> --
                        <div class="row">
                            <div class="col-lg-4">
                                <p class="text-muted">
                                    {{-- Prepend or Append Text next to your inputs, useful if you you would like to add extra info --
                                </p>
                            </div>
                            <div class="col-lg-8 col-xl-5">
                                @if (session('success'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger" role="alert">
                                        {{ session('error') }}
                                    </div>
                                @endif
                                <div class="mb-4">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                           Country
                                        </span>
                                        <select name="country" class="form-control" required>
                                            <option value="">Select Country</option>
                                            {{-- @foreach ($countries as $country)
                                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                            @endforeach --}}
        </select>
        {{-- <input type="text" class="form-control" name="country" id="example-group1-input1"
                                            name="example-group1-input1"> --
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            Level
                                        </span>
                                        <select name="level" class="form-control" required>

                                        </select>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            Validate
                                        </span>
                                        <input type="text" class="form-control" name="validationCode" id="validationCode"
                                            placeholder="Enter validation code" required>
                                    </div>
                                </div>


                                <div class="mb-4">
                                    <button type="submit" class="btn btn-sm btn-primary">Send Code</button>
                                </div>

                            </div>
                        </div>
                        <!-- END Text -->


                    </form>
                </div>


            </div>
        </div> --}}

        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                    Currency List
                </h3>
            </div>


            <div class="block-content block-content-full">
                {{-- error --}}
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                {{-- success --}}
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                <!-- DataTables init on table by adding .js-dataTable-buttons class, functionality is initialized in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table class="table table-bordered table-striped table-vcenter js-dataTable-buttons">
                    <thead>
                        <tr>
                            <th>Country</th>
                            <th> Name</th>
                            <th> Code</th>
                            <th> Symbol</th>
                            <th>Base Rate</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    {{-- <tbody>

                        @foreach ($currencies as $currency)
                            <tr>
                                <td>

                                    {{ $currency->country }}
                                    </a>
                                </td>
                                <td>
                                    {{ $currency->name }}
                                </td>
                                <td>
                                    {{ $currency->code }}
                                </td>
                                <td>
                                    {{ $currency->symbol }}
                                </td>
                                <td>
                                    {{ $currency->base_rate }}
                                </td>
                                <td>
                                    <a href="{{ url('currency/status/' . $currency->id) }}"
                                        class="btn btn-sm {{ $currency->is_active ? 'btn-danger' : 'btn-success' }}">
                                        {{ $currency->is_active ? 'Deactivate' : 'Activate' }}
                                    </a>
                                    {{ $currency->is_active ? 'Active' : 'Inactive' }}
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                        data-target="#exampleModal-{{ $currency->id }}">
                                        View Details
                                    </button>
                                </td>
                            </tr>

                            

                        @endforeach
                    </tbody> --}}

                    <tbody>

    @foreach ($currencies as $currency)

        <tr>

            <td>{{ $currency->country }}</td>

            <td>{{ $currency->name }}</td>

            <td>
                <span class="badge bg-primary">
                    {{ $currency->code }}
                </span>
            </td>

            <td>{{ $currency->symbol }}</td>

            <td>{{ number_format($currency->base_rate, 2) }}</td>

            <td>

                @if ($currency->is_active)

                    <span class="badge bg-success">
                        Active
                    </span>

                @else

                    <span class="badge bg-danger">
                        Inactive
                    </span>

                @endif

            </td>

            <td>

                <div class="d-flex gap-2">

                    {{-- Activate / Deactivate --}}
                    <a href="{{ url('currency/status/' . $currency->id) }}"
                        class="btn btn-sm {{ $currency->is_active ? 'btn-danger' : 'btn-success' }}">

                        {{ $currency->is_active ? 'Deactivate' : 'Activate' }}
                    </a>

                    {{-- Edit Base Rate --}}
                    <button type="button"
                        class="btn btn-sm btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#currencyModal-{{ $currency->id }}">

                        Edit Rate
                    </button>

                </div>

            </td>

        </tr>

    @endforeach

</tbody>
</table>

{{-- MODALS MUST BE OUTSIDE TABLE --}}
@foreach ($currencies as $currency)

    <div class="modal fade"
        id="currencyModal-{{ $currency->id }}"
        tabindex="-1"
        aria-labelledby="currencyModalLabel-{{ $currency->id }}"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title"
                        id="currencyModalLabel-{{ $currency->id }}">

                        Edit Base Rate - {{ $currency->code }}
                    </h5>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                    </button>

                </div>

                <form action="{{ url('currency/update/' . $currency->id) }}"
                    method="POST">

                    @csrf
                    @method('PUT')

                    <div class="modal-body">

                        <div class="mb-3">

                            <label class="form-label">
                                Country
                            </label>

                            <input type="text"
                                class="form-control"
                                value="{{ $currency->country }}"
                                readonly>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Currency
                            </label>

                            <input type="text"
                                class="form-control"
                                value="{{ $currency->name }} ({{ $currency->code }})"
                                readonly>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Base Rate
                            </label>

                            <input type="number"
                                step="0.0001"
                                name="base_rate"
                                class="form-control"
                                value="{{ $currency->base_rate }}"
                                required>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                            Close
                        </button>

                        <button type="submit"
                            class="btn btn-primary">

                            Update Rate
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endforeach

                </table>
            </div>
        </div>



    </div>
@endsection

@section('script')
    <script src="{{ asset('src/assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('src/assets/js/plugins/datatables-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('src/assets/js/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('src/assets/js/plugins/datatables-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('src/assets/js/plugins/datatables-buttons/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('src/assets/js/plugins/datatables-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('src/assets/js/plugins/datatables-buttons-jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('src/assets/js/plugins/datatables-buttons-pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('src/assets/js/plugins/datatables-buttons-pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('src/assets/js/plugins/datatables-buttons/buttons.print.min.js') }}"></script>
    <script src="{{ asset('src/assets/js/plugins/datatables-buttons/buttons.html5.min.js') }}"></script>

    <!-- Page JS Code -->
    <script src="{{ asset('src/assets/js/pages/be_tables_datatables.min.js') }}"></script>
@endsection
