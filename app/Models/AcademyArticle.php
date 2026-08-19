<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AcademyArticle extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = [
        'academy_category_id',
        'title',
        'slug',
        'body',
        'meta_title',
        'meta_description',
        'faq_schema',
        'published',
        'featured_image',
        'author',
        'seo_score',
        'read_time',
        'published_at',
    ];

    protected $casts = [
        'faq_schema' => 'array',
        'published' => 'boolean',
        'published_at' => 'datetime',
        'seo_score' => 'integer',
        'read_time' => 'integer',
    ];

    protected $appends = ['featured_image_url', 'category_name'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (AcademyArticle $article) {
            if (empty($article->id)) {
                $article->id = (string) Str::uuid();
            }
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title).'-'.uniqid();
            }
            if (empty($article->read_time) && filled($article->body)) {
                $article->read_time = max(1, (int) ceil(str_word_count(strip_tags($article->body)) / 200));
            }
            if ($article->published && empty($article->published_at)) {
                $article->published_at = now();
            }
        });

        static::updating(function (AcademyArticle $article) {
            if ($article->isDirty('body') && ! $article->isDirty('read_time')) {
                $article->read_time = max(1, (int) ceil(str_word_count(strip_tags((string) $article->body)) / 200));
            }
            if ($article->published && empty($article->published_at)) {
                $article->published_at = now();
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(AcademyCategory::class, 'academy_category_id');
    }

    public function getCategoryNameAttribute(): ?string
    {
        return $this->category?->name;
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        return Blog::normalizeImageUrl($this->featured_image);
    }

    public function hasFeaturedImage(): bool
    {
        return filled($this->featured_image_url);
    }

    public function seoTitle(): string
    {
        return $this->meta_title ?: $this->title.' · Creator Academy';
    }

    public function seoDescription(): string
    {
        if (filled($this->meta_description)) {
            return (string) $this->meta_description;
        }

        return Str::limit(strip_tags((string) $this->body), 155);
    }

    public function safeBodyHtml(): string
    {
        $html = (string) ($this->body ?? '');
        if ($html === '') {
            return '';
        }

        $html = preg_replace_callback(
            '/(<img\b[^>]*\bsrc=["\'])(http:\/\/[^"\']+)(["\'])/i',
            function (array $m) {
                $url = Blog::normalizeImageUrl($m[2]) ?? $m[2];
                $url = preg_replace('#^http://#i', 'https://', $url);

                return $m[1].e($url).$m[3];
            },
            $html
        ) ?? $html;

        $html = preg_replace(
            '/<img\b(?![^>]*\bonerror=)/i',
            '<img onerror="this.remove()" loading="lazy"',
            $html
        ) ?? $html;

        return $html;
    }

    public function scopePublished($query)
    {
        return $query->where('published', true);
    }
}
