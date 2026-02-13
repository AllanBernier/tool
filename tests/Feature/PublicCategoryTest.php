<?php

use App\Models\Category;
use App\Models\Tag;
use App\Models\Tool;

test('index displays categories ordered by sort_order with published tool count', function () {
    $categoryB = Category::factory()->create(['name' => 'B Category', 'sort_order' => 2]);
    $categoryA = Category::factory()->create(['name' => 'A Category', 'sort_order' => 1]);

    Tool::factory()->published()->count(3)->create(['category_id' => $categoryA->id]);
    Tool::factory()->published()->count(1)->create(['category_id' => $categoryB->id]);
    Tool::factory()->create(['category_id' => $categoryA->id, 'is_published' => false]);

    $response = $this->get('/categories');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Public/Categories/Index')
            ->has('categories', 2)
            ->where('categories.0.name', 'A Category')
            ->where('categories.0.tools_count', 3)
            ->where('categories.1.name', 'B Category')
            ->where('categories.1.tools_count', 1)
        );
});

test('show displays category with published tools', function () {
    $category = Category::factory()->create();
    Tool::factory()->published()->count(2)->create(['category_id' => $category->id]);

    $response = $this->get("/categorie/{$category->slug}");

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Public/Categories/Show')
            ->has('category', fn ($prop) => $prop
                ->where('slug', $category->slug)
                ->where('name', $category->name)
                ->etc()
            )
            ->has('tools.data', 2)
            ->has('tags')
            ->has('filters')
        );
});

test('show does not display unpublished tools', function () {
    $category = Category::factory()->create();
    Tool::factory()->published()->create(['category_id' => $category->id]);
    Tool::factory()->create(['category_id' => $category->id, 'is_published' => false]);

    $response = $this->get("/categorie/{$category->slug}");

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Public/Categories/Show')
            ->has('tools.data', 1)
        );
});

test('show filters by platform', function () {
    $category = Category::factory()->create();
    Tool::factory()->published()->create(['category_id' => $category->id, 'platforms' => ['web', 'desktop']]);
    Tool::factory()->published()->create(['category_id' => $category->id, 'platforms' => ['mobile']]);

    $response = $this->get("/categorie/{$category->slug}?platform=web");

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('tools.data', 1)
        );
});

test('show filters by tag', function () {
    $category = Category::factory()->create();
    $tag = Tag::factory()->create();

    $toolWithTag = Tool::factory()->published()->create(['category_id' => $category->id]);
    $toolWithTag->tags()->attach($tag);

    Tool::factory()->published()->create(['category_id' => $category->id]);

    $response = $this->get("/categorie/{$category->slug}?tag={$tag->slug}");

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('tools.data', 1)
        );
});

test('show returns 404 for non-existent category', function () {
    $response = $this->get('/categorie/non-existent-category');

    $response->assertNotFound();
});
