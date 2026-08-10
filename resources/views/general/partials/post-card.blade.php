@props(['blog', 'reveal' => true])

<article @class(['post-card', 'reveal' => $reveal])>
    <a href="{{ url('blog/' . $blog->slug) }}">
        <div class="post-card__img"
            @if ($blog->cover_image) style="background-image:url('{{ $blog->cover_image }}')" @endif>
            <span class="post-card__cat">{{ $blog->blogCategory->name ?? 'Article' }}</span>
        </div>
        <div class="post-card__body">
            <h3>{{ $blog->title }}</h3>
            <p>{{ Str::limit(strip_tags($blog->excerpt ?? ''), 120) }}</p>
            <div class="post-meta">
                <div class="avatar" style="background:var(--grad-brand)">PK</div>
                <span>Payhankey</span>
                <span>·</span>
                <span>{{ ($blog->published_at ?? $blog->created_at)?->format('M d, Y') }}</span>
                @if (! empty($blog->content))
                    <span>·</span>
                    <span>{{ max(1, (int) ceil(str_word_count(strip_tags($blog->content)) / 200)) }} min read</span>
                @endif
            </div>
        </div>
    </a>
</article>
