<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTagRequest;
use App\Http\Requests\Admin\UpdateTagRequest;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TagController extends Controller
{
    /**
     * Display a listing of tags.
     */
    public function index(): Response
    {
        return Inertia::render('dashboard/Tags/Index', [
            'tags' => Tag::query()
                ->withCount('tools')
                ->orderBy('name')
                ->paginate(25),
        ]);
    }

    /**
     * Show the form for creating a new tag.
     */
    public function create(): Response
    {
        return Inertia::render('dashboard/Tags/Create');
    }

    /**
     * Store a newly created tag.
     */
    public function store(StoreTagRequest $request): RedirectResponse
    {
        Tag::create($request->validated());

        return to_route('admin.tags.index')
            ->with('success', 'Tag créé avec succès.');
    }

    /**
     * Show the form for editing a tag.
     */
    public function edit(Tag $tag): Response
    {
        return Inertia::render('dashboard/Tags/Edit', [
            'tag' => $tag,
        ]);
    }

    /**
     * Update the specified tag.
     */
    public function update(UpdateTagRequest $request, Tag $tag): RedirectResponse
    {
        $tag->update($request->validated());

        return to_route('admin.tags.index')
            ->with('success', 'Tag mis à jour avec succès.');
    }

    /**
     * Remove the specified tag.
     */
    public function destroy(Tag $tag): RedirectResponse
    {
        if ($tag->tools()->exists()) {
            return back()->with('error', 'Impossible de supprimer un tag lié à des outils.');
        }

        $tag->delete();

        return to_route('admin.tags.index')
            ->with('success', 'Tag supprimé avec succès.');
    }
}
