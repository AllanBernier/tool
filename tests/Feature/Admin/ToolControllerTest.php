<?php

use App\Enums\GenerationStatus;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Tool;
use App\Models\User;

test('index returns paginated tool list for authenticated user', function () {
    $user = User::factory()->create();
    Tool::factory()->count(3)->create();

    $response = $this->actingAs($user)->get(route('admin.outils.index'));

    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard/Tools/Index')
            ->has('tools.data', 3)
        );
});

test('index filters by category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    Tool::factory()->count(2)->create(['category_id' => $category->id]);
    Tool::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.outils.index', ['category_id' => $category->id]));

    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard/Tools/Index')
            ->has('tools.data', 2)
        );
});

test('index filters by generation status', function () {
    $user = User::factory()->create();
    Tool::factory()->create(['generation_status' => GenerationStatus::Completed]);
    Tool::factory()->create(['generation_status' => GenerationStatus::Pending]);

    $response = $this->actingAs($user)->get(route('admin.outils.index', ['generation_status' => 'completed']));

    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard/Tools/Index')
            ->has('tools.data', 1)
        );
});

test('index searches by name', function () {
    $user = User::factory()->create();
    Tool::factory()->create(['name' => 'Laravel Debugbar']);
    Tool::factory()->create(['name' => 'Telescope']);

    $response = $this->actingAs($user)->get(route('admin.outils.index', ['search' => 'Debugbar']));

    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard/Tools/Index')
            ->has('tools.data', 1)
            ->where('tools.data.0.name', 'Laravel Debugbar')
        );
});

test('index redirects to login if unauthenticated', function () {
    $response = $this->get(route('admin.outils.index'));

    $response->assertRedirect(route('login'));
});

test('store creates tool with valid data and syncs tags', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $tags = Tag::factory()->count(2)->create();

    $response = $this->actingAs($user)->post(route('admin.outils.store'), [
        'name' => 'My New Tool',
        'url' => 'https://example.com',
        'category_id' => $category->id,
        'tags' => $tags->pluck('id')->toArray(),
    ]);

    $tool = Tool::where('name', 'My New Tool')->first();

    $response->assertRedirect(route('admin.outils.edit', $tool));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('tools', [
        'name' => 'My New Tool',
        'url' => 'https://example.com',
        'category_id' => $category->id,
    ]);

    expect($tool->tags)->toHaveCount(2);
});

test('store fails with duplicate name', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    Tool::factory()->create(['name' => 'Existing Tool']);

    $response = $this->actingAs($user)->post(route('admin.outils.store'), [
        'name' => 'Existing Tool',
        'url' => 'https://example.com',
        'category_id' => $category->id,
    ]);

    $response->assertSessionHasErrors(['name']);
});

test('store fails with invalid url', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();

    $response = $this->actingAs($user)->post(route('admin.outils.store'), [
        'name' => 'My Tool',
        'url' => 'not-a-url',
        'category_id' => $category->id,
    ]);

    $response->assertSessionHasErrors(['url']);
});

test('store fails with non-existent category', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('admin.outils.store'), [
        'name' => 'My Tool',
        'url' => 'https://example.com',
        'category_id' => 'non-existent-id',
    ]);

    $response->assertSessionHasErrors(['category_id']);
});

test('show returns tool with all relations', function () {
    $user = User::factory()->create();
    $tool = Tool::factory()->create();
    $tags = Tag::factory()->count(2)->create();
    $tool->tags()->attach($tags);

    $response = $this->actingAs($user)->get(route('admin.outils.show', $tool));

    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard/Tools/Show')
            ->has('tool')
            ->where('tool.id', $tool->id)
        );
});

test('create page renders with categories and tags', function () {
    $user = User::factory()->create();
    Category::factory()->count(2)->create();
    Tag::factory()->count(3)->create();

    $response = $this->actingAs($user)->get(route('admin.outils.create'));

    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard/Tools/Create')
            ->has('categories', 2)
            ->has('tags', 3)
        );
});

test('edit page renders with tool data', function () {
    $user = User::factory()->create();
    $tool = Tool::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.outils.edit', $tool));

    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard/Tools/Edit')
            ->has('tool')
            ->where('tool.id', $tool->id)
            ->has('categories')
            ->has('tags')
        );
});

test('update updates tool and syncs tags', function () {
    $user = User::factory()->create();
    $tool = Tool::factory()->create();
    $category = Category::factory()->create();
    $tags = Tag::factory()->count(2)->create();

    $response = $this->actingAs($user)->put(route('admin.outils.update', $tool), [
        'name' => 'Updated Tool Name',
        'url' => 'https://updated.com',
        'category_id' => $category->id,
        'tags' => $tags->pluck('id')->toArray(),
        'description' => 'A short description',
        'features' => ['Feature 1', 'Feature 2'],
        'pros' => ['Pro 1'],
        'cons' => ['Con 1'],
    ]);

    $response->assertRedirect(route('admin.outils.edit', $tool->fresh()));
    $response->assertSessionHas('success');

    $tool->refresh();
    expect($tool->name)->toBe('Updated Tool Name');
    expect($tool->url)->toBe('https://updated.com');
    expect($tool->category_id)->toBe($category->id);
    expect($tool->description)->toBe('A short description');
    expect($tool->features)->toBe(['Feature 1', 'Feature 2']);
    expect($tool->tags)->toHaveCount(2);
});

test('update allows same name for same tool', function () {
    $user = User::factory()->create();
    $tool = Tool::factory()->create(['name' => 'My Tool']);

    $response = $this->actingAs($user)->put(route('admin.outils.update', $tool), [
        'name' => 'My Tool',
        'url' => $tool->url,
        'category_id' => $tool->category_id,
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();
});

test('destroy deletes tool and its pivots', function () {
    $user = User::factory()->create();
    $tool = Tool::factory()->create();
    $tags = Tag::factory()->count(2)->create();
    $tool->tags()->attach($tags);

    $response = $this->actingAs($user)->delete(route('admin.outils.destroy', $tool));

    $response->assertRedirect(route('admin.outils.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('tools', ['id' => $tool->id]);
    $this->assertDatabaseMissing('tool_tag', ['tool_id' => $tool->id]);
});

test('togglePublish toggles publication status', function () {
    $user = User::factory()->create();
    $tool = Tool::factory()->create(['is_published' => false, 'published_at' => null]);

    $response = $this->actingAs($user)->post(route('admin.tools.toggle-publish', $tool));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $tool->refresh();
    expect($tool->is_published)->toBeTrue();
    expect($tool->published_at)->not->toBeNull();

    $this->actingAs($user)->post(route('admin.tools.toggle-publish', $tool));

    $tool->refresh();
    expect($tool->is_published)->toBeFalse();
    expect($tool->published_at)->toBeNull();
});

test('unauthenticated access to store is redirected', function () {
    $category = Category::factory()->create();

    $response = $this->post(route('admin.outils.store'), [
        'name' => 'Tool',
        'url' => 'https://example.com',
        'category_id' => $category->id,
    ]);

    $response->assertRedirect(route('login'));
});
