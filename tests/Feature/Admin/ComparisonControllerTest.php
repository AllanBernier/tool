<?php

use App\Models\Comparison;
use App\Models\Tool;
use App\Models\User;

test('index returns paginated comparisons for authenticated user', function () {
    $user = User::factory()->create();
    Comparison::factory()->count(3)->create();

    $response = $this->actingAs($user)->get(route('admin.comparatifs.index'));

    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard/Comparisons/Index')
            ->has('comparisons.data', 3)
        );
});

test('index filters by generation status', function () {
    $user = User::factory()->create();
    Comparison::factory()->published()->create();
    Comparison::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.comparatifs.index', ['generation_status' => 'completed']));

    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard/Comparisons/Index')
            ->has('comparisons.data', 1)
        );
});

test('create page renders with tools list', function () {
    $user = User::factory()->create();
    Tool::factory()->count(5)->create();

    $response = $this->actingAs($user)->get(route('admin.comparatifs.create'));

    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard/Comparisons/Create')
            ->has('tools', 5)
        );
});

test('store creates comparison with valid tool pair', function () {
    $user = User::factory()->create();
    $toolA = Tool::factory()->create();
    $toolB = Tool::factory()->create();

    $response = $this->actingAs($user)->post(route('admin.comparatifs.store'), [
        'tool_a_id' => $toolA->id,
        'tool_b_id' => $toolB->id,
    ]);

    $comparison = Comparison::first();

    $response->assertRedirect(route('admin.comparatifs.edit', $comparison));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('comparisons', [
        'tool_a_id' => $toolA->id,
        'tool_b_id' => $toolB->id,
    ]);
});

test('store with duplicate pair fails', function () {
    $user = User::factory()->create();
    $toolA = Tool::factory()->create();
    $toolB = Tool::factory()->create();

    Comparison::factory()->create([
        'tool_a_id' => $toolA->id,
        'tool_b_id' => $toolB->id,
    ]);

    $response = $this->actingAs($user)->post(route('admin.comparatifs.store'), [
        'tool_a_id' => $toolA->id,
        'tool_b_id' => $toolB->id,
    ]);

    $response->assertSessionHasErrors(['tool_b_id']);
});

test('store with reversed duplicate pair fails', function () {
    $user = User::factory()->create();
    $toolA = Tool::factory()->create();
    $toolB = Tool::factory()->create();

    Comparison::factory()->create([
        'tool_a_id' => $toolA->id,
        'tool_b_id' => $toolB->id,
    ]);

    $response = $this->actingAs($user)->post(route('admin.comparatifs.store'), [
        'tool_a_id' => $toolB->id,
        'tool_b_id' => $toolA->id,
    ]);

    $response->assertSessionHasErrors(['tool_b_id']);
});

test('store with tool_a_id equal to tool_b_id fails', function () {
    $user = User::factory()->create();
    $tool = Tool::factory()->create();

    $response = $this->actingAs($user)->post(route('admin.comparatifs.store'), [
        'tool_a_id' => $tool->id,
        'tool_b_id' => $tool->id,
    ]);

    $response->assertSessionHasErrors(['tool_b_id']);
});

test('edit page renders with comparison data', function () {
    $user = User::factory()->create();
    $comparison = Comparison::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.comparatifs.edit', $comparison));

    $response->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('dashboard/Comparisons/Edit')
            ->has('comparison')
            ->where('comparison.id', $comparison->id)
        );
});

test('update updates content and verdict', function () {
    $user = User::factory()->create();
    $comparison = Comparison::factory()->create();

    $response = $this->actingAs($user)->put(route('admin.comparatifs.update', $comparison), [
        'content' => 'Updated content here.',
        'verdict' => 'Tool A is better.',
        'meta_title' => 'SEO Title',
        'meta_description' => 'SEO Description',
    ]);

    $response->assertRedirect(route('admin.comparatifs.edit', $comparison));
    $response->assertSessionHas('success');

    $comparison->refresh();
    expect($comparison->content)->toBe('Updated content here.');
    expect($comparison->verdict)->toBe('Tool A is better.');
    expect($comparison->meta_title)->toBe('SEO Title');
    expect($comparison->meta_description)->toBe('SEO Description');
});

test('togglePublish toggles publication status', function () {
    $user = User::factory()->create();
    $comparison = Comparison::factory()->create(['is_published' => false, 'published_at' => null]);

    $response = $this->actingAs($user)->post(route('admin.comparisons.toggle-publish', $comparison));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $comparison->refresh();
    expect($comparison->is_published)->toBeTrue();
    expect($comparison->published_at)->not->toBeNull();

    $this->actingAs($user)->post(route('admin.comparisons.toggle-publish', $comparison));

    $comparison->refresh();
    expect($comparison->is_published)->toBeFalse();
    expect($comparison->published_at)->toBeNull();
});

test('destroy deletes comparison', function () {
    $user = User::factory()->create();
    $comparison = Comparison::factory()->create();

    $response = $this->actingAs($user)->delete(route('admin.comparatifs.destroy', $comparison));

    $response->assertRedirect(route('admin.comparatifs.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('comparisons', ['id' => $comparison->id]);
});

test('unauthenticated access to index is redirected', function () {
    $response = $this->get(route('admin.comparatifs.index'));

    $response->assertRedirect(route('login'));
});
