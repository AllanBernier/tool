<?php

use App\Models\Category;
use App\Models\Comparison;
use App\Models\Tag;
use App\Models\Tool;

test('homepage loads successfully', function () {
    $response = $this->get('/');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Welcome')
            ->has('popularTools')
            ->has('trendingComparisons')
            ->has('categories')
            ->has('popularTags')
        );
});

test('homepage displays published tools only', function () {
    $category = Category::factory()->create();
    Tool::factory()->published()->create(['name' => 'Published Tool', 'category_id' => $category->id]);
    Tool::factory()->create(['name' => 'Draft Tool', 'category_id' => $category->id]);

    $response = $this->get('/');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('popularTools', 1)
            ->where('popularTools.0.name', 'Published Tool')
        );
});

test('homepage displays published comparisons only', function () {
    Comparison::factory()->published()->create();
    Comparison::factory()->create();

    $response = $this->get('/');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('trendingComparisons', 1)
        );
});

test('homepage displays categories with tool count', function () {
    $category = Category::factory()->create(['name' => 'Frontend']);
    Tool::factory()->published()->count(3)->create(['category_id' => $category->id]);
    Tool::factory()->create(['category_id' => $category->id]);

    $response = $this->get('/');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('categories', 1)
            ->where('categories.0.name', 'Frontend')
            ->where('categories.0.tools_count', 3)
        );
});

test('homepage eager loads tool relations', function () {
    $category = Category::factory()->create();
    $tag = Tag::factory()->create();
    $tool = Tool::factory()->published()->create(['category_id' => $category->id]);
    $tool->tags()->attach($tag);

    $response = $this->get('/');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('popularTools', 1)
            ->has('popularTools.0.category')
            ->has('popularTools.0.tags', 1)
        );
});

test('homepage eager loads comparison tool relations', function () {
    Comparison::factory()->published()->create();

    $response = $this->get('/');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('trendingComparisons', 1)
            ->has('trendingComparisons.0.tool_a')
            ->has('trendingComparisons.0.tool_b')
        );
});

test('homepage limits popular tools to 12', function () {
    $category = Category::factory()->create();
    Tool::factory()->published()->count(15)->create(['category_id' => $category->id]);

    $response = $this->get('/');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('popularTools', 12)
        );
});

test('homepage limits trending comparisons to 6', function () {
    Comparison::factory()->published()->count(8)->create();

    $response = $this->get('/');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('trendingComparisons', 6)
        );
});

test('homepage only shows tags used by published tools', function () {
    $usedTag = Tag::factory()->create(['name' => 'Used Tag']);
    Tag::factory()->create(['name' => 'Unused Tag']);
    $tool = Tool::factory()->published()->create();
    $tool->tags()->attach($usedTag);

    $response = $this->get('/');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('popularTags', 1)
            ->where('popularTags.0.name', 'Used Tag')
        );
});
