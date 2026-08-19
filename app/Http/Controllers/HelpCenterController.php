<?php

namespace App\Http\Controllers;

use App\Models\HelpArticle;
use App\Models\HelpCategory;
use Illuminate\Http\Request;

class HelpCenterController extends Controller
{
    public function index(Request $request)
    {
        $categories = HelpCategory::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->with(['articles' => function ($query) use ($request) {
                $query->published()
                    ->when($request->filled('q'), function ($q) use ($request) {
                        $term = '%'.$request->string('q').'%';
                        $q->where(function ($inner) use ($term) {
                            $inner->where('title', 'like', $term)
                                ->orWhere('body', 'like', $term)
                                ->orWhere('meta_description', 'like', $term);
                        });
                    })
                    ->orderBy('sort_order')
                    ->orderBy('title');
            }])
            ->when($request->filled('category'), function ($query) use ($request) {
                $value = (string) $request->string('category');
                $query->where(function ($q) use ($value) {
                    $q->where('slug', $value)->orWhere('id', $value);
                });
            })
            ->get();

        if ($request->filled('q')) {
            $categories = $categories->filter(fn (HelpCategory $category) => $category->articles->isNotEmpty())->values();
        }

        $schemaEntities = [];
        foreach ($categories as $category) {
            foreach ($category->articles as $article) {
                $schemaEntities[] = [
                    '@type' => 'Question',
                    'name' => $article->title,
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => strip_tags((string) $article->body),
                    ],
                ];
            }
        }

        $allCategories = HelpCategory::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return view('general.help', compact('categories', 'allCategories', 'schemaEntities'));
    }

    public function show(string $slug)
    {
        $article = HelpArticle::query()
            ->with('category:id,name,slug')
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $related = HelpArticle::query()
            ->with('category:id,name,slug')
            ->published()
            ->where('id', '!=', $article->id)
            ->when($article->help_category_id, fn ($q) => $q->where('help_category_id', $article->help_category_id))
            ->orderBy('sort_order')
            ->limit(5)
            ->get();

        return view('general.help-show', compact('article', 'related'));
    }
}
