<?php

use App\AI\Prompts\GenerateToolPrompt;
use App\Models\Category;
use App\Models\Tool;

it('returns a non-empty system prompt', function () {
    $system = GenerateToolPrompt::system();

    expect($system)->toBeString()
        ->not->toBeEmpty();
});

it('system prompt specifies JSON output format', function () {
    $system = GenerateToolPrompt::system();

    expect($system)->toContain('JSON');
});

it('system prompt specifies professional developer-oriented tone', function () {
    $system = GenerateToolPrompt::system();

    expect($system)->toContain('Professionnel')
        ->toContain('développeur')
        ->toContain('Objectif');
});

it('returns a user prompt containing the tool name and url', function () {
    $tool = Tool::factory()->create([
        'name' => 'Visual Studio Code',
        'url' => 'https://code.visualstudio.com',
    ]);

    $prompt = GenerateToolPrompt::user($tool);

    expect($prompt)->toContain('Visual Studio Code')
        ->toContain('https://code.visualstudio.com');
});

it('includes the category name in the user prompt', function () {
    $category = Category::factory()->create(['name' => 'Éditeurs de code']);
    $tool = Tool::factory()->create(['category_id' => $category->id]);

    $prompt = GenerateToolPrompt::user($tool);

    expect($prompt)->toContain('Éditeurs de code');
});

it('user prompt specifies expected JSON fields', function () {
    $tool = Tool::factory()->create();

    $prompt = GenerateToolPrompt::user($tool);

    expect($prompt)->toContain('"description"')
        ->toContain('"content"')
        ->toContain('"pros"')
        ->toContain('"cons"')
        ->toContain('"features"')
        ->toContain('"faq"')
        ->toContain('"pricing"')
        ->toContain('"platforms"')
        ->toContain('"suggested_tags"')
        ->toContain('"suggested_alternatives"')
        ->toContain('"meta_title"')
        ->toContain('"meta_description"');
});

it('user prompt specifies content constraints', function () {
    $tool = Tool::factory()->create();

    $prompt = GenerateToolPrompt::user($tool);

    expect($prompt)->toContain('5 à 8')
        ->toContain('3 à 5')
        ->toContain('5 à 10')
        ->toContain('~60 caractères')
        ->toContain('~155 caractères');
});

it('returns the output schema with all expected keys', function () {
    $schema = GenerateToolPrompt::outputSchema();

    expect($schema)->toBeArray()
        ->toHaveKeys([
            'description',
            'content',
            'pros',
            'cons',
            'features',
            'faq',
            'pricing',
            'platforms',
            'suggested_tags',
            'suggested_alternatives',
            'meta_title',
            'meta_description',
        ]);
});

it('returns all required fields', function () {
    $fields = GenerateToolPrompt::requiredFields();

    expect($fields)->toBeArray()
        ->toContain('description')
        ->toContain('content')
        ->toContain('pros')
        ->toContain('cons')
        ->toContain('features')
        ->toContain('faq')
        ->toContain('pricing')
        ->toContain('platforms')
        ->toContain('suggested_tags')
        ->toContain('suggested_alternatives')
        ->toContain('meta_title')
        ->toContain('meta_description')
        ->toHaveCount(12);
});
