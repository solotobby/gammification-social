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
           Partner Transaction
         </h3>
       </div>
       <div class="block-content block-content-full">
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif
         <!-- DataTables init on table by adding .js-dataTable-buttons class, functionality is initialized in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
         <table class="table table-bordered table-striped table-vcenter js-dataTable-buttons">
           <thead>
             <tr>
                <th>Ref</th>
                <th>Display Name</th>
                <th>Amount</th>
                <th>Currency</th>
                <th>Status</th>
                <th>Validate</th>
             </tr>
           </thead>
           <tbody>
               @foreach ($transactions as $partner)
                <tr>
                    <td>{{ $partner->ref }}</td>
                    <td>{{ @$partner->user->partner->name }}</td>
                    <td>{{ $partner->amount }}</td>
                    <td>{{ $partner->currency}}</td>
                    <td>{{ $partner->status}}</td>
                    <td><a href="{{ url('agent/validate/activate/transaction/'.$partner->ref) }}" class="btn btn-alt-secondary"> Validate Payment </a></td>
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