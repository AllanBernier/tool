<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Services\SeoMeta;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicTagController extends Controller
{
    public function show(Request $request, Tag $tag): Response
    {
        $tools = $tag->tools()
            ->published()
            ->with(['category', 'tags'])
            ->latest('published_at')
            ->paginate(18)
            ->withQueryString();

        return Inertia::render('Public/Tags/Show', [
            'seo' => SeoMeta::forTag($tag, $request->integer('page', 1)),
            'tag' => $tag,
            'tools' => $tools,
        ]);
    }
}
