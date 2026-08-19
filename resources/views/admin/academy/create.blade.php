@extends('layouts.admin')

@section('styles')
    @include('admin.partials.dash-styles')
    <style>
        .dash-form { display: grid; gap: 1.25rem; max-width: 860px; }
        .dash-field label { display: block; margin-bottom: .375rem; font-size: .8125rem; font-weight: 600; color: var(--dash-muted); }
        .dash-field__hint { margin-top: .375rem; font-size: .8125rem; color: var(--dash-muted); }
        .dash-select, .dash-textarea {
            width: 100%; padding: .625rem .875rem; border-radius: 10px;
            border: 1px solid var(--dash-border); font: inherit; font-size: .875rem;
            background: var(--dash-surface); color: var(--dash-text);
        }
        .dash-textarea { min-height: 96px; resize: vertical; }
        .dash-check { display: flex; align-items: center; gap: .5rem; font-size: .875rem; }
        .dash-errors { padding: .875rem 1rem; border-radius: 12px; background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; font-size: .875rem; }
        .dash-errors ul { margin: 0; padding-left: 1.25rem; }
        .dash-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        @media (max-width: 720px) { .dash-grid-2 { grid-template-columns: 1fr; } }
    </style>
@endsection

@section('content')
    <div class="content p-0">
        <div class="dash">
            <header class="dash-header">
                <div>
                    <h1>New Academy article</h1>
                    <p>Create a Creator Academy guide (not a blog post)</p>
                </div>
                <a href="{{ route('admin.academy.index') }}" class="dash-btn dash-btn--ghost">
                    <i class="fa fa-arrow-left"></i> All articles
                </a>
            </header>

            @if (session('success'))
                <div class="dash-alert dash-alert--success">{{ session('success') }}</div>
            @endif

            <div class="dash-card">
                <div class="dash-card__head">
                    <h2 class="dash-card__title">Article details</h2>
                </div>
                <div class="dash-card__body">
                    <form action="{{ route('admin.academy.store') }}" method="POST" enctype="multipart/form-data" class="dash-form">
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
                            <input type="text" id="title" name="title" class="dash-input" value="{{ old('title') }}" required>
                        </div>

                        <div class="dash-grid-2">
                            <div class="dash-field">
                                <label for="academy_category_id">Category</label>
                                <select id="academy_category_id" name="academy_category_id" class="dash-select" required>
                                    <option value="">Select category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('academy_category_id') == $category->id)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="dash-field">
                                <label for="author">Author</label>
                                <input type="text" id="author" name="author" class="dash-input" value="{{ old('author', 'Payhankey Academy') }}">
                            </div>
                        </div>

                        <div class="dash-grid-2">
                            <div class="dash-field">
                                <label for="meta_title">Meta title</label>
                                <input type="text" id="meta_title" name="meta_title" class="dash-input" maxlength="70" value="{{ old('meta_title') }}">
                                <p class="dash-field__hint">Optional. Max 70 characters.</p>
                            </div>
                            <div class="dash-field">
                                <label for="seo_score">SEO score (0–100)</label>
                                <input type="number" id="seo_score" name="seo_score" class="dash-input" min="0" max="100" value="{{ old('seo_score', 0) }}">
                            </div>
                        </div>

                        <div class="dash-grid-2">
                            <div class="dash-field">
                                <label for="meta_description">Meta description</label>
                                <textarea id="meta_description" name="meta_description" class="dash-textarea" maxlength="180" rows="3">{{ old('meta_description') }}</textarea>
                            </div>
                            <div class="dash-field">
                                <label for="read_time">Read time (minutes)</label>
                                <input type="number" id="read_time" name="read_time" class="dash-input" min="1" max="120" value="{{ old('read_time') }}" placeholder="Auto from body if empty">
                                <label for="featured_image" style="margin-top:1rem;">Featured image</label>
                                <input type="file" id="featured_image" name="featured_image" class="dash-input" accept="image/*">
                            </div>
                        </div>

                        <div class="dash-field">
                            <label for="faq_schema">FAQ schema (JSON)</label>
                            <textarea id="faq_schema" name="faq_schema" class="dash-textarea" rows="5" placeholder='[{"q":"Question?","a":"Answer."}]'>{{ old('faq_schema') }}</textarea>
                            <p class="dash-field__hint">Optional JSON array of { "q": "...", "a": "..." } for FAQ rich results.</p>
                        </div>

                        <div class="dash-field">
                            <label for="js-ckeditor">Body</label>
                            <textarea id="js-ckeditor" name="body">{{ old('body') }}</textarea>
                        </div>

                        <div class="dash-check">
                            <input type="checkbox" id="published" name="published" value="1" @checked(old('published', true))>
                            <label for="published" style="margin:0;color:var(--dash-text);">Publish now</label>
                        </div>

                        <div>
                            <button type="submit" class="dash-btn dash-btn--primary">
                                <i class="fa fa-check"></i> Create Academy article
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
        });
    </script>
@endsection
