<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tag;
use App\Services\SeoMeta;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicCategoryController extends Controller
{
    public function index(): Response
    {
        $categories = Category::query()
            ->withCount(['tools' => fn ($query) => $query->published()])
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Public/Categories/Index', [
            'seo' => SeoMeta::forCategoriesIndex(),
            'categories' => $categories,
        ]);
    }

    public function show(Request $request, Category $category): Response
    {
        $tools = $category->tools()
            ->published()
            ->with(['category', 'tags'])
            ->when($request->input('platform'), fn ($query, $platform) => $query->whereJsonContains('platforms', $platform))
            ->when($request->input('tag'), fn ($query, $tagSlug) => $query->whereHas('tags', fn ($q) => $q->where('slug', $tagSlug)))
            ->when($request->input('sort') === 'alpha', fn ($query) => $query->orderBy('name'), fn ($query) => $query->latest('published_at'))
            ->paginate(18)
            ->withQueryString();

        $tags = Tag::query()
            ->whereHas('tools', fn ($query) => $query->published()->where('category_id', $category->id))
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return Inertia::render('Public/Categories/Show', [
            'seo' => SeoMeta::forCategory($category, $request->integer('page', 1), $tools->lastPage()),
            'category' => $category,
            'tools' => $tools,
            'tags' => $tags,
            'filters' => [
                'platform' => $request->input('platform', ''),
                'tag' => $request->input('tag', ''),
                'sort' => $request->input('sort', 'recent'),
            ],
        ]);
    }
}
