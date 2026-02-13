<?php

namespace App\Http\Controllers;

use App\Models\Comparison;
use Inertia\Inertia;
use Inertia\Response;

class PublicComparisonController extends Controller
{
    public function index(): Response
    {
        $comparisons = Comparison::query()
            ->published()
            ->with(['toolA', 'toolB'])
            ->latest('published_at')
            ->paginate(18)
            ->withQueryString();

        return Inertia::render('Public/Comparisons/Index', [
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
            'comparison' => $comparison,
        ]);
    }
}
