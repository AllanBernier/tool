<?php

use App\Models\Category;
use App\Models\Comparison;
use App\Models\Tag;
use App\Models\Tool;

test('homepage renders successfully with seo data for json-ld', function () {
    $response = $this->get('/');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Welcome')
            ->has('seo.canonical')
            ->has('seo.ogImage')
        );
});

test('tool show page renders with seo data for json-ld schemas', function () {
    $tool = Tool::factory()->published()->create([
        'name' => 'VS Code',
        'description' => 'A code editor',
        'platforms' => ['web', 'desktop'],
        'faq' => [
            ['question' => 'Is it free?', 'answer' => 'Yes, it is free.'],
        ],
    ]);

    $response = $this->get("/outil/{$tool->slug}");

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Public/Tools/Show')
            ->has('seo.canonical')
            ->where('tool.name', 'VS Code')
            ->has('tool.platforms')
            ->has('tool.faq')
        );
});

test('tool show page includes category for breadcrumbs', function () {
    $category = Category::factory()->create(['name' => 'IDE']);
    $tool = Tool::factory()->published()->create([
        'category_id' => $category->id,
    ]);

    $response = $this->get("/outil/{$tool->slug}");

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('tool.category', fn ($prop) => $prop
                ->where('name', 'IDE')
                ->has('slug')
                ->etc()
            )
        );
});

test('category show page renders with category data for json-ld', function () {
    $category = Category::factory()->create([
        'name' => 'Frontend',
        'description' => 'Frontend tools',
    ]);

    $response = $this->get("/categorie/{$category->slug}");

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Public/Categories/Show')
            ->has('seo.canonical')
            ->where('category.name', 'Frontend')
            ->where('category.description', 'Frontend tools')
        );
});

test('comparison show page renders with dates for article json-ld', function () {
    $comparison = Comparison::factory()->published()->create();

    $response = $this->get("/comparatif/{$comparison->slug}");

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Public/Comparisons/Show')
            ->has('seo.canonical')
            ->has('comparison.published_at')
            ->has('comparison.updated_at')
            ->has('comparison.tool_a.name')
            ->has('comparison.tool_b.name')
        );
});

test('tag show page renders with tag data for json-ld', function () {
    $tag = Tag::factory()->create(['name' => 'PHP']);
    $tool = Tool::factory()->published()->create();
    $tool->tags()->attach($tag);

    $response = $this->get("/tag/{$tag->slug}");

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Public/Tags/Show')
            ->has('seo.canonical')
            ->where('tag.name', 'PHP')
            ->has('tools.total')
        );
});

test('all detail pages load without errors', function () {
    $tool = Tool::factory()->published()->create();
    $category = Category::factory()->create();
    $comparison = Comparison::factory()->published()->create();
    $tag = Tag::factory()->create();
    $tool->tags()->attach($tag);

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
        $this->get($route)->assertOk();
    }
});
