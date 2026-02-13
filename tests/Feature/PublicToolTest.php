<?php

use App\Models\Category;
use App\Models\Tool;

test('show displays a published tool', function () {
    $tool = Tool::factory()->published()->create();

    $response = $this->get("/outil/{$tool->slug}");

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Public/Tools/Show')
            ->has('tool', fn ($prop) => $prop
                ->where('slug', $tool->slug)
                ->where('name', $tool->name)
                ->etc()
            )
        );
});

test('show returns 404 for unpublished tool', function () {
    $tool = Tool::factory()->create(['is_published' => false]);

    $response = $this->get("/outil/{$tool->slug}");

    $response->assertNotFound();
});

test('show returns 404 for non-existent slug', function () {
    $response = $this->get('/outil/non-existent-tool');

    $response->assertNotFound();
});

test('index lists paginated published tools', function () {
    Tool::factory()->published()->count(3)->create();
    Tool::factory()->create(['is_published' => false]);

    $response = $this->get('/outils');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Public/Tools/Index')
            ->has('tools.data', 3)
            ->has('categories')
            ->has('filters')
        );
});

test('index filters by category', function () {
    $category = Category::factory()->create();
    $otherCategory = Category::factory()->create();

    Tool::factory()->published()->create(['category_id' => $category->id]);
    Tool::factory()->published()->create(['category_id' => $category->id]);
    Tool::factory()->published()->create(['category_id' => $otherCategory->id]);

    $response = $this->get("/outils?category={$category->slug}");

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Public/Tools/Index')
            ->has('tools.data', 2)
        );
});

test('index searches by name', function () {
    Tool::factory()->published()->create(['name' => 'Visual Studio Code']);
    Tool::factory()->published()->create(['name' => 'PhpStorm']);

    $response = $this->get('/outils?search=Visual');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Public/Tools/Index')
            ->has('tools.data', 1)
            ->where('tools.data.0.name', 'Visual Studio Code')
        );
});
