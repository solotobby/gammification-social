@extends('layouts.admin')
@section('styles')
<link rel="stylesheet" href="{{asset('src/assets/js/plugins/datatables-bs5/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('src/assets/js/plugins/datatables-buttons-bs5/css/buttons.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('src/assets/js/plugins/datatables-responsive-bs5/css/responsive.bootstrap5.min.css')}}">

@endsection

@section('content')

<div class="content">
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                   Users Payout Information
                </h3>
            </div>

            <div class="block-content block-content-full">
                <!-- DataTables init on table by adding .js-dataTable-buttons class, functionality is initialized in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table class="table table-bordered table-striped table-vcenter js-dataTable-buttons">

                    <thead>
                        <th>Content</th>
                        <th>Payout Method</th>
                        <th>Bank Name</th>
                        <th>Acc Number</th>
                        <th>currency</th>
                        {{-- <th>Total Comment</th> --}}
                        <th>When Posted</th>
                    </thead>

                    <tbody>
                        @foreach ($withdrawals as $post)
                            <tr>
                                {{-- <td class="text-center">1</td> --}}
                                <td>
                                    
                                    {{-- {{ $post->content }} --}}
                                    <a href="{{ url('user/info/'.$post->user->id) }}">{{ $post->user->name }}</a>
                                </td>
                                <td>
                                    {{ $post->payment_method }}
                                </td>
                                <td>
                                    {{ $post->bank_name }}
                                </td>
                                <td>
                                    {{ $post->account_number }}
                                </td>
                               
                                <td>
                                    {{ $post->currency}}
                                </td>

                                <td>
                                    <em class="text-muted">{{ $post->created_at }}</em>
                                    {{-- {{   $post->created_at?->shortAbsoluteDiffForHumans() }}  --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $withdrawals->links() }}


                {{-- <a href="{{ url('user/info/' . $user->id) }}" class="btn btn-secondary mt-3"> Back to Users </a> --}}
            </div>

        </div>
    </div>


@endsection