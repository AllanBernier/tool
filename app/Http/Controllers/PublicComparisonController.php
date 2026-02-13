<?php

namespace App\Http\Controllers;

use App\Models\Comparison;
use App\Services\SeoMeta;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicComparisonController extends Controller
{
    public function index(Request $request): Response
    {
        $comparisons = Comparison::query()
            ->published()
            ->with(['toolA', 'toolB'])
            ->latest('published_at')
            ->paginate(18)
            ->withQueryString();

        return Inertia::render('Public/Comparisons/Index', [
            'seo' => SeoMeta::forComparisonsIndex($request->integer('page', 1), $comparisons->lastPage()),
            'comparisons' => $comparisons,
        ]);
    }

    public function show(Comparison $comparison): Response
    {
        if (! $comparison->is_published) {
            abort(404);
        }

        $comparison->load([
            'toolA' => fn ($query) => $query->with(['category', 'tags']),
            'toolB' => fn ($query) => $query->with(['category', 'tags']),
        ]);

        return Inertia::render('Public/Comparisons/Show', [
            'seo' => SeoMeta::forComparison($comparison),
            'comparison' => $comparison,
        ]);
    }
}
