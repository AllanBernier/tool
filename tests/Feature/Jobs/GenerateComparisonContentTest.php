<?php

use App\Enums\GenerationStatus;
use App\Jobs\GenerateComparisonContent;
use App\Models\Comparison;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\AnonymousAgent;

function fakeComparisonAiResponse(array $overrides = []): string
{
    return json_encode(array_merge([
        'content' => '## VS Code vs Sublime Text\n\nComparatif détaillé entre ces deux éditeurs...',
        'verdict' => 'VS Code est idéal pour les projets complexes, Sublime Text pour la rapidité.',
        'meta_title' => 'VS Code vs Sublime Text : Comparatif détaillé',
        'meta_description' => 'Découvrez notre comparatif détaillé entre VS Code et Sublime Text.',
    ], $overrides));
}

test('generates comparison content from AI response and updates all fields', function () {
    AnonymousAgent::fake([fakeComparisonAiResponse()]);

    $comparison = Comparison::factory()->create([
        'generation_status' => GenerationStatus::Pending,
        'content' => null,
        'verdict' => null,
    ]);

    (new GenerateComparisonContent($comparison))->handle();

    $comparison->refresh();

    expect($comparison->generation_status)->toBe(GenerationStatus::Completed)
        ->and($comparison->generated_at)->not->toBeNull()
        ->and($comparison->content)->toContain('VS Code vs Sublime Text')
        ->and($comparison->verdict)->toContain('VS Code')
        ->and($comparison->meta_title)->toBe('VS Code vs Sublime Text : Comparatif détaillé')
        ->and($comparison->meta_description)->toContain('comparatif détaillé');
});

test('sets generation status to failed on API error', function () {
    AnonymousAgent::fake([fn () => throw new \RuntimeException('API Error')]);

    $comparison = Comparison::factory()->create([
        'generation_status' => GenerationStatus::Pending,
    ]);

    $originalContent = $comparison->content;

    expect(fn () => (new GenerateComparisonContent($comparison))->handle())
        ->toThrow(\RuntimeException::class);

    $comparison->refresh();

    expect($comparison->generation_status)->toBe(GenerationStatus::Failed)
        ->and($comparison->content)->toBe($originalContent);
});

test('job implements ShouldQueue', function () {
    $comparison = Comparison::factory()->create();
    $job = new GenerateComparisonContent($comparison);

    expect($job)->toBeInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class);
});

test('job has correct tries and timeout', function () {
    $comparison = Comparison::factory()->create();
    $job = new GenerateComparisonContent($comparison);

    expect($job->tries)->toBe(3)
        ->and($job->timeout)->toBe(120);
});

test('job has backoff configuration', function () {
    $comparison = Comparison::factory()->create();
    $job = new GenerateComparisonContent($comparison);

    expect($job->backoff())->toBe([10, 30]);
});

test('logs error on failure', function () {
    AnonymousAgent::fake([fn () => throw new \RuntimeException('API Error')]);
    Log::spy();

    $comparison = Comparison::factory()->create([
        'generation_status' => GenerationStatus::Pending,
    ]);

    try {
        (new GenerateComparisonContent($comparison))->handle();
    } catch (\RuntimeException) {
        // Expected
    }

    Log::shouldHaveReceived('error')
        ->withArgs(fn ($message, $context) => $message === 'GenerateComparisonContent failed'
            && $context['comparison'] === $comparison->slug
        )->once();
});

test('sets status to generating before calling AI', function () {
    AnonymousAgent::fake([fakeComparisonAiResponse()]);

    $comparison = Comparison::factory()->create([
        'generation_status' => GenerationStatus::Pending,
    ]);

    (new GenerateComparisonContent($comparison))->handle();

    $comparison->refresh();

    expect($comparison->generation_status)->toBe(GenerationStatus::Completed);
});
