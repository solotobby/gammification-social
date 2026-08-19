<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademyArticle;
use App\Models\AcademyCategory;
use App\Services\AdminAuditService;
use App\Services\ImageUploadService;
use App\Support\StoredMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AcademyController extends Controller
{
    public function __construct(
        private AdminAuditService $audit,
        private ImageUploadService $images,
    ) {}

    public function list()
    {
        $baseQuery = AcademyArticle::query()->with('category:id,name');

        $total = (clone $baseQuery)->count();
        $published = (clone $baseQuery)->where('published', true)->count();
        $drafts = (clone $baseQuery)->where('published', false)->count();

        $articles = (clone $baseQuery)
            ->latest()
            ->paginate(10);

        return view('admin.academy.index', [
            'total' => $total,
            'published' => $published,
            'drafts' => $drafts,
            'articles' => $articles,
        ]);
    }

    public function create()
    {
        $categories = AcademyCategory::query()->orderBy('name')->get();

        return view('admin.academy.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'academy_category_id' => 'required|exists:academy_categories,id',
            'body' => 'required|string|min:200',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:180',
            'author' => 'nullable|string|max:120',
            'seo_score' => 'nullable|integer|min:0|max:100',
            'read_time' => 'nullable|integer|min:1|max:120',
            'faq_schema' => 'nullable|string',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:'.$this->images->maxFileKb(),
            'published' => 'nullable|boolean',
        ]);

        $faqSchema = null;
        if ($request->filled('faq_schema')) {
            $decoded = json_decode($request->string('faq_schema'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withInput()->withErrors(['faq_schema' => 'FAQ schema must be valid JSON.']);
            }
            $faqSchema = $decoded;
        }

        $imagePath = null;
        if ($request->hasFile('featured_image')) {
            $imagePath = $this->images->upload($request->file('featured_image'), 'payhankey_media/academy');
        }

        $published = $request->boolean('published');

        $article = AcademyArticle::create([
            'title' => $data['title'],
            'academy_category_id' => $data['academy_category_id'],
            'body' => $data['body'],
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? Str::limit(strip_tags($data['body']), 155),
            'faq_schema' => $faqSchema,
            'published' => $published,
            'featured_image' => $imagePath,
            'author' => $data['author'] ?? 'Payhankey Academy',
            'seo_score' => (int) ($data['seo_score'] ?? 0),
            'read_time' => (int) ($data['read_time'] ?? max(1, (int) ceil(str_word_count(strip_tags($data['body'])) / 200))),
            'published_at' => $published ? now() : null,
        ]);

        $this->audit->log('academy.created', $article);

        return redirect()
            ->route('admin.academy.index')
            ->with('success', 'Academy article created successfully.');
    }

    public function delete(string $slug)
    {
        $article = AcademyArticle::query()->where('slug', $slug)->firstOrFail();
        StoredMedia::delete($article->featured_image);
        $article->delete();

        $this->audit->log('academy.deleted', $article, ['slug' => $slug]);

        return back()->with('success', 'Academy article deleted successfully.');
    }
}
