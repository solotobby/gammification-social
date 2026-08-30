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
        .dash-select, .dash-textarea {
            width: 100%;
            padding: 0.625rem 0.875rem;
            border-radius: 10px;
            border: 1px solid var(--dash-border);
            font: inherit;
            font-size: 0.875rem;
            background: var(--dash-surface);
            color: var(--dash-text);
        }
        .dash-textarea { min-height: 96px; resize: vertical; }
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
        .dash-cover-preview {
            margin-top: 0.75rem;
            border: 1px solid var(--dash-border);
            border-radius: 12px;
            overflow: hidden;
            background: #f8fafc;
            max-width: 420px;
        }
        .dash-cover-preview img {
            display: block;
            width: 100%;
            height: auto;
            aspect-ratio: 1200 / 630;
            object-fit: cover;
        }
    </style>
@endsection

@section('content')
    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>Edit blog post</h1>
                    <p>Update content and featured image</p>
                </div>
                <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
                    <a href="{{ url('blog/' . $blog->slug) }}" target="_blank" class="dash-btn dash-btn--ghost">
                        <i class="fa fa-external-link"></i> View
                    </a>
                    <a href="{{ route('admin.blog.index') }}" class="dash-btn dash-btn--ghost">
                        <i class="fa fa-arrow-left"></i> All posts
                    </a>
                </div>
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
                    <form action="{{ route('admin.blog.update', $blog->slug) }}" method="POST" enctype="multipart/form-data" class="dash-form">
                        @csrf
                        @method('PUT')

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
                            <input type="text" id="title" name="title" class="dash-input"
                                value="{{ old('title', $blog->title) }}" required>
                        </div>

                        <div class="dash-field">
                            <label for="blog_category_id">Category</label>
                            <select id="blog_category_id" name="blog_category_id" class="dash-select" required>
                                <option value="">Select category</option>
                                @foreach ($category as $cate)
                                    <option value="{{ $cate->id }}" @selected((string) old('blog_category_id', $blog->blog_category_id) === (string) $cate->id)>
                                        {{ $cate->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="dash-field">
                            <label for="excerpt">Excerpt</label>
                            <textarea id="excerpt" name="excerpt" class="dash-textarea" rows="3">{{ old('excerpt', $blog->excerpt) }}</textarea>
                        </div>

                        <div class="dash-field">
                            <label for="cover_image">Featured image</label>
                            @if ($blog->hasCover())
                                <div class="dash-cover-preview">
                                    <img src="{{ $blog->cover_url }}" alt="Current featured image"
                                        onerror="this.closest('.dash-cover-preview').remove();">
                                </div>
                                <div class="dash-check" style="margin-top:.75rem;">
                                    <input type="checkbox" id="remove_cover_image" name="remove_cover_image" value="1" @checked(old('remove_cover_image'))>
                                    <label for="remove_cover_image" style="margin:0;color:var(--dash-text);">Remove current image</label>
                                </div>
                            @else
                                <p class="dash-field__hint">No featured image yet.</p>
                            @endif
                            <input type="file" id="cover_image" name="cover_image" class="dash-input" accept="image/*" style="margin-top:.75rem;">
                            <p class="dash-field__hint">Upload a new image to replace the current cover (JPG, PNG, or WebP). Stored on DigitalOcean Spaces.</p>
                        </div>

                        <div class="dash-field">
                            <label for="js-ckeditor">Body</label>
                            <textarea id="js-ckeditor" name="content">{{ old('content', $blog->content) }}</textarea>
                        </div>

                        <div>
                            <button type="submit" class="dash-btn dash-btn--primary">
                                <i class="fa fa-check"></i> Save changes
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
