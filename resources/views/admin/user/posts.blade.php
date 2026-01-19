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
                    POSTS MADE BY {{ $user->name }}
                </h3>
            </div>

            <div class="block-content block-content-full">
                <!-- DataTables init on table by adding .js-dataTable-buttons class, functionality is initialized in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table class="table table-bordered table-striped table-vcenter js-dataTable-buttons">

                    <thead>
                        <th>Content</th>
                            <th>Total Likes</th>
                            <th>Unique View</th>
                            <th>Total Views</th>
                            <th>Unique Comment</th>
                            <th>Total Comment</th>
                            <th>When Posted</th>
                    </thead>
                    <tbody>
                        @foreach ($posts as $post)
                            <tr>
                                {{-- <td class="text-center">1</td> --}}
                                <td>
                                    {{ \Illuminate\Support\Str::words($post->content, 3, '...') }}
                                    {{-- {{ $post->content }} --}}
                                    {{-- <a href="{{ url('user/info/'.$post->id) }}">{{ $post->user->name }}</a> --}}
                                </td>
                                <td>
                                    {{ $post->likes }}
                                </td>
                                <td>
                                    {{ $post->views }}
                                </td>
                                <td>
                                    {{ sumCounter($post->views, $post->views_external) }}
                                </td>
                                <td>
                                    {{ $post->comments }}
                                </td>
                                <td>
                                    {{ sumCounter($post->comments, $post->comments_external) }}
                                </td>



                                <td>
                                    <em class="text-muted">{{ $post->created_at }}</em>
                                    {{-- {{   $post->created_at?->shortAbsoluteDiffForHumans() }}  --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>


                <a href="{{ url('user/info/' . $user->id) }}" class="btn btn-secondary mt-3"> Back to Users </a>
            </div>

        </div>
    </div>


@endsection