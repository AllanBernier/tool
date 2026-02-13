<?php

use App\Enums\GenerationStatus;
use App\Models\Category;
use App\Models\Comparison;
use App\Models\Tag;
use App\Models\Tool;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('dashboard displays correct statistics', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    Tag::factory()->count(4)->create();

    $toolA = Tool::factory()->published()->create(['category_id' => $category->id]);
    $toolB = Tool::factory()->published()->create(['category_id' => $category->id]);
    $toolC = Tool::factory()->published()->create(['category_id' => $category->id]);
    Tool::factory()->count(2)->create(['category_id' => $category->id]);

    Comparison::factory()->create(['tool_a_id' => $toolA->id, 'tool_b_id' => $toolB->id]);
    Comparison::factory()->create(['tool_a_id' => $toolA->id, 'tool_b_id' => $toolC->id]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('stats', fn ($stats) => $stats
                ->where('tools', 5)
                ->where('tools_published', 3)
                ->where('comparisons', 2)
                ->where('categories', 1)
                ->where('tags', 4)
            )
            ->has('latestTools', 5)
            ->has('latestComparisons', 2)
        );
});

test('dashboard shows latest 5 tools ordered by newest first', function () {
    $user = User::factory()->create();

    Tool::factory()->create(['name' => 'Old Tool', 'created_at' => now()->subDays(10)]);
    Tool::factory()->create(['name' => 'Tool 2', 'created_at' => now()->subDays(5)]);
    Tool::factory()->create(['name' => 'Tool 3', 'created_at' => now()->subDays(4)]);
    Tool::factory()->create(['name' => 'Tool 4', 'created_at' => now()->subDays(3)]);
    Tool::factory()->create(['name' => 'Tool 5', 'created_at' => now()->subDays(2)]);
    Tool::factory()->create(['name' => 'Newest Tool', 'created_at' => now()]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('latestTools', 5)
            ->where('latestTools.0.name', 'Newest Tool')
        );
});

test('dashboard shows latest 5 comparisons with tool relationships', function () {
    $user = User::factory()->create();

    Comparison::factory()->count(6)->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('latestComparisons', 5)
            ->has('latestComparisons.0.tool_a')
            ->has('latestComparisons.0.tool_b')
        );
});

test('dashboard shows generation status for tools', function () {
    $user = User::factory()->create();

    Tool::factory()->create(['generation_status' => GenerationStatus::Generating]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->where('latestTools.0.generation_status', 'generating')
        );
});
