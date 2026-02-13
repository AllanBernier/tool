<?php

use App\Models\Category;
use App\Models\Tool;
use App\Models\User;

test('index returns category list for authenticated user', function () {
    $user = User::factory()->create();
    Category::factory()->count(3)->create();

    $response = $this->actingAs($user)->get(route('admin.categories.index'));

    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard/Categories/Index')
            ->has('categories', 3)
        );
});

test('index redirects to login if unauthenticated', function () {
    $response = $this->get(route('admin.categories.index'));

    $response->assertRedirect(route('login'));
});

test('store creates a category with valid data', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('admin.categories.store'), [
        'name' => 'IDE & Éditeurs',
        'description' => 'Les meilleurs IDE et éditeurs de code',
        'icon' => 'code',
    ]);

    $response->assertRedirect(route('admin.categories.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('categories', [
        'name' => 'IDE & Éditeurs',
        'icon' => 'code',
    ]);
});

test('store fails with invalid data', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('admin.categories.store'), [
        'name' => '',
        'description' => '',
        'icon' => '',
    ]);

    $response->assertSessionHasErrors(['name', 'description', 'icon']);
});

test('store fails with duplicate name', function () {
    $user = User::factory()->create();
    Category::factory()->create(['name' => 'Existing Category']);

    $response = $this->actingAs($user)->post(route('admin.categories.store'), [
        'name' => 'Existing Category',
        'description' => 'Some description',
        'icon' => 'folder',
    ]);

    $response->assertSessionHasErrors(['name']);
});

test('create page renders for authenticated user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.categories.create'));

    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page->component('dashboard/Categories/Create'));
});

test('edit page renders with category data', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.categories.edit', $category));

    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard/Categories/Edit')
            ->has('category')
            ->where('category.id', $category->id)
        );
});

test('update updates an existing category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();

    $response = $this->actingAs($user)->put(route('admin.categories.update', $category), [
        'name' => 'Updated Name',
        'description' => 'Updated description',
        'icon' => 'wrench',
    ]);

    $response->assertRedirect(route('admin.categories.index'));
    $response->assertSessionHas('success');

    $category->refresh();
    expect($category->name)->toBe('Updated Name')
        ->and($category->icon)->toBe('wrench');
});

test('update allows same name for same category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['name' => 'My Category']);

    $response = $this->actingAs($user)->put(route('admin.categories.update', $category), [
        'name' => 'My Category',
        'description' => 'Updated description',
        'icon' => 'wrench',
    ]);

    $response->assertRedirect(route('admin.categories.index'));
    $response->assertSessionHasNoErrors();
});

test('destroy deletes a category with no linked tools', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();

    $response = $this->actingAs($user)->delete(route('admin.categories.destroy', $category));

    $response->assertRedirect(route('admin.categories.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

test('destroy fails if category has linked tools', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    Tool::factory()->create(['category_id' => $category->id]);

    $response = $this->actingAs($user)->delete(route('admin.categories.destroy', $category));

    $response->assertRedirect();
    $response->assertSessionHas('error');

    $this->assertDatabaseHas('categories', ['id' => $category->id]);
});

test('reorder updates category order', function () {
    $user = User::factory()->create();
    $categories = Category::factory()->count(3)->create();

    $reversedIds = $categories->reverse()->pluck('id')->values()->toArray();

    $response = $this->actingAs($user)->post(route('admin.categories.reorder'), [
        'ids' => $reversedIds,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    foreach ($reversedIds as $index => $id) {
        $this->assertDatabaseHas('categories', [
            'id' => $id,
            'sort_order' => $index,
        ]);
    }
});

test('index includes tools count', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    Tool::factory()->count(3)->create(['category_id' => $category->id]);

    $response = $this->actingAs($user)->get(route('admin.categories.index'));

    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard/Categories/Index')
            ->where('categories.0.tools_count', 3)
        );
});
