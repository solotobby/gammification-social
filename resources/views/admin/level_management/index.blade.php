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
           Level List
         </h3>
       </div>
       <div class="block-content block-content-full">

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

        <table class="table table-bordered table-striped table-vcenter js-dataTable-buttons">
           <thead>
             <tr>
               {{-- <th class="text-center" style="width: 80px;">#</th> --}}
               <th>Name</th>
               <th>Amount($)</th>
               <th>Reg. Bonus</th>
               
               <th>Ref. Bonus</th>
               <th>Earning per 1k View</th>
               <th>Earning Per 1k Like</th>
               <th>Earning Per 1k Comment</th>
               
               <th>Actions</th>
             </tr>
           </thead>
           <tbody>
            <tr>

            </tr>
               @foreach ($levels as $level)
               <tr>
                   <td>{{ $level->name }}</td>
                   <td>{{ $level->amount }}</td>
                   <td>{{ $level->reg_bonus }}</td>
                   <td>{{ $level->ref_bonus }}</td>
                   <td>{{ $level->earning_per_view }}</td>
                   <td>{{ $level->earning_per_like }}</td>
                   <td>{{ $level->earning_per_comment }}</td>
                   <td>
                    @if($level->planId)
                        {{ $level->planId->plan_id }}
                    @else
                        <a href="{{url('generate/plan/'.$level->id)}}"  class="btn btn-sm btn-alt-primary @disabled($level->name === 'Basic') ">Generate Paystack PlanId</a></td>
                    @endif

                   
                   {{-- <td>{{ $partner->country}}</td>
                   <td>{{ $partner->phone}}</td>
                   <td>{{ $partner->account_number == null ? 'NILL' : 'CREATED'}}</td>
                   <td>
                    @if($partner->status == true)
                        <a href=" {{  url('partner/'.$partner->id)}}" target="_blank" class="btn btn-sm btn-alt-primary">View Account</a>
                    @else
                    <a href="{{ url('partner/'.$partner->id.'/activate') }}" class="btn btn-sm btn-alt-warning"> {{ $partner->status == true ? 'Active' : 'Not Active' }}</a>
                    @endif
                    </td>
                    <td>
                     <em class="text-muted">{{$partner->created_at?->shortAbsoluteDiffForHumans()}} ago</em>
                   </td> --}}
                 </tr>
               @endforeach
           </tbody>
         </table>



       </div>
   </div>
</div>  

@endsection