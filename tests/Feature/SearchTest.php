<?php

use App\Models\Category;
use App\Models\Tag;
use App\Models\Tool;

test('search by tool name returns result', function () {
    Tool::factory()->published()->create(['name' => 'Visual Studio Code']);
    Tool::factory()->published()->create(['name' => 'PhpStorm']);

    $response = $this->getJson('/search?query=Visual');

    $response->assertSuccessful()
        ->assertJsonCount(1, 'tools')
        ->assertJsonPath('tools.0.name', 'Visual Studio Code')
        ->assertJsonPath('tools.0.type', 'tool');
});

test('search by category name returns result', function () {
    Category::factory()->create(['name' => 'Code Editors']);
    Category::factory()->create(['name' => 'Databases']);

    $response = $this->getJson('/search?query=Editors');

    $response->assertSuccessful()
        ->assertJsonCount(1, 'categories')
        ->assertJsonPath('categories.0.name', 'Code Editors')
        ->assertJsonPath('categories.0.type', 'category');
});

test('search by tag name returns result', function () {
    Tag::factory()->create(['name' => 'open-source']);
    Tag::factory()->create(['name' => 'premium']);

    $response = $this->getJson('/search?query=open-source');

    $response->assertSuccessful()
        ->assertJsonCount(1, 'tags')
        ->assertJsonPath('tags.0.name', 'open-source')
        ->assertJsonPath('tags.0.type', 'tag');
});

test('empty search returns empty results', function () {
    Tool::factory()->published()->create();

    $response = $this->getJson('/search?query=');

    $response->assertSuccessful()
        ->assertJsonCount(0, 'tools')
        ->assertJsonCount(0, 'categories')
        ->assertJsonCount(0, 'tags');
});

test('only published tools appear in search results', function () {
    Tool::factory()->published()->create(['name' => 'Published Tool']);
    Tool::factory()->create(['name' => 'Unpublished Tool', 'is_published' => false]);

    $response = $this->getJson('/search?query=Tool');

    $response->assertSuccessful()
        ->assertJsonCount(1, 'tools')
        ->assertJsonPath('tools.0.name', 'Published Tool');
});

test('search results are limited to 5 per type', function () {
    for ($i = 1; $i <= 8; $i++) {
        Tool::factory()->published()->create(['name' => "Searchable Tool {$i}"]);
    }

    $response = $this->getJson('/search?query=Searchable');

    $response->assertSuccessful()
        ->assertJsonCount(5, 'tools');
});

test('search without query parameter returns empty results', function () {
    $response = $this->getJson('/search');

    $response->assertSuccessful()
        ->assertJsonCount(0, 'tools')
        ->assertJsonCount(0, 'categories')
        ->assertJsonCount(0, 'tags');
});

test('search results include url field', function () {
    $tool = Tool::factory()->published()->create(['name' => 'Docker']);

    $response = $this->getJson('/search?query=Docker');

    $response->assertSuccessful()
        ->assertJsonPath('tools.0.url', route('tools.show', $tool));
});
