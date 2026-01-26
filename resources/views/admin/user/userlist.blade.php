@extends('layouts.admin')

@section('styles')
    <link rel="stylesheet" href="{{ asset('src/assets/js/plugins/datatables-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('src/assets/js/plugins/datatables-buttons-bs5/css/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('src/assets/js/plugins/datatables-responsive-bs5/css/responsive.bootstrap5.min.css') }}">
@endsection
@section('content')
    <div class="content content-full content-boxed">
        <!-- Dynamic Table with Export Buttons -->
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                    Users List - <i>{{ $level }}</i>
                </h3>
            </div>
            <div class="block-content block-content-full">
                <form action="{{ url('user/search') }}" method="GET">

                    <div class="mb-2">
                        <div class="input-group">
                            <input type="text" class="form-control" name="query" id="example-group1-input1"
                                name="example-group1-input1">
                        </div>
                    </div>
                    <button class="btn btn-primary mb-2" type="submit"> Search</button>
                </form>

                <!-- DataTables init on table by adding .js-dataTable-buttons class, functionality is initialized in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table class="table table-bordered table-striped table-vcenter">
                    <thead>
                        <tr>
                            {{-- <th class="text-center" style="width: 80px;">#</th> --}}
                            <th>Name</th>
                            <th>Email</th>
                            <th>Level</th>
                            <th>Verified at</th>
                            <th>Heard</th>
                            <th>Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                {{-- <td class="text-center">1</td> --}}
                                <td>
                                    <a href="{{ url('user/info/' . $user->id) }}">{{ $user->name }}</a>
                                </td>
                                <td>
                                    {{ $user->email }}
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $user?->activeLevel?->plan_name ?? 'Basic' }}</span>
                                </td>
                                <td>
                                    {{ $user->email_verified_at }}
                                </td>
                                <td>
                                    {{ $user->heard }}
                                </td>
                                <td>
                                    <em class="text-muted">{{ $user->created_at?->shortAbsoluteDiffForHumans() }} ago</em>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-4">
                    {!! $users->links('pagination::bootstrap-4') !!}
                </div>


            </div>
        </div>
    </div>
    <!-- END Dynamic Table with Export Buttons -->
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
