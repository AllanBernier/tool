<?php

use App\AI\Prompts\GenerateComparisonPrompt;
use App\Models\Category;
use App\Models\Comparison;
use App\Models\Tool;

it('returns a non-empty system prompt', function () {
    $system = GenerateComparisonPrompt::system();

    expect($system)->toBeString()
        ->not->toBeEmpty();
});

it('system prompt specifies JSON output format', function () {
    $system = GenerateComparisonPrompt::system();

    expect($system)->toContain('JSON');
});

it('system prompt specifies professional and objective tone', function () {
    $system = GenerateComparisonPrompt::system();

    expect($system)->toContain('Professionnel')
        ->toContain('Objectif')
        ->toContain('équilibré');
});

it('returns a user prompt containing both tool names and urls', function () {
    $toolA = Tool::factory()->create([
        'name' => 'Laravel',
        'url' => 'https://laravel.com',
    ]);
    $toolB = Tool::factory()->create([
        'name' => 'Symfony',
        'url' => 'https://symfony.com',
    ]);

    $comparison = Comparison::factory()->create([
        'tool_a_id' => $toolA->id,
        'tool_b_id' => $toolB->id,
    ]);

    $prompt = GenerateComparisonPrompt::user($comparison);

    expect($prompt)->toContain('Laravel')
        ->toContain('https://laravel.com')
        ->toContain('Symfony')
        ->toContain('https://symfony.com');
});

it('includes tool descriptions in the user prompt', function () {
    $toolA = Tool::factory()->create(['description' => 'Un framework PHP élégant']);
    $toolB = Tool::factory()->create(['description' => 'Un framework PHP robuste']);

    $comparison = Comparison::factory()->create([
        'tool_a_id' => $toolA->id,
        'tool_b_id' => $toolB->id,
    ]);

    $prompt = GenerateComparisonPrompt::user($comparison);

    expect($prompt)->toContain('Un framework PHP élégant')
        ->toContain('Un framework PHP robuste');
});

it('includes category names in the user prompt', function () {
    $category = Category::factory()->create(['name' => 'Frameworks']);
    $toolA = Tool::factory()->create(['category_id' => $category->id]);
    $toolB = Tool::factory()->create(['category_id' => $category->id]);

    $comparison = Comparison::factory()->create([
        'tool_a_id' => $toolA->id,
        'tool_b_id' => $toolB->id,
    ]);

    $prompt = GenerateComparisonPrompt::user($comparison);

    expect($prompt)->toContain('Frameworks');
});

it('user prompt specifies expected JSON fields', function () {
    $comparison = Comparison::factory()->create();

    $prompt = GenerateComparisonPrompt::user($comparison);

    expect($prompt)->toContain('"content"')
        ->toContain('"verdict"')
        ->toContain('"meta_title"')
        ->toContain('"meta_description"');
});

it('returns the output schema with all expected keys', function () {
    $schema = GenerateComparisonPrompt::outputSchema();

    expect($schema)->toBeArray()
        ->toHaveKeys([
            'content',
            'verdict',
            'meta_title',
            'meta_description',
        ]);
});

it('returns all required fields', function () {
    $fields = GenerateComparisonPrompt::requiredFields();

    expect($fields)->toBeArray()
        ->toContain('content')
        ->toContain('verdict')
        ->toContain('meta_title')
        ->toContain('meta_description')
        ->toHaveCount(4);
});
