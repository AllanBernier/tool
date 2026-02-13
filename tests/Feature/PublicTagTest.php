<?php

use App\Models\Category;
use App\Models\Tag;
use App\Models\Tool;

test('show displays tag with its published tools', function () {
    $tag = Tag::factory()->create();
    $category = Category::factory()->create();

    $publishedTools = Tool::factory()->published()->count(2)->create(['category_id' => $category->id]);
    foreach ($publishedTools as $tool) {
        $tool->tags()->attach($tag);
    }

    $unpublishedTool = Tool::factory()->create(['category_id' => $category->id, 'is_published' => false]);
    $unpublishedTool->tags()->attach($tag);

    $response = $this->get("/tag/{$tag->slug}");

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Public/Tags/Show')
            ->has('tag', fn ($prop) => $prop
                ->where('slug', $tag->slug)
                ->where('name', $tag->name)
                ->etc()
            )
            ->has('tools.data', 2)
        );
});

test('show returns 404 for non-existent slug', function () {
    $response = $this->get('/tag/non-existent-tag');

    $response->assertNotFound();
});
