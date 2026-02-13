<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comparison;
use App\Models\Tag;
use App\Models\Tool;
use App\Services\SeoMeta;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Welcome', [
            'seo' => SeoMeta::forHomepage(),
            'popularTools' => Tool::query()
                ->published()
                ->with(['category', 'tags'])
                ->latest('published_at')
                ->limit(12)
                ->get(),
            'trendingComparisons' => Comparison::query()
                ->published()
                ->with(['toolA', 'toolB'])
                ->latest('published_at')
                ->limit(6)
                ->get(),
            'categories' => Category::query()
                ->withCount(['tools' => fn ($query) => $query->published()])
                ->orderBy('sort_order')
                ->get(),
            'popularTags' => Tag::query()
                ->whereHas('tools', fn ($query) => $query->published())
                ->withCount(['tools' => fn ($query) => $query->published()])
                ->orderByDesc('tools_count')
                ->limit(20)
                ->get(),
        ]);
    }
}
