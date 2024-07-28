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
        Withdrawal List
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
            <th >Amount</th>
            <th >Wallet</th>
            <th >Mode</th>
            <th >Status</th>
            <th >Requested</th>
          </tr>
        </thead>
        <tbody>
            @foreach ($lists as $list)
            <tr>
                {{-- <td class="text-center">1</td> --}}
                <td class="fw-semibold">
                  <a href="{{ url('user/info/'.$list->user->id) }}">{{ $list->user->name }}</a>
                </td>
                <td class="d-none d-sm-table-cell">
                    ${{ number_format($list->amount) }}/&#8358;{{ number_format($list->naira) }}
                </td>
                <td class="d-none d-sm-table-cell">
                  <span class="badge bg-info">{{ $list->wallet_type }}</span>
                </td>
                <td>
                    <a href="" data-bs-toggle="modal" data-bs-target="#modal-default-popout-upgrade-{{ $list->id }}">   {{ $list->method }}</a>
                </td>
                <td>
                    {{ $list->status }}
                </td>
                <td>
                    <em class="text-muted">{{$list->created_at?->shortAbsoluteDiffForHumans()}} ago</em>
                </td>
              </tr>


              <div class="modal fade" id="modal-default-popout-upgrade-{{ $list->id }}" tabindex="-1" role="dialog" aria-labelledby="modal-default-popout" aria-hidden="true">
                <div class="modal-dialog modal-dialog-popout" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title">Withdrawal Method</h5> 
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
    
                    <div class="modal-body pb-1">
                       
                        <hr>
                        <div class="block-content">
                          <ul class="list-group push">
                             <li class="list-group-item d-flex justify-content-between align-items-center mb-2">
                              Bank Name 
                               <span class="badge rounded-pill bg-info">{{ @$list->withdrawalMethod->bank_name }} </span>
                              
                             </li>
                             <li class="list-group-item d-flex justify-content-between align-items-center mb-2">
                              Account Name
                               <span class="badge rounded-pill bg-info">{{ @$list->user->name }}* </span>
                              
                             </li>
                             <li class="list-group-item d-flex justify-content-between align-items-center mb-2">
                              Account Number
                               <span class="badge rounded-pill bg-info">{{ @$list->withdrawalMethod->account_number }} </span>
                              
                             </li>

                             <li class="list-group-item d-flex justify-content-between align-items-center mb-2">
                              Amount
                               <span class="badge rounded-pill bg-info">&#8358;{{ number_format($list->amount) }} </span>
                              
                             </li> 
                          </ul>
                        </div>
                        
                    </div>
                    
                    <div class="modal-footer">
                      <a href="{{ url('withdrawal/list/'.$list->id) }}" class="btn btn-sm btn-primary">Update Payment</a>
                    <button type="button" class="btn btn-sm btn-alt-secondary" data-bs-dismiss="modal">Close</button>
                    {{-- @if($with->status != '1')
                        
                          @if(@$with->user->accountDetails->bank_name == '')
                            <a href="{{ url('update/withdrawal/manual/'.$with->id) }}" class="btn btn-sm btn-primary">Manual Approval</a>
                          @else
                            <a href="{{ url('update/withdrawal/'.$with->id) }}" class="btn btn-sm btn-primary">Approve</a>
                          @endif

                          <a href="{{ url('update/withdrawal/manual/'.$with->id) }}" class="btn btn-sm btn-secondary">Update Approval</a>
                        
                    @else
                    <a href="#" class="btn btn-sm btn-success diasbled">Approved</a>
                    @endif --}}
                    </div>
                </div>
                </div>
            </div> 
            
      

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