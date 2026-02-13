<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tool;
use App\Services\SeoMeta;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicToolController extends Controller
{
    public function index(Request $request): Response
    {
        $tools = Tool::query()
            ->published()
            ->with(['category', 'tags'])
            ->when($request->input('category'), fn ($query, $categorySlug) => $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug)))
            ->when($request->input('platform'), fn ($query, $platform) => $query->whereJsonContains('platforms', $platform))
            ->when($request->input('search'), fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->orderByDesc('is_sponsored')
            ->when($request->input('sort') === 'alpha', fn ($query) => $query->orderBy('name'), fn ($query) => $query->latest('published_at'))
            ->paginate(18)
            ->withQueryString();

        $categories = Category::query()
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug']);

        return Inertia::render('Public/Tools/Index', [
            'seo' => SeoMeta::forToolsIndex($request->integer('page', 1), $tools->lastPage()),
            'tools' => $tools,
            'categories' => $categories,
            'filters' => [
                'search' => $request->input('search', ''),
                'category' => $request->input('category', ''),
                'platform' => $request->input('platform', ''),
                'sort' => $request->input('sort', 'recent'),
            ],
        ]);
    }

    public function show(Tool $tool): Response
    {
        if (! $tool->is_published) {
            abort(404);
        }

        $tool->load([
            'category',
            'tags',
            'alternatives' => fn ($query) => $query->published()->with(['category', 'tags']),
            'comparisonsAsToolA' => fn ($query) => $query->published()->with('toolB'),
            'comparisonsAsToolB' => fn ($query) => $query->published()->with('toolA'),
        ]);

        return Inertia::render('Public/Tools/Show', [
            'seo' => SeoMeta::forTool($tool),
            'tool' => $tool,
        ]);
    }
}
