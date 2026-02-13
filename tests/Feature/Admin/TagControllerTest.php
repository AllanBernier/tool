<?php

use App\Models\Tag;
use App\Models\Tool;
use App\Models\User;

test('index returns paginated tag list for authenticated user', function () {
    $user = User::factory()->create();
    Tag::factory()->count(3)->create();

    $response = $this->actingAs($user)->get(route('admin.tags.index'));

    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard/Tags/Index')
            ->has('tags.data', 3)
        );
});

test('index redirects to login if unauthenticated', function () {
    $response = $this->get(route('admin.tags.index'));

    $response->assertRedirect(route('login'));
});

test('store creates a tag with valid data', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('admin.tags.store'), [
        'name' => 'Open Source',
    ]);

    $response->assertRedirect(route('admin.tags.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('tags', [
        'name' => 'Open Source',
    ]);
});

test('store fails with invalid data', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('admin.tags.store'), [
        'name' => '',
    ]);

    $response->assertSessionHasErrors(['name']);
});

test('store fails with duplicate name', function () {
    $user = User::factory()->create();
    Tag::factory()->create(['name' => 'Existing Tag']);

    $response = $this->actingAs($user)->post(route('admin.tags.store'), [
        'name' => 'Existing Tag',
    ]);

    $response->assertSessionHasErrors(['name']);
});

test('store fails with name exceeding 100 characters', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('admin.tags.store'), [
        'name' => str_repeat('a', 101),
    ]);

    $response->assertSessionHasErrors(['name']);
});

test('create page renders for authenticated user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.tags.create'));

    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page->component('dashboard/Tags/Create'));
});

test('edit page renders with tag data', function () {
    $user = User::factory()->create();
    $tag = Tag::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.tags.edit', $tag));

    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard/Tags/Edit')
            ->has('tag')
            ->where('tag.id', $tag->id)
        );
});

test('update updates an existing tag', function () {
    $user = User::factory()->create();
    $tag = Tag::factory()->create();

    $response = $this->actingAs($user)->put(route('admin.tags.update', $tag), [
        'name' => 'Updated Tag',
    ]);

    $response->assertRedirect(route('admin.tags.index'));
    $response->assertSessionHas('success');

    $tag->refresh();
    expect($tag->name)->toBe('Updated Tag');
});

test('update allows same name for same tag', function () {
    $user = User::factory()->create();
    $tag = Tag::factory()->create(['name' => 'My Tag']);

    $response = $this->actingAs($user)->put(route('admin.tags.update', $tag), [
        'name' => 'My Tag',
    ]);

    $response->assertRedirect(route('admin.tags.index'));
    $response->assertSessionHasNoErrors();
});

test('destroy deletes a tag with no linked tools', function () {
    $user = User::factory()->create();
    $tag = Tag::factory()->create();

    $response = $this->actingAs($user)->delete(route('admin.tags.destroy', $tag));

    $response->assertRedirect(route('admin.tags.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
});

test('destroy fails if tag has linked tools', function () {
    $user = User::factory()->create();
    $tag = Tag::factory()->create();
    $tool = Tool::factory()->create();
    $tool->tags()->attach($tag);

    $response = $this->actingAs($user)->delete(route('admin.tags.destroy', $tag));

    $response->assertRedirect();
    $response->assertSessionHas('error');

    $this->assertDatabaseHas('tags', ['id' => $tag->id]);
});

test('index includes tools count', function () {
    $user = User::factory()->create();
    $tag = Tag::factory()->create();
    $tools = Tool::factory()->count(3)->create();
    $tag->tools()->attach($tools);

    $response = $this->actingAs($user)->get(route('admin.tags.index'));

    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard/Tags/Index')
            ->where('tags.data.0.tools_count', 3)
        );
});

test('index returns tags sorted by name', function () {
    $user = User::factory()->create();
    Tag::factory()->create(['name' => 'Zebra']);
    Tag::factory()->create(['name' => 'Alpha']);
    Tag::factory()->create(['name' => 'Middle']);

    $response = $this->actingAs($user)->get(route('admin.tags.index'));

    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard/Tags/Index')
            ->where('tags.data.0.name', 'Alpha')
            ->where('tags.data.1.name', 'Middle')
            ->where('tags.data.2.name', 'Zebra')
        );
});
