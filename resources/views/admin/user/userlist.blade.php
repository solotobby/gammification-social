@extends('layouts.admin')

@section('styles')
<link rel="stylesheet" href="{{asset('src/assets/js/plugins/datatables-bs5/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('src/assets/js/plugins/datatables-buttons-bs5/css/buttons.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('src/assets/js/plugins/datatables-responsive-bs5/css/responsive.bootstrap5.min.css')}}">

@endsection
@section('content')

  
<div class="content content-full content-boxed">
 <!-- Dynamic Table with Export Buttons -->
 <div class="block block-rounded">
    <div class="block-header block-header-default">
      <h3 class="block-title">
        Users List
      </h3>
    </div>
    <div class="block-content block-content-full">
      <!-- DataTables init on table by adding .js-dataTable-buttons class, functionality is initialized in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
      <table class="table table-bordered table-striped table-vcenter js-dataTable-buttons">
        <thead>
          <tr>
            {{-- <th class="text-center" style="width: 80px;">#</th> --}}
            <th>Name</th>
            <th class="d-none d-sm-table-cell" style="width: 30%;">Email</th>
            <th class="d-none d-sm-table-cell" style="width: 15%;">Level</th>
            <th style="width: 15%;">Registered</th>
          </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr>
                {{-- <td class="text-center">1</td> --}}
                <td class="fw-semibold">
                  <a href="#">{{ $user->name }}</a>
                </td>
                <td class="d-none d-sm-table-cell">
                    {{ $user->email }}
                 
                </td>
                <td class="d-none d-sm-table-cell">

                  <span class="badge bg-info">{{ $user->level->name }}</span>
                </td>
                <td>
                  <em class="text-muted">{{$user->created_at?->shortAbsoluteDiffForHumans()}} ago</em>
                </td>
              </tr>
            @endforeach
         
         
         
        </tbody>
      </table>
    </div>
  </div>
</div>
  <!-- END Dynamic Table with Export Buttons -->


  
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