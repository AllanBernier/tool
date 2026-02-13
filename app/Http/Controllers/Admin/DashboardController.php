<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comparison;
use App\Models\Tag;
use App\Models\Tool;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Dashboard', [
            'stats' => [
                'tools' => Tool::query()->count(),
                'tools_published' => Tool::query()->published()->count(),
                'comparisons' => Comparison::query()->count(),
                'categories' => Category::query()->count(),
                'tags' => Tag::query()->count(),
            ],
            'latestTools' => Tool::query()
                ->with('category')
                ->latest()
                ->limit(5)
                ->get(['id', 'name', 'slug', 'logo_path', 'is_published', 'generation_status', 'category_id', 'created_at']),
            'latestComparisons' => Comparison::query()
                ->with(['toolA:id,name,slug', 'toolB:id,name,slug'])
                ->latest()
                ->limit(5)
                ->get(['id', 'slug', 'generation_status', 'is_published', 'tool_a_id', 'tool_b_id', 'created_at']),
        ]);
    }
}
