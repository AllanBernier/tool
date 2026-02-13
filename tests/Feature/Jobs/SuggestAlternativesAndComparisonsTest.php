<?php

use App\Enums\GenerationStatus;
use App\Jobs\SuggestAlternativesAndComparisons;
use App\Models\Comparison;
use App\Models\Tool;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\AnonymousAgent;

function fakeAlternativesResponse(array $overrides = []): string
{
    return json_encode(array_merge([
        'alternatives' => [
            ['name' => 'Sublime Text', 'url' => 'https://www.sublimetext.com', 'reason' => 'Éditeur rapide et léger'],
            ['name' => 'WebStorm', 'url' => 'https://www.jetbrains.com/webstorm', 'reason' => 'IDE puissant pour le web'],
            ['name' => 'Atom', 'url' => 'https://atom.io', 'reason' => 'Éditeur open-source personnalisable'],
        ],
    ], $overrides));
}

test('creates draft tools for non-existing alternatives', function () {
    AnonymousAgent::fake([fakeAlternativesResponse()]);

    $tool = Tool::factory()->create([
        'name' => 'Visual Studio Code',
        'url' => 'https://code.visualstudio.com',
    ]);

    (new SuggestAlternativesAndComparisons($tool))->handle();

    expect(Tool::where('name', 'Sublime Text')->exists())->toBeTrue()
        ->and(Tool::where('name', 'WebStorm')->exists())->toBeTrue()
        ->and(Tool::where('name', 'Atom')->exists())->toBeTrue();

    $sublimeText = Tool::where('name', 'Sublime Text')->first();

    expect($sublimeText->url)->toBe('https://www.sublimetext.com')
        ->and($sublimeText->category_id)->toBe($tool->category_id)
        ->and($sublimeText->generation_status)->toBe(GenerationStatus::Pending);
});

test('does not duplicate already existing alternatives', function () {
    $tool = Tool::factory()->create(['name' => 'Visual Studio Code']);
    $existingTool = Tool::factory()->create([
        'name' => 'Sublime Text',
        'category_id' => $tool->category_id,
    ]);

    AnonymousAgent::fake([fakeAlternativesResponse([
        'alternatives' => [
            ['name' => 'Sublime Text', 'url' => 'https://www.sublimetext.com', 'reason' => 'Fast editor'],
        ],
    ])]);

    (new SuggestAlternativesAndComparisons($tool))->handle();

    expect(Tool::where('name', 'Sublime Text')->count())->toBe(1);
    expect($tool->alternatives->pluck('id'))->toContain($existingTool->id);
});

test('creates comparisons for each alternative pair', function () {
    AnonymousAgent::fake([fakeAlternativesResponse()]);

    $tool = Tool::factory()->create(['name' => 'Visual Studio Code']);

    (new SuggestAlternativesAndComparisons($tool))->handle();

    expect(Comparison::count())->toBe(3);

    $sublimeText = Tool::where('name', 'Sublime Text')->first();
    expect(Comparison::where('tool_a_id', $tool->id)->where('tool_b_id', $sublimeText->id)->exists())->toBeTrue();
});

test('does not duplicate existing comparisons', function () {
    $tool = Tool::factory()->create(['name' => 'Visual Studio Code']);
    $existingTool = Tool::factory()->create([
        'name' => 'Sublime Text',
        'category_id' => $tool->category_id,
    ]);

    Comparison::factory()->create([
        'tool_a_id' => $tool->id,
        'tool_b_id' => $existingTool->id,
    ]);

    AnonymousAgent::fake([fakeAlternativesResponse([
        'alternatives' => [
            ['name' => 'Sublime Text', 'url' => 'https://www.sublimetext.com', 'reason' => 'Fast editor'],
        ],
    ])]);

    (new SuggestAlternativesAndComparisons($tool))->handle();

    expect(Comparison::where('tool_a_id', $tool->id)->where('tool_b_id', $existingTool->id)->count())->toBe(1);
});

