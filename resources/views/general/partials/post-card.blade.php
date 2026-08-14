@props(['blog', 'reveal' => true])

@php
    $readMins = ! empty($blog->content)
        ? max(1, (int) ceil(str_word_count(strip_tags($blog->content)) / 200))
        : null;
    $coverUrl = $blog->cover_url ?? \App\Models\Blog::normalizeImageUrl($blog->cover_image ?? null);
@endphp

<article @class(['apl-blog-card', 'reveal' => $reveal])>
  <a href="{{ url('blog/' . $blog->slug) }}" class="apl-blog-card__link">
    <div class="apl-blog-card__media">
      @if ($coverUrl)
        <img
          class="apl-blog-card__img"
          src="{{ $coverUrl }}"
          alt=""
          loading="lazy"
          decoding="async"
          onerror="this.remove(); this.parentElement.classList.add('is-fallback');"
        >
      @endif
      <span class="apl-blog-card__cat">{{ $blog->blogCategory->name ?? 'Article' }}</span>
    </div>
    <div class="apl-blog-card__body">
      <h3>{{ $blog->title }}</h3>
      <p>{{ Str::limit(strip_tags($blog->excerpt ?? ''), 110) }}</p>
      <div class="apl-blog-meta">
        <span>{{ ($blog->published_at ?? $blog->created_at)?->format('M d, Y') }}</span>
        @if ($readMins)
          <span>·</span>
          <span>{{ $readMins }} min read</span>
        @endif
      </div>
    </div>
  </a>
</article>
