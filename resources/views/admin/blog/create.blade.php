@extends('layouts.admin')
{{-- @section('styles')
<link rel="stylesheet" href="{{asset('src/assets/js/plugins/datatables-bs5/css/dataTables.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('src/assets/js/plugins/datatables-buttons-bs5/css/buttons.bootstrap5.min.css')}}">
<link rel="stylesheet" href="{{asset('src/assets/js/plugins/datatables-responsive-bs5/css/responsive.bootstrap5.min.css')}}">

@endsection --}}

@section('content')


    <!-- Main Container -->
    <main id="main-container">

        <!-- Hero -->
        <div class="bg-body-light">
            <div class="content content-full">
                <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                    <h1 class="flex-grow-1 fs-3 fw-semibold my-2 my-sm-3">New Blog Post</h1>
                    <nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">Dashboard</li>
                            <li class="breadcrumb-item">Blog</li>
                            <li class="breadcrumb-item active" aria-current="page">Create</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <!-- END Hero -->

        <!-- Page Content -->
        <div class="content content-full content-boxed">
            <!-- New Post -->
            <form action="{{ route('store.blog') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="block">
                    <div class="block-header block-header-default">
                        <a class="btn btn-alt-secondary" href="{{url('view/blog/list')}}">
                            <i class="fa fa-arrow-left me-1"></i> Manage Posts
                        </a>
                        <div class="block-options">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" value="" id="dm-post-add-active"
                                    name="dm-post-add-active">
                                <label class="form-check-label" for="dm-post-add-active">Set active</label>
                            </div>
                        </div>
                    </div>
                    <div class="block-content">
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
                        @if ($errors->any())
                            <div
                                style="background:#ffe6e6; color:#cc0000; padding:10px; border-radius:5px; margin-bottom:15px;">
                                <ul style="margin:0; padding-left:20px;">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="row justify-content-center push">
                            <div class="col-md-10">
                                <div class="mb-4">
                                    <label class="form-label" for="dm-post-add-title">Title</label>
                                    <input type="text" class="form-control" id="dm-post-add-title" value="{{ old('title') }}" name="title"
                                        placeholder="Enter a title..">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label" for="dm-post-add-title">Category</label>
                                    <select name="blog_category_id" class="form-control" required>
                                        <option value="">Select One</option>
                                        @foreach ($category as $cate)
                                            <option value="{{ $cate->id }}"> {{ $cate->name }} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label" for="dm-post-add-excerpt">Excerpt</label>
                                    <textarea class="form-control" id="dm-post-add-excerpt" name="excerpt" rows="3" placeholder="Enter an excerpt..."> {{ old('excerpt') }} </textarea>
                                    <div class="form-text">Visible on blog post list as a small description of the post.
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-xl-6">
                                        <label class="form-label" for="dm-post-add-image">Featured Image</label>
                                        <input class="form-control" type="file" name="cover_image"
                                            id="dm-post-add-image">
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <!-- CKEditor (js-ckeditor-inline + js-ckeditor ids are initialized in Helpers.jsCkeditor()) -->
                                    <!-- For more info and examples you can check out http://ckeditor.com -->
                                    <label class="form-label">Body</label>
                                    <textarea id="js-ckeditor" name="content"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="block-content bg-body-light">
                        <div class="row justify-content-center push">
                            <div class="col-md-10">
                                <button type="submit" class="btn btn-alt-primary">
                                    <i class="fa fa-fw fa-check opacity-50 me-1"></i> Create Post
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <!-- END New Post -->
        </div>
        <!-- END Page Content -->
    </main>
    <!-- END Main Container -->

@endsection

<script src="{{ asset('src/assets/js/dashmix.app.min.js') }}"></script>

<!-- Page JS Plugins -->
<script src="{{ asset('src/assets/js/plugins/ckeditor/ckeditor.js') }}"></script>

<!-- Page JS Helpers (CKEditor plugin) -->
<script>
    Dashmix.onLoad(function() {
        CKEDITOR.config.height = '450px';
        Dashmix.helpers(['js-ckeditor']);
    });
</script>
