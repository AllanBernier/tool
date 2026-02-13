<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreComparisonRequest;
use App\Http\Requests\Admin\UpdateComparisonRequest;
use App\Models\Comparison;
use App\Models\Tool;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ComparisonController extends Controller
{
    public function index(): Response
    {
        $query = Comparison::query()
            ->with(['toolA', 'toolB']);

        if ($status = request('generation_status')) {
            $query->where('generation_status', $status);
        }

        return Inertia::render('dashboard/Comparisons/Index', [
            'comparisons' => $query->latest()->paginate(25),
            'filters' => [
                'generation_status' => request('generation_status', ''),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('dashboard/Comparisons/Create', [
            'tools' => Tool::query()->orderBy('name')->get(['id', 'name', 'slug']),
        ]);
    }

    public function store(StoreComparisonRequest $request): RedirectResponse
    {
        $comparison = Comparison::create($request->validated());

        return to_route('admin.comparatifs.edit', $comparison)
            ->with('success', 'Comparaison créée avec succès.');
    }

    public function edit(Comparison $comparison): Response
    {
        $comparison->load(['toolA', 'toolB']);

        return Inertia::render('dashboard/Comparisons/Edit', [
            'comparison' => $comparison,
        ]);
    }

    public function update(UpdateComparisonRequest $request, Comparison $comparison): RedirectResponse
    {
        $comparison->update($request->validated());

        return to_route('admin.comparatifs.edit', $comparison)
            ->with('success', 'Comparaison mise à jour avec succès.');
    }

    public function destroy(Comparison $comparison): RedirectResponse
    {
        $comparison->delete();

        return to_route('admin.comparatifs.index')
            ->with('success', 'Comparaison supprimée avec succès.');
    }

    public function togglePublish(Comparison $comparison): RedirectResponse
    {
        $comparison->update([
            'is_published' => ! $comparison->is_published,
            'published_at' => ! $comparison->is_published ? now() : null,
        ]);

        $message = $comparison->is_published
            ? 'Comparaison publiée avec succès.'
            : 'Comparaison dépubliée avec succès.';

        return back()->with('success', $message);
    }
}
