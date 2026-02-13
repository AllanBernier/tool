<?php

use App\Models\Category;
use App\Models\Comparison;
use App\Models\Tag;
use App\Models\Tool;
use App\Services\SeoMeta;

test('homepage has seo meta prop', function () {
    $response = $this->get('/');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('seo', fn ($prop) => $prop
                ->where('title', 'Découvrez les meilleurs outils pour développeurs')
                ->has('description')
                ->has('canonical')
                ->where('ogType', 'website')
                ->has('ogImage')
            )
        );
});

test('tool show page has seo meta prop with tool name', function () {
    $tool = Tool::factory()->published()->create([
        'name' => 'VS Code',
        'description' => 'A great code editor',
    ]);

    $response = $this->get("/outil/{$tool->slug}");

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('seo', fn ($prop) => $prop
                ->has('title')
                ->has('description')
                ->has('canonical')
                ->where('ogType', 'article')
                ->has('ogImage')
            )
        );
});

test('tool show page uses meta_title when available', function () {
    $tool = Tool::factory()->published()->create([
        'name' => 'VS Code',
        'meta_title' => 'Custom Meta Title',
        'meta_description' => 'Custom meta description',
    ]);

    $response = $this->get("/outil/{$tool->slug}");

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('seo.title', 'Custom Meta Title')
            ->where('seo.description', 'Custom meta description')
        );
});

test('tools index page has seo meta prop', function () {
    $response = $this->get('/outils');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('seo', fn ($prop) => $prop
                ->where('title', 'Tous les outils')
                ->has('description')
                ->has('canonical')
                ->where('ogType', 'website')
                ->has('ogImage')
            )
        );
});

test('tools index page 2 appends page to title', function () {
    Tool::factory()->published()->count(20)->create();

    $response = $this->get('/outils?page=2');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('seo.title', 'Tous les outils — Page 2')
        );
});

test('category show page has seo meta prop', function () {
    $category = Category::factory()->create([
        'name' => 'Frontend',
        'description' => 'Frontend tools',
    ]);

    $response = $this->get("/categorie/{$category->slug}");

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('seo', fn ($prop) => $prop
                ->has('title')
                ->has('description')
                ->has('canonical')
                ->where('ogType', 'website')
                ->has('ogImage')
            )
        );
});

test('category show page uses meta_title when available', function () {
    $category = Category::factory()->create([
        'meta_title' => 'Custom Category Title',
        'meta_description' => 'Custom category description',
    ]);

    $response = $this->get("/categorie/{$category->slug}");

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('seo.title', 'Custom Category Title')
            ->where('seo.description', 'Custom category description')
        );
});

test('categories index page has seo meta prop', function () {
    $response = $this->get('/categories');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('seo', fn ($prop) => $prop
                ->where('title', 'Toutes les catégories')
                ->has('description')
                ->has('canonical')
                ->where('ogType', 'website')
                ->has('ogImage')
            )
        );
});

test('comparison show page has seo meta prop', function () {
    $comparison = Comparison::factory()->published()->create();

    $response = $this->get("/comparatif/{$comparison->slug}");

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('seo', fn ($prop) => $prop
                ->has('title')
                ->has('description')
                ->has('canonical')
                ->where('ogType', 'article')
                ->has('ogImage')
            )
        );
});

test('comparison show page uses meta_title when available', function () {
    $comparison = Comparison::factory()->published()->create([
        'meta_title' => 'Custom Comparison Title',
        'meta_description' => 'Custom comparison description',
    ]);

    $response = $this->get("/comparatif/{$comparison->slug}");

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('seo.title', 'Custom Comparison Title')
            ->where('seo.description', 'Custom comparison description')
        );
});

test('comparisons index page has seo meta prop', function () {
    $response = $this->get('/comparatifs');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('seo', fn ($prop) => $prop
                ->where('title', 'Tous les comparatifs')
                ->has('description')
                ->has('canonical')
                ->where('ogType', 'website')
                ->has('ogImage')
            )
        );
});

test('tag show page has seo meta prop', function () {
    $tag = Tag::factory()->create(['name' => 'PHP']);
    $tool = Tool::factory()->published()->create();
    $tool->tags()->attach($tag);

    $response = $this->get("/tag/{$tag->slug}");

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('seo', fn ($prop) => $prop
                ->where('title', 'Outils PHP | Tool')
                ->has('description')
                ->has('canonical')
                ->where('ogType', 'website')
                ->has('ogImage')
            )
        );
});

test('SeoMeta::forTool generates correct fallback title', function () {
    $tool = Tool::factory()->published()->create([
        'name' => 'VS Code',
        'meta_title' => null,
        'meta_description' => null,
        'description' => 'A lightweight code editor',
    ]);

    $seo = SeoMeta::forTool($tool);

    expect($seo['title'])->toBe('VS Code — Avis, Prix, Alternatives | Tool');
    expect($seo['description'])->toBe('A lightweight code editor');
    expect($seo['ogType'])->toBe('article');
});

test('SeoMeta::forCategory generates correct fallback title', function () {
    $category = Category::factory()->create([
        'name' => 'Frontend',
        'meta_title' => null,
    ]);

    $seo = SeoMeta::forCategory($category);

    expect($seo['title'])->toBe('Frontend — Meilleurs outils | Tool');
});

test('SeoMeta::forComparison generates correct fallback title', function () {
    $comparison = Comparison::factory()->published()->create([
        'meta_title' => null,
    ]);
    $comparison->load(['toolA', 'toolB']);

    $seo = SeoMeta::forComparison($comparison);

    expect($seo['title'])->toContain('vs');
    expect($seo['title'])->toContain('Comparatif | Tool');
});

test('SeoMeta truncates long descriptions', function () {
    $tool = Tool::factory()->published()->create([
        'meta_title' => null,
        'meta_description' => null,
        'description' => str_repeat('A', 200),
    ]);

    $seo = SeoMeta::forTool($tool);

    expect(mb_strlen($seo['description']))->toBeLessThanOrEqual(160);
    expect($seo['description'])->toEndWith('...');
});

test('each public page has unique canonical url', function () {
    $tool = Tool::factory()->published()->create();
    $category = Category::factory()->create();
    $comparison = Comparison::factory()->published()->create();
    $tag = Tag::factory()->create();
    $tool->tags()->attach($tag);

    $canonicals = [];

    $routes = [
        '/',
        '/outils',
        "/outil/{$tool->slug}",
        '/categories',
        "/categorie/{$category->slug}",
        '/comparatifs',
        "/comparatif/{$comparison->slug}",
        "/tag/{$tag->slug}",
    ];

    foreach ($routes as $route) {
        $response = $this->get($route);
        $response->assertOk();
        $seo = $response->original->getData()['page']['props']['seo'];
        $canonicals[] = $seo['canonical'];
    }

    expect(count($canonicals))->toBe(count(array_unique($canonicals)));
});
