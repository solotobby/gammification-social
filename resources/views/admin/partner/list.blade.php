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
           Partner List
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
               {{-- <th class="text-center" style="width: 80px;">#</th> --}}
               <th>Name</th>
               <th >Email</th>
               <th >Country</th>
               <th>Phone</th>
               <th >Status</th>
               {{-- <th >Created</th> --}}
             </tr>
           </thead>
           <tbody>
               @foreach ($partners as $partner)
               <tr>
                   <td>{{ $partner->name }}</td>
                   <td>{{ $partner->email }}</td>
                   <td>{{ $partner->country}}</td>
                   <td>{{ $partner->phone}}</td>
                   <td>
                    @if($partner->status == true)
                        <a href=" {{  url('partner/'.$partner->id)}}" class="btn btn-sm btn-alt-primary">View Account</a>
                    @else
                    <a href="{{ url('partner/'.$partner->id.'/activate') }}" class="btn btn-sm btn-alt-warning"> {{ $partner->status == true ? 'Active' : 'Not Active' }}</a>
                    @endif
                    </td>
                   {{-- <td>
                     <em class="text-muted">{{$partner->created_at?->shortAbsoluteDiffForHumans()}} ago</em>
                   </td> --}}
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