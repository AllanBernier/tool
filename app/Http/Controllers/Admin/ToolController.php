<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreToolRequest;
use App\Http\Requests\Admin\UpdateToolRequest;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Tool;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ToolController extends Controller
{
    public function index(): Response
    {
        $query = Tool::query()
            ->with('category')
            ->withCount('tags');

        if ($search = request('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($categoryId = request('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($status = request('generation_status')) {
            $query->where('generation_status', $status);
        }

        return Inertia::render('dashboard/Tools/Index', [
            'tools' => $query->latest()->paginate(25),
            'categories' => Category::query()->orderBy('sort_order')->get(['id', 'name']),
            'filters' => [
                'search' => request('search', ''),
                'category_id' => request('category_id', ''),
                'generation_status' => request('generation_status', ''),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('dashboard/Tools/Create', [
            'categories' => Category::query()->orderBy('sort_order')->get(['id', 'name']),
            'tags' => Tag::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreToolRequest $request): RedirectResponse
    {
        $tool = Tool::create($request->safe()->except('tags'));

        if ($request->validated('tags')) {
            $tool->tags()->sync($request->validated('tags'));
        }

        return to_route('admin.outils.edit', $tool)
            ->with('success', 'Outil créé avec succès.');
    }

    public function show(Tool $tool): Response
    {
        $tool->load(['category', 'tags', 'alternatives', 'comparisonsAsToolA.toolB', 'comparisonsAsToolB.toolA']);

        return Inertia::render('dashboard/Tools/Show', [
            'tool' => $tool,
        ]);
    }

    public function edit(Tool $tool): Response
    {
        $tool->load(['category', 'tags', 'alternatives', 'comparisonsAsToolA.toolB', 'comparisonsAsToolB.toolA']);

        return Inertia::render('dashboard/Tools/Edit', [
            'tool' => $tool,
            'categories' => Category::query()->orderBy('sort_order')->get(['id', 'name']),
            'tags' => Tag::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(UpdateToolRequest $request, Tool $tool): RedirectResponse
    {
        $tool->update($request->safe()->except('tags'));

        if ($request->has('tags')) {
            $tool->tags()->sync($request->validated('tags') ?? []);
        }

        return to_route('admin.outils.edit', $tool)
            ->with('success', 'Outil mis à jour avec succès.');
    }

    public function destroy(Tool $tool): RedirectResponse
    {
        $tool->tags()->detach();
        $tool->alternatives()->detach();
        $tool->delete();

        return to_route('admin.outils.index')
            ->with('success', 'Outil supprimé avec succès.');
    }

    public function togglePublish(Tool $tool): RedirectResponse
    {
        $tool->update([
            'is_published' => ! $tool->is_published,
            'published_at' => ! $tool->is_published ? now() : null,
        ]);

        $message = $tool->is_published
            ? 'Outil publié avec succès.'
            : 'Outil dépublié avec succès.';

        return back()->with('success', $message);
    }
}
