<?php

namespace App\Http\Controllers;

use App\Models\AcademyArticle;
use App\Models\AcademyCategory;
use Illuminate\Http\Request;

class AcademyController extends Controller
{
    public function index(Request $request)
    {
        $categories = AcademyCategory::query()->orderBy('name')->get(['id', 'name', 'slug']);

        $articles = AcademyArticle::query()
            ->with('category:id,name,slug')
            ->published()
            ->when($request->filled('category'), function ($query) use ($request) {
                $value = (string) $request->string('category');
                $query->whereHas('category', function ($q) use ($value) {
                    $q->where(function ($inner) use ($value) {
                        $inner->where('slug', $value)->orWhere('id', $value);
                    });
                });
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q').'%';
                $query->where(function ($q) use ($term) {
                    $q->where('title', 'like', $term)
                        ->orWhere('meta_description', 'like', $term)
                        ->orWhere('body', 'like', $term);
                });
            })
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(9)
            ->withQueryString();

        return view('general.academy', compact('articles', 'categories'));
    }

    public function show(string $slug)
    {
        $article = AcademyArticle::query()
            ->with('category:id,name,slug')
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $related = AcademyArticle::query()
            ->with('category:id,name,slug')
            ->published()
            ->where('id', '!=', $article->id)
            ->when($article->academy_category_id, fn ($q) => $q->where('academy_category_id', $article->academy_category_id))
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('general.academy-show', compact('article', 'related'));
    }
}
