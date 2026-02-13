<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Comparison;
use App\Models\Tag;
use App\Models\Tool;

class SeoMeta
{
    /**
     * @return array{title: string, description: string, canonical: string, ogType: string, ogImage: string|null}
     */
    public static function forHomepage(): array
    {
        return [
            'title' => 'Découvrez les meilleurs outils pour développeurs',
            'description' => 'Trouvez, comparez et choisissez les meilleurs outils de développement grâce à nos fiches détaillées et comparatifs générés par IA.',
            'canonical' => route('home'),
            'ogType' => 'website',
            'ogImage' => asset('images/og-default.png'),
        ];
    }

    /**
     * @return array{title: string, description: string, canonical: string, ogType: string, ogImage: string|null}
     */
    public static function forTool(Tool $tool): array
    {
        $title = $tool->meta_title ?: "{$tool->name} — Avis, Prix, Alternatives | Tool";
        $description = $tool->meta_description ?: self::truncate($tool->description ?? "Découvrez {$tool->name}, un outil de développement.");

        return [
            'title' => $title,
            'description' => $description,
            'canonical' => route('tools.show', $tool),
            'ogType' => 'article',
            'ogImage' => $tool->logo_url ?: asset('images/og-default.png'),
        ];
    }

    /**
     * @return array{title: string, description: string, canonical: string, ogType: string, ogImage: string|null}
     */
    public static function forToolsIndex(int $page = 1, int $lastPage = 1): array
    {
        $title = 'Tous les outils';
        if ($page > 1) {
            $title .= " — Page {$page}";
        }

        return [
            'title' => $title,
            'description' => 'Découvrez et comparez les meilleurs outils de développement. Filtrez par catégorie, plateforme et trouvez l\'outil parfait pour vos projets.',
            'canonical' => route('tools.index', $page > 1 ? ['page' => $page] : []),
            'ogType' => 'website',
            'ogImage' => asset('images/og-default.png'),
            ...self::paginationLinks(route('tools.index'), $page, $lastPage),
        ];
    }

    /**
     * @return array{title: string, description: string, canonical: string, ogType: string, ogImage: string|null}
     */
    public static function forCategory(Category $category, int $page = 1, int $lastPage = 1): array
    {
        $title = $category->meta_title ?: "{$category->name} — Meilleurs outils | Tool";
        if ($page > 1) {
            $title .= " — Page {$page}";
        }
        $description = $category->meta_description ?: self::truncate($category->description ?? "Découvrez les meilleurs outils dans la catégorie {$category->name}.");

        return [
            'title' => $title,
            'description' => $description,
            'canonical' => route('categories.show', array_merge([$category], $page > 1 ? ['page' => $page] : [])),
            'ogType' => 'website',
            'ogImage' => asset('images/og-default.png'),
            ...self::paginationLinks(route('categories.show', $category), $page, $lastPage),
        ];
    }

    /**
     * @return array{title: string, description: string, canonical: string, ogType: string, ogImage: string|null}
     */
    public static function forCategoriesIndex(): array
    {
        return [
            'title' => 'Toutes les catégories',
            'description' => 'Explorez les outils de développement par catégorie. Trouvez exactement ce qu\'il vous faut pour vos projets.',
            'canonical' => route('categories.index'),
            'ogType' => 'website',
            'ogImage' => asset('images/og-default.png'),
        ];
    }

    /**
     * @return array{title: string, description: string, canonical: string, ogType: string, ogImage: string|null}
     */
    public static function forComparison(Comparison $comparison): array
    {
        $title = $comparison->meta_title ?: "{$comparison->toolA->name} vs {$comparison->toolB->name} — Comparatif | Tool";
        $description = $comparison->meta_description ?: self::truncate($comparison->verdict ?? "Comparatif détaillé entre {$comparison->toolA->name} et {$comparison->toolB->name}. Découvrez leurs différences, avantages et prix.");

        return [
            'title' => $title,
            'description' => $description,
            'canonical' => route('comparisons.show', $comparison),
            'ogType' => 'article',
            'ogImage' => asset('images/og-default.png'),
        ];
    }

    /**
     * @return array{title: string, description: string, canonical: string, ogType: string, ogImage: string|null}
     */
    public static function forComparisonsIndex(int $page = 1, int $lastPage = 1): array
    {
        $title = 'Tous les comparatifs';
        if ($page > 1) {
            $title .= " — Page {$page}";
        }

        return [
            'title' => $title,
            'description' => 'Comparez les outils de développement côte à côte pour faire le meilleur choix. Comparatifs détaillés générés par IA.',
            'canonical' => route('comparisons.index', $page > 1 ? ['page' => $page] : []),
            'ogType' => 'website',
            'ogImage' => asset('images/og-default.png'),
            ...self::paginationLinks(route('comparisons.index'), $page, $lastPage),
        ];
    }

    /**
     * @return array{title: string, description: string, canonical: string, ogType: string, ogImage: string|null}
     */
    public static function forTag(Tag $tag, int $page = 1, int $lastPage = 1): array
    {
        $title = "Outils {$tag->name} | Tool";
        if ($page > 1) {
            $title .= " — Page {$page}";
        }

        return [
            'title' => $title,
            'description' => "Découvrez les meilleurs outils de développement liés à {$tag->name}. Fiches détaillées et comparatifs.",
            'canonical' => route('tags.show', array_merge([$tag], $page > 1 ? ['page' => $page] : [])),
            'ogType' => 'website',
            'ogImage' => asset('images/og-default.png'),
            ...self::paginationLinks(route('tags.show', $tag), $page, $lastPage),
        ];
    }

    /**
     * @return array{paginationPrev: string|null, paginationNext: string|null}
     */
    private static function paginationLinks(string $baseUrl, int $page, int $lastPage): array
    {
        return [
            'paginationPrev' => $page > 1 ? $baseUrl.($page > 2 ? '?page='.($page - 1) : '') : null,
            'paginationNext' => $page < $lastPage ? $baseUrl.'?page='.($page + 1) : null,
        ];
    }

    private static function truncate(string $text, int $length = 160): string
    {
        $text = strip_tags($text);

        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return mb_substr($text, 0, $length - 3).'...';
    }
}
