<?php

use App\AI\Prompts\SuggestAlternativesPrompt;
use App\Models\Category;
use App\Models\Tool;

it('returns a non-empty system prompt', function () {
    $system = SuggestAlternativesPrompt::system();

    expect($system)->toBeString()
        ->not->toBeEmpty();
});

it('system prompt specifies JSON output format', function () {
    $system = SuggestAlternativesPrompt::system();

    expect($system)->toContain('JSON');
});

it('system prompt specifies factual tone', function () {
    $system = SuggestAlternativesPrompt::system();

    expect($system)->toContain('Professionnel')
        ->toContain('Objectif')
        ->toContain('factuel');
});

it('returns a user prompt containing the tool name and url', function () {
    $tool = Tool::factory()->create([
        'name' => 'GitHub',
        'url' => 'https://github.com',
    ]);

    $prompt = SuggestAlternativesPrompt::user($tool);

    expect($prompt)->toContain('GitHub')
        ->toContain('https://github.com');
});

it('includes the category name in the user prompt', function () {
    $category = Category::factory()->create(['name' => 'Gestion de code']);
    $tool = Tool::factory()->create(['category_id' => $category->id]);

    $prompt = SuggestAlternativesPrompt::user($tool);

    expect($prompt)->toContain('Gestion de code');
});

it('includes the tool description in the user prompt', function () {
    $tool = Tool::factory()->create(['description' => 'Plateforme de collaboration pour développeurs']);

    $prompt = SuggestAlternativesPrompt::user($tool);

    expect($prompt)->toContain('Plateforme de collaboration pour développeurs');
});

it('user prompt specifies expected JSON structure', function () {
    $tool = Tool::factory()->create();

    $prompt = SuggestAlternativesPrompt::user($tool);

    expect($prompt)->toContain('"alternatives"')
        ->toContain('"name"')
        ->toContain('"url"')
        ->toContain('"reason"');
});

it('user prompt specifies constraints', function () {
    $tool = Tool::factory()->create();

    $prompt = SuggestAlternativesPrompt::user($tool);

    expect($prompt)->toContain('5 et 10')
        ->toContain('outils réels');
});

it('returns the output schema with alternatives key', function () {
    $schema = SuggestAlternativesPrompt::outputSchema();

    expect($schema)->toBeArray()
        ->toHaveKey('alternatives');
});

it('returns all required fields', function () {
    $fields = SuggestAlternativesPrompt::requiredFields();

    expect($fields)->toBeArray()
        ->toContain('alternatives')
        ->toHaveCount(1);
});
