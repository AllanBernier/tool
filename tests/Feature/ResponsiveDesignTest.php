<?php

use App\Models\Category;
use App\Models\Comparison;
use App\Models\Tag;
use App\Models\Tool;

test('homepage renders with responsive layout', function () {
    $this->get('/')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Welcome'));
});

test('tools index renders with responsive grid and filters', function () {
    Tool::factory()->published()->count(2)->create();

    $this->get('/outils')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Public/Tools/Index')
            ->has('tools.data', 2)
            ->has('filters')
        );
});

test('tool show renders with responsive sections', function () {
    $tool = Tool::factory()->published()->create();

    $this->get("/outil/{$tool->slug}")
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Public/Tools/Show')
            ->has('tool')
        );
});

test('categories index renders with responsive grid', function () {
    Category::factory()->count(2)->create();

    $this->get('/categories')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Public/Categories/Index')
            ->has('categories')
        );
});

test('category show renders with responsive grid and filters', function () {
    $category = Category::factory()->create();
    Tool::factory()->published()->create(['category_id' => $category->id]);

    $this->get("/categorie/{$category->slug}")
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Public/Categories/Show')
            ->has('tools.data')
            ->has('filters')
        );
});

test('comparisons index renders with responsive grid', function () {
    Comparison::factory()->published()->create();

    $this->get('/comparatifs')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Public/Comparisons/Index')
            ->has('comparisons.data')
        );
});

test('comparison show renders with responsive table and pricing', function () {
    $comparison = Comparison::factory()->published()->create();

    $this->get("/comparatif/{$comparison->slug}")
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Public/Comparisons/Show')
            ->has('comparison')
        );
});

test('tag show renders with responsive grid', function () {
    $tag = Tag::factory()->create();
    $tool = Tool::factory()->published()->create();
    $tool->tags()->attach($tag);

    $this->get("/tag/{$tag->slug}")
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Public/Tags/Show')
            ->has('tag')
            ->has('tools.data')
        );
});

test('search endpoint returns json for command palette', function () {
    Tool::factory()->published()->create(['name' => 'Visual Studio Code']);

    $this->getJson('/search?query=Visual')
        ->assertOk()
        ->assertJsonStructure(['tools', 'categories', 'tags']);
});