test('does not duplicate existing comparisons in reverse order', function () {
    $tool = Tool::factory()->create(['name' => 'Visual Studio Code']);
    $existingTool = Tool::factory()->create([
        'name' => 'Sublime Text',
        'category_id' => $tool->category_id,
    ]);

    Comparison::factory()->create([
        'tool_a_id' => $existingTool->id,
        'tool_b_id' => $tool->id,
    ]);

    AnonymousAgent::fake([fakeAlternativesResponse([
        'alternatives' => [
            ['name' => 'Sublime Text', 'url' => 'https://www.sublimetext.com', 'reason' => 'Fast editor'],
        ],
    ])]);

    (new SuggestAlternativesAndComparisons($tool))->handle();

    expect(Comparison::count())->toBe(1);
});

test('attaches alternatives via pivot table', function () {
    AnonymousAgent::fake([fakeAlternativesResponse()]);

    $tool = Tool::factory()->create(['name' => 'Visual Studio Code']);

    (new SuggestAlternativesAndComparisons($tool))->handle();

    expect($tool->alternatives)->toHaveCount(3);
});

test('job implements ShouldQueue', function () {
    $tool = Tool::factory()->create();
    $job = new SuggestAlternativesAndComparisons($tool);

    expect($job)->toBeInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class);
});

test('job has correct tries and timeout', function () {
    $tool = Tool::factory()->create();
    $job = new SuggestAlternativesAndComparisons($tool);

    expect($job->tries)->toBe(3)
        ->and($job->timeout)->toBe(120);
});

test('job has backoff configuration', function () {
    $tool = Tool::factory()->create();
    $job = new SuggestAlternativesAndComparisons($tool);

    expect($job->backoff())->toBe([10, 30]);
});

test('logs the number of alternatives and comparisons created', function () {
    AnonymousAgent::fake([fakeAlternativesResponse()]);
    Log::spy();

    $tool = Tool::factory()->create(['name' => 'Visual Studio Code']);

    (new SuggestAlternativesAndComparisons($tool))->handle();

    Log::shouldHaveReceived('info')
        ->withArgs(fn ($message, $context) => $message === 'SuggestAlternativesAndComparisons completed'
            && $context['tool'] === $tool->slug
            && $context['alternatives_created'] === 3
            && $context['comparisons_created'] === 3
        )->once();
});

test('logs error on failure', function () {
    AnonymousAgent::fake([fn () => throw new \RuntimeException('API Error')]);
    Log::spy();

    $tool = Tool::factory()->create();

    try {
        (new SuggestAlternativesAndComparisons($tool))->handle();
    } catch (\RuntimeException) {
        // Expected
    }

    Log::shouldHaveReceived('error')
        ->withArgs(fn ($message, $context) => $message === 'SuggestAlternativesAndComparisons failed'
            && $context['tool'] === $tool->slug
        )->once();
});

test('skips alternatives with empty names', function () {
    AnonymousAgent::fake([fakeAlternativesResponse([
        'alternatives' => [
            ['name' => '', 'url' => 'https://example.com', 'reason' => 'Empty name'],
            ['name' => 'Sublime Text', 'url' => 'https://www.sublimetext.com', 'reason' => 'Valid'],
        ],
    ])]);

    $tool = Tool::factory()->create(['name' => 'Visual Studio Code']);

    (new SuggestAlternativesAndComparisons($tool))->handle();

    expect($tool->alternatives)->toHaveCount(1);
});

test('does not attach self as alternative', function () {
    $tool = Tool::factory()->create(['name' => 'Visual Studio Code']);

    AnonymousAgent::fake([fakeAlternativesResponse([
        'alternatives' => [
            ['name' => 'Visual Studio Code', 'url' => 'https://code.visualstudio.com', 'reason' => 'Self'],
        ],
    ])]);

    (new SuggestAlternativesAndComparisons($tool))->handle();

    expect($tool->alternatives)->toHaveCount(0);
    expect(Comparison::count())->toBe(0);
});
