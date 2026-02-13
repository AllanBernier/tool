<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReorderCategoriesRequest;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(): Response
    {
        return Inertia::render('dashboard/Categories/Index', [
            'categories' => Category::query()
                ->withCount('tools')
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): Response
    {
        return Inertia::render('dashboard/Categories/Create');
    }

    /**
     * Store a newly created category.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        Category::create($request->validated());

        return to_route('admin.categories.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

    /**
     * Show the form for editing a category.
     */
    public function edit(Category $category): Response
    {
        return Inertia::render('dashboard/Categories/Edit', [
            'category' => $category,
        ]);
    }

    /**
     * Update the specified category.
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return to_route('admin.categories.index')
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->tools()->exists()) {
            return back()->with('error', 'Impossible de supprimer une catégorie contenant des outils.');
        }

        $category->delete();

        return to_route('admin.categories.index')
            ->with('success', 'Catégorie supprimée avec succès.');
    }

    /**
     * Reorder categories.
     */
    public function reorder(ReorderCategoriesRequest $request): RedirectResponse
    {
        foreach ($request->validated('ids') as $index => $id) {
            Category::where('id', $id)->update(['sort_order' => $index]);
        }

        return back()->with('success', 'Ordre mis à jour.');
    }
}
