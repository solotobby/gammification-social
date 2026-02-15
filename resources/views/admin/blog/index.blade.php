@extends('layouts.admin')

@section('content')
    <!-- Main Container -->
    <main id="main-container">

        <!-- Hero -->
        <div class="bg-body-light">
            <div class="content content-full">
                <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                    <h1 class="flex-grow-1 fs-3 fw-semibold my-2 my-sm-3">Manage All Posts</h1>
                    <nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">Pages</li>
                            <li class="breadcrumb-item">Blog</li>
                            <li class="breadcrumb-item active" aria-current="page">Manage</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <!-- END Hero -->

        <!-- Page Content -->
        <div class="content content-full">
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

            <!-- Posts Statistics -->
            <div class="row text-center">
                <div class="col-6 col-xl-3">
                    <!-- All Posts -->
                    <a class="block block-rounded" href="{{ url('view/blog/list') }}">
                        <div class="block-content block-content-full">
                            <div class="py-md-3">
                                <div class="py-3 d-none d-md-block">
                                    <i class="far fa-2x fa-file-alt text-primary"></i>
                                </div>
                                <p class="fs-4 fw-bold mb-0">
                                    {{ $totalPosts }}
                                </p>
                                <p class="text-muted mb-0">
                                    All Posts
                                </p>
                            </div>
                        </div>
                    </a>
                    <!-- END All Posts -->
                </div>
                <div class="col-6 col-xl-3">
                    <!-- Active Posts -->
                    <a class="block block-rounded" href="{{ url('view/blog/list') }}">
                        <div class="block-content block-content-full">
                            <div class="py-md-3">
                                <div class="py-3 d-none d-md-block">
                                    <i class="far fa-2x fa-eye text-primary"></i>
                                </div>
                                <p class="fs-4 fw-bold mb-0">
                                    {{ $totalPublished }}
                                </p>
                                <p class="text-muted mb-0">
                                    Active
                                </p>
                            </div>
                        </div>
                    </a>
                    <!-- END Active Posts -->
                </div>
                <div class="col-6 col-xl-3">
                    <!-- Draft Posts -->
                    <a class="block block-rounded" href="{{ url('view/blog/list') }}">
                        <div class="block-content block-content-full">
                            <div class="py-md-3">
                                <div class="py-3 d-none d-md-block">
                                    <i class="fa fa-2x fa-pencil-alt text-primary"></i>
                                </div>
                                <p class="fs-4 fw-bold mb-0">
                                    {{$totalDrafts}}
                                </p>
                                <p class="text-muted mb-0">
                                    Drafts
                                </p>
                            </div>
                        </div>
                    </a>
                    <!-- END Draft Posts -->
                </div>
                <div class="col-6 col-xl-3">
                    <!-- New Post -->
                    <a class="block block-rounded" href="{{ url('create/blog/post') }}">
                        <div class="block-content block-content-full">
                            <div class="py-md-3">
                                <div class="py-3 d-none d-md-block">
                                    <i class="fa fa-2x fa-plus text-primary"></i>
                                </div>
                                <p class="fs-4 fw-bold mb-0">
                                    <i class="fa fa-plus text-primary me-1 d-md-none"></i> New Post
                                </p>
                                <p class="text-muted mb-0">
                                    by John Doe
                                </p>
                            </div>
                        </div>
                    </a>
                    <!-- END New Post -->
                </div>
            </div>
            <!-- Post Statistics -->

            <!-- Posts -->
            <div class="block">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Posts (150)</h3>
                </div>
                <div class="block-content">
                    <!-- Search Posts -->
                    <form class="push" action="@" method="POST">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search Posts..">
                            <span class="input-group-text">
                                <i class="fa fa-fw fa-search"></i>
                            </span>
                        </div>
                    </form>
                    <!-- END Search Posts -->

                    <!-- Posts Table -->
                    <table class="table table-striped table-borderless table-vcenter">
                        <thead>
                            <tr class="bg-body-dark">
                                <th style="width: 33%;">Title</th>
                                <th class="d-none d-sm-table-cell">Category</th>
                                <th class="d-none d-xl-table-cell">Created</th>
                                <th class="d-none d-xl-table-cell">Published</th>
                                <th style="width: 100px;" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php. $list = 1; ?> 
                            @foreach ($blogs as $blog)
                                <tr>
                                    <td>
                                        <i class="fa fa-eye text-success me-1"></i>
                                        <a href="{{ url('blog/' . $blog->slug) }}" target="_blank">
                                            {{ $blog->title }}
                                        </a>
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        {{ $blog->blogCategory ? $blog->blogCategory->name : 'Uncategorized' }}
                                        {{-- <a href="">{{ $blog['blogCategory']['name'] }}</a> --}}
                                    </td>
                                    <td class="d-none d-xl-table-cell">
                                        {{ \Carbon\Carbon::parse($blog->created_at)->format('F d, Y \a\t H:i A') }}
                                    </td>
                                    <td class="d-none d-xl-table-cell">
                                        {{ \Carbon\Carbon::parse($blog->published_at)->format('F d, Y \a\t H:i A') }}
                                    </td>
                                    <td class="text-center">
                                        <a class="btn btn-sm btn-alt-secondary" href="be_pages_blog_post_edit.html">
                                            <i class="fa fa-fw fa-pencil-alt text-primary"></i>
                                        </a>
                                        <a class="btn btn-sm btn-alt-secondary"
                                            href="{{ url('delete/blog/' . $blog->slug) }}">
                                            <i class="fa fa-fw fa-times text-danger"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- END Posts Table -->

                    <div class="d-flex justify-content-center mt-4">
                        {!! $blogs->links('pagination::bootstrap-4') !!}
                    </div>


                </div>
            </div>
            <!-- END Posts -->
        </div>
        <!-- END Page Content -->
    </main>
    <!-- END Main Container -->
@endsection
