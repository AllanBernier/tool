<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tag;
use App\Models\Tool;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $query = trim((string) $request->input('query'));

        if ($query === '') {
            return response()->json([
                'tools' => [],
                'categories' => [],
                'tags' => [],
            ]);
        }

        $tools = Tool::query()
            ->published()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get(['id', 'name', 'slug', 'description'])
            ->map(fn (Tool $tool) => [
                'type' => 'tool',
                'name' => $tool->name,
                'slug' => $tool->slug,
                'url' => route('tools.show', $tool),
                'description' => $tool->description,
            ]);

        $categories = Category::query()
            ->where('name', 'like', "%{$query}%")
            ->limit(5)
            ->get(['id', 'name', 'slug', 'description'])
            ->map(fn (Category $category) => [
                'type' => 'category',
                'name' => $category->name,
                'slug' => $category->slug,
                'url' => route('categories.show', $category),
                'description' => $category->description,
            ]);

        $tags = Tag::query()
            ->where('name', 'like', "%{$query}%")
            ->limit(5)
            ->get(['id', 'name', 'slug'])
            ->map(fn (Tag $tag) => [
                'type' => 'tag',
                'name' => $tag->name,
                'slug' => $tag->slug,
                'url' => route('tags.show', $tag),
                'description' => null,
            ]);

        return response()->json([
            'tools' => $tools->values(),
            'categories' => $categories->values(),
            'tags' => $tags->values(),
        ]);
    }
}
