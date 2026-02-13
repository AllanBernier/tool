<?php

use App\Enums\GenerationStatus;
use App\Models\Comparison;
use App\Models\Tool;

test('factory creates a valid comparison', function () {
    $comparison = Comparison::factory()->create();

    expect($comparison)->toBeInstanceOf(Comparison::class)
        ->and($comparison->id)->not->toBeEmpty()
        ->and($comparison->tool_a_id)->not->toBeEmpty()
        ->and($comparison->tool_b_id)->not->toBeEmpty()
        ->and($comparison->slug)->not->toBeEmpty();
});

test('slug is auto-generated in X-vs-Y format', function () {
    $toolA = Tool::factory()->create(['name' => 'Visual Studio Code']);
    $toolB = Tool::factory()->create(['name' => 'Sublime Text']);

    $comparison = Comparison::factory()->create([
        'tool_a_id' => $toolA->id,
        'tool_b_id' => $toolB->id,
    ]);

    expect($comparison->slug)->toBe('visual-studio-code-vs-sublime-text');
});

test('uniqueness of tool_a and tool_b pair', function () {
    $toolA = Tool::factory()->create();
    $toolB = Tool::factory()->create();

    Comparison::factory()->create([
        'tool_a_id' => $toolA->id,
        'tool_b_id' => $toolB->id,
    ]);

    expect(fn () => Comparison::factory()->create([
        'tool_a_id' => $toolA->id,
        'tool_b_id' => $toolB->id,
        'slug' => 'different-slug',
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});

test('toolA relationship returns BelongsTo', function () {
    $comparison = Comparison::factory()->create();

    expect($comparison->toolA)->toBeInstanceOf(Tool::class)
        ->and($comparison->toolA->id)->toBe($comparison->tool_a_id);
});

test('toolB relationship returns BelongsTo', function () {
    $comparison = Comparison::factory()->create();

    expect($comparison->toolB)->toBeInstanceOf(Tool::class)
        ->and($comparison->toolB->id)->toBe($comparison->tool_b_id);
});

test('published scope filters published comparisons', function () {
    Comparison::factory()->published()->create();
    Comparison::factory()->create(['is_published' => false]);

    $published = Comparison::published()->get();

    expect($published)->toHaveCount(1)
        ->and($published->first()->is_published)->toBeTrue();
});

test('comparison uses ulid as primary key', function () {
    $comparison = Comparison::factory()->create();

    expect($comparison->id)->toHaveLength(26);
});

test('route key name is slug', function () {
    $comparison = Comparison::factory()->create();

    expect($comparison->getRouteKeyName())->toBe('slug');
});

test('generation_status cast returns enum', function () {
    $comparison = Comparison::factory()->create(['generation_status' => 'pending']);

    $comparison->refresh();

    expect($comparison->generation_status)->toBe(GenerationStatus::Pending);
});

test('boolean fields are cast correctly', function () {
    $comparison = Comparison::factory()->create(['is_published' => true]);

    $comparison->refresh();

    expect($comparison->is_published)->toBeBool()->toBeTrue();
});

test('datetime fields are cast correctly', function () {
    $now = now();
    $comparison = Comparison::factory()->create([
        'generated_at' => $now,
        'published_at' => $now,
    ]);

    $comparison->refresh();

    expect($comparison->generated_at)->toBeInstanceOf(\Carbon\CarbonImmutable::class)
        ->and($comparison->published_at)->toBeInstanceOf(\Carbon\CarbonImmutable::class);
});

test('factory published state sets correct attributes', function () {
    $comparison = Comparison::factory()->published()->create();

    expect($comparison->is_published)->toBeTrue()
        ->and($comparison->published_at)->not->toBeNull()
        ->and($comparison->generation_status)->toBe(GenerationStatus::Completed);
});
