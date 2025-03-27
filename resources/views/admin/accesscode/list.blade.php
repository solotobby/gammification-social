@extends('layouts.admin')

@section('styles')
<link rel="stylesheet" href="{{asset('src/assets/js/plugins/datatables-bs5/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('src/assets/js/plugins/datatables-buttons-bs5/css/buttons.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('src/assets/js/plugins/datatables-responsive-bs5/css/responsive.bootstrap5.min.css')}}">

@endsection

@section('content')

<!-- Page Content -->
<div class="content">
    <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">
          Access Code List
          </h3>
        </div>
        <div class="block-content block-content-full">
          <!-- DataTables init on table by adding .js-dataTable-buttons class, functionality is initialized in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
          <table class="table table-bordered table-striped table-vcenter js-dataTable-buttons">
            <thead>
              <tr>
                {{-- <th class="text-center" style="width: 80px;">#</th> --}}
                <th>Name</th>
                <th>Email</th>
                <th>Level</th>
                <th>Code</th>
                <th>Created</th>
              </tr>
            </thead>
            <tbody>
                @foreach ($lists as $user)
                <tr>
                    {{-- <td class="text-center">1</td> --}}
                    <td>
                     {{ $user->recipient_name }}
                    </td>
                    <td>
                        {{ $user->recipient_email }}
                    </td>
                    <td>
                        {{ $user->name }}
                    </td>
                    <td >
                      {{ $user->code }}
                    </td>
                    <td>
                      <em class="text-muted">{{$user->created_at}}</em>
                    </td>
                  </tr>
                @endforeach
             
             
             
            </tbody>
          </table>
        </div>
      </div>
    
</div>
  <!-- END Page Content -->




@endsection


@section('script')

<script src="{{asset('src/assets/js/plugins/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('src/assets/js/plugins/datatables-bs5/js/dataTables.bootstrap5.min.js')}}"></script>
  <script src="{{asset('src/assets/js/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
  <script src="{{asset('src/assets/js/plugins/datatables-responsive-bs5/js/responsive.bootstrap5.min.js')}}"></script>
  <script src="{{asset('src/assets/js/plugins/datatables-buttons/dataTables.buttons.min.js')}}"></script>
  <script src="{{asset('src/assets/js/plugins/datatables-buttons-bs5/js/buttons.bootstrap5.min.js')}}"></script>
  <script src="{{asset('src/assets/js/plugins/datatables-buttons-jszip/jszip.min.js')}}"></script>
  <script src="{{asset('src/assets/js/plugins/datatables-buttons-pdfmake/pdfmake.min.js')}}"></script>
  <script src="{{asset('src/assets/js/plugins/datatables-buttons-pdfmake/vfs_fonts.js')}}"></script>
  <script src="{{asset('src/assets/js/plugins/datatables-buttons/buttons.print.min.js')}}"></script>
  <script src="{{asset('src/assets/js/plugins/datatables-buttons/buttons.html5.min.js')}}"></script>

  <!-- Page JS Code -->
  <script src="{{asset('src/assets/js/pages/be_tables_datatables.min.js')}}"></script>


@endsection

