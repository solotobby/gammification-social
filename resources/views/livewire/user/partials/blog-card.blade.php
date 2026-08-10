@props(['blog'])

<article class="pk-blog-card" wire:key="blog-{{ $blog->id }}">
    <a href="{{ url('blog/' . $blog->slug) }}" class="pk-blog-card-link">
        <div class="pk-blog-card-cover" @if ($blog->cover_image) style="background-image:url('{{ $blog->cover_image }}')" @endif>
            <span class="pk-blog-card-cat">{{ $blog->blogCategory->name ?? 'Article' }}</span>
        </div>
        <div class="pk-blog-card-body">
            <h3>{{ $blog->title }}</h3>
            <p>{{ Str::limit(strip_tags($blog->excerpt ?? ''), 110) }}</p>
            <div class="pk-blog-card-meta">
                <span class="pk-blog-card-avatar">PK</span>
                <span>Payhankey</span>
                <span aria-hidden="true">·</span>
                <span>{{ ($blog->published_at ?? $blog->created_at)?->format('M j, Y') }}</span>
                @if (! empty($blog->content))
                    <span aria-hidden="true">·</span>
                    <span>{{ max(1, (int) ceil(str_word_count(strip_tags($blog->content)) / 200)) }} min</span>
                @endif
            </div>
        </div>
    </a>
</article>
