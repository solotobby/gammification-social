<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $blogCategories = BlogCategory::orderBy('name')->get();

        $blogs = Blog::query()
            ->with('blogCategory:id,name')
            ->where('status', 'PUBLISHED')
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('blog_category_id', $request->integer('category'));
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%' . $request->string('q') . '%';
                $query->where(function ($q) use ($term) {
                    $q->where('title', 'like', $term)
                        ->orWhere('excerpt', 'like', $term);
                });
            })
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(9)
            ->withQueryString();

        return view('general.blog', compact('blogs', 'blogCategories'));
    }

    public function show(string $slug)
    {
        $blog = Blog::with('blogCategory:id,name')
            ->where('slug', $slug)
            ->where('status', 'PUBLISHED')
            ->firstOrFail();

        $blog->increment('views');

        $limit = 3;
        $select = ['id', 'title', 'slug', 'excerpt', 'cover_image', 'content', 'created_at', 'published_at', 'blog_category_id'];

        $suggestions = Blog::query()
            ->with('blogCategory:id,name')
            ->where('status', 'PUBLISHED')
            ->where('id', '!=', $blog->id)
            ->when($blog->blog_category_id, fn ($query) => $query->where('blog_category_id', $blog->blog_category_id))
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get($select);

        if ($suggestions->count() < $limit) {
            $excludeIds = $suggestions->pluck('id')->push($blog->id)->all();

            $fillers = Blog::query()
                ->with('blogCategory:id,name')
                ->where('status', 'PUBLISHED')
                ->whereNotIn('id', $excludeIds)
                ->orderByDesc('published_at')
                ->orderByDesc('created_at')
                ->limit($limit - $suggestions->count())
                ->get($select);

            $suggestions = $suggestions->concat($fillers)->values();
        }

        return view('general.show', compact('blog', 'suggestions'));
    }
}
