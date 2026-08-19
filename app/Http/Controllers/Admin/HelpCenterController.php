<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HelpArticle;
use App\Models\HelpCategory;
use App\Services\AdminAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HelpCenterController extends Controller
{
    public function __construct(private AdminAuditService $audit) {}

    public function list()
    {
        $baseQuery = HelpArticle::query()->with('category:id,name');

        $total = (clone $baseQuery)->count();
        $published = (clone $baseQuery)->where('published', true)->count();
        $drafts = (clone $baseQuery)->where('published', false)->count();

        $articles = (clone $baseQuery)
            ->orderBy('sort_order')
            ->latest()
            ->paginate(12);

        return view('admin.help.index', compact('total', 'published', 'drafts', 'articles'));
    }

    public function create()
    {
        $categories = HelpCategory::query()->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.help.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'help_category_id' => 'required|uuid|exists:help_categories,id',
            'body' => 'required|string|min:40',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:180',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'published' => 'nullable|boolean',
        ]);

        $published = $request->boolean('published');

        $article = HelpArticle::create([
            'title' => $data['title'],
            'help_category_id' => $data['help_category_id'],
            'body' => $data['body'],
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? Str::limit(strip_tags($data['body']), 155),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'published' => $published,
            'published_at' => $published ? now() : null,
        ]);

        $this->audit->log('help.created', $article);

        return redirect()
            ->route('admin.help.index')
            ->with('success', 'Help article created successfully.');
    }

    public function delete(string $slug)
    {
        $article = HelpArticle::query()->where('slug', $slug)->firstOrFail();
        $article->delete();

        $this->audit->log('help.deleted', $article, ['slug' => $slug]);

        return back()->with('success', 'Help article deleted successfully.');
    }
}
