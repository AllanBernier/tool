<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comparison;
use App\Models\Tag;
use App\Models\Tool;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $xml = Cache::remember('sitemap', 3600, function () {
            return $this->generateSitemap();
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    private function generateSitemap(): string
    {
        $urls = [];

        $urls[] = $this->url(route('home'), now()->toW3cString(), 'daily', '1.0');

        foreach (Category::query()->orderBy('sort_order')->get() as $category) {
            $urls[] = $this->url(
                route('categories.show', $category),
                $category->updated_at?->toW3cString(),
                'weekly',
                '0.8'
            );
        }

        $urls[] = $this->url(route('categories.index'), now()->toW3cString(), 'weekly', '0.8');

        foreach (Tool::query()->published()->get() as $tool) {
            $urls[] = $this->url(
                route('tools.show', $tool),
                $tool->updated_at?->toW3cString(),
                'weekly',
                '0.7'
            );
        }

        $urls[] = $this->url(route('tools.index'), now()->toW3cString(), 'daily', '0.7');

        foreach (Comparison::query()->published()->get() as $comparison) {
            $urls[] = $this->url(
                route('comparisons.show', $comparison),
                $comparison->updated_at?->toW3cString(),
                'weekly',
                '0.7'
            );
        }

        $urls[] = $this->url(route('comparisons.index'), now()->toW3cString(), 'daily', '0.7');

        $tagsWithTools = Tag::query()
            ->whereHas('tools', fn ($query) => $query->published())
            ->get();

        foreach ($tagsWithTools as $tag) {
            $urls[] = $this->url(
                route('tags.show', $tag),
                $tag->updated_at?->toW3cString(),
                'weekly',
                '0.5'
            );
        }

        $xmlContent = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xmlContent .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        $xmlContent .= implode("\n", $urls);
        $xmlContent .= "\n".'</urlset>';

        return $xmlContent;
    }

    private function url(string $loc, ?string $lastmod, string $changefreq, string $priority): string
    {
        $xml = "  <url>\n";
        $xml .= "    <loc>{$loc}</loc>\n";
        if ($lastmod) {
            $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
        }
        $xml .= "    <changefreq>{$changefreq}</changefreq>\n";
        $xml .= "    <priority>{$priority}</priority>\n";
        $xml .= '  </url>';

        return $xml;
    }
}
