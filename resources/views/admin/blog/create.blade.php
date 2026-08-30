@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-form { display: grid; gap: 1.25rem; max-width: 820px; }

        .dash-field label {
            display: block;
            margin-bottom: 0.375rem;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--dash-muted);
        }

        .dash-field__hint {
            margin-top: 0.375rem;
            font-size: 0.8125rem;
            color: var(--dash-muted);
        }

        .dash-select {
            width: 100%;
            padding: 0.625rem 0.875rem;
            border-radius: 10px;
            border: 1px solid var(--dash-border);
            font: inherit;
            font-size: 0.875rem;
            background: var(--dash-surface);
            color: var(--dash-text);
        }

        .dash-textarea {
            width: 100%;
            min-height: 96px;
            padding: 0.625rem 0.875rem;
            border-radius: 10px;
            border: 1px solid var(--dash-border);
            font: inherit;
            font-size: 0.875rem;
            resize: vertical;
        }

        .dash-check {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }

        .dash-errors {
            padding: 0.875rem 1rem;
            border-radius: 12px;
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            font-size: 0.875rem;
        }

        .dash-errors ul { margin: 0; padding-left: 1.25rem; }
    </style>
@endsection

@section('content')
    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>New blog post</h1>
                    <p>Create and publish content for the public blog</p>
                </div>
                <a href="{{ route('admin.blog.index') }}" class="dash-btn dash-btn--ghost">
                    <i class="fa fa-arrow-left"></i> All posts
                </a>
            </header>

            @if (session('success'))
                <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="dash-alert dash-alert--error">{{ session('error') }}</div>
            @endif

            <div class="dash-card">
                <div class="dash-card__head">
                    <h2 class="dash-card__title">Post details</h2>
                </div>
                <div class="dash-card__body">
                    <form action="{{ route('admin.blog.store') }}" method="POST" enctype="multipart/form-data" class="dash-form">
                        @csrf

                        @if ($errors->any())
                            <div class="dash-errors">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="dash-field">
                            <label for="title">Title</label>
                            <input type="text" id="title" name="title" class="dash-input" value="{{ old('title') }}"
                                placeholder="Enter a title" required>
                        </div>

                        <div class="dash-field">
                            <label for="blog_category_id">Category</label>
                            <select id="blog_category_id" name="blog_category_id" class="dash-select" required>
                                <option value="">Select category</option>
                                @foreach ($category as $cate)
                                    <option value="{{ $cate->id }}" @selected(old('blog_category_id') == $cate->id)>{{ $cate->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="dash-field">
                            <label for="excerpt">Excerpt</label>
                            <textarea id="excerpt" name="excerpt" class="dash-textarea" rows="3"
                                placeholder="Short description shown in blog listings">{{ old('excerpt') }}</textarea>
                            <p class="dash-field__hint">Visible on the blog list as a brief summary.</p>
                        </div>

                        <div class="dash-field">
                            <label for="cover_image">Featured image</label>
                            <input type="file" id="cover_image" name="cover_image" class="dash-input" accept="image/*">
                        </div>

                        <div class="dash-field">
                            <label for="js-ckeditor">Body</label>
                            <textarea id="js-ckeditor" name="content">{{ old('content') }}</textarea>
                        </div>

                        <div class="dash-check">
                            <input type="checkbox" id="dm-post-add-active" name="dm-post-add-active" value="1">
                            <label for="dm-post-add-active" style="margin:0; color:var(--dash-text);">Mark as active when publishing</label>
                        </div>

                        <div>
                            <button type="submit" class="dash-btn dash-btn--primary">
                                <i class="fa fa-check"></i> Create post
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('src/assets/js/plugins/ckeditor/ckeditor.js') }}"></script>
    <script>
        Dashmix.onLoad(function () {
            CKEDITOR.config.height = '450px';
            Dashmix.helpers(['js-ckeditor']);

            document.querySelector('.dash-form')?.addEventListener('submit', function () {
                if (typeof CKEDITOR !== 'undefined') {
                    for (var name in CKEDITOR.instances) {
                        CKEDITOR.instances[name].updateElement();
                    }
                }
            });
        });
    </script>
@endsection
