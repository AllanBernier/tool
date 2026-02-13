<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Inertia\Inertia;
use Inertia\Response;

class PublicTagController extends Controller
{
    public function show(Tag $tag): Response
    {
        $tools = $tag->tools()
            ->published()
            ->with(['category', 'tags'])
            ->latest('published_at')
            ->paginate(18)
            ->withQueryString();

        return Inertia::render('Public/Tags/Show', [
            'tag' => $tag,
            'tools' => $tools,
        ]);
    }
}
