<?php

use App\Enums\GenerationStatus;
use App\Jobs\FetchToolLogo;
use App\Jobs\GenerateToolContent;
use App\Jobs\SuggestAlternativesAndComparisons;
use App\Models\Tag;
use App\Models\Tool;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\AnonymousAgent;

function fakeAiResponse(array $overrides = []): string
{
    return json_encode(array_merge([
        'description' => 'Un éditeur de code léger et puissant développé par Microsoft.',
        'content' => '## Visual Studio Code\n\nVS Code est un éditeur de code...',
        'pros' => ['Gratuit', 'Extensible', 'Rapide', 'Multi-plateforme', 'Grande communauté'],
        'cons' => ['Consommation mémoire', 'Parfois lent avec extensions', 'Electron-based'],
        'features' => ['IntelliSense', 'Débogueur intégré', 'Git intégré', 'Extensions', 'Terminal intégré'],
        'faq' => [
            ['question' => 'Est-ce gratuit ?', 'answer' => 'Oui, VS Code est totalement gratuit.'],
            ['question' => 'Quels langages sont supportés ?', 'answer' => 'Presque tous via les extensions.'],
        ],
        'pricing' => [
            ['plan' => 'Gratuit', 'price' => 'Gratuit', 'features' => ['Toutes les fonctionnalités']],
        ],
        'platforms' => ['Windows', 'macOS', 'Linux', 'Web'],
        'suggested_tags' => ['editeur', 'ide', 'vscode', 'microsoft'],
        'suggested_alternatives' => ['Sublime Text', 'Atom', 'WebStorm'],
        'meta_title' => 'VS Code — Éditeur de code gratuit',
        'meta_description' => 'Découvrez Visual Studio Code, l\'éditeur de code gratuit et open-source de Microsoft.',
    ], $overrides));
}

test('generates tool content from AI response and updates all fields', function () {
    AnonymousAgent::fake([fakeAiResponse()]);
    Bus::fake([FetchToolLogo::class, SuggestAlternativesAndComparisons::class]);

    $tool = Tool::factory()->create([
        'name' => 'Visual Studio Code',
        'url' => 'https://code.visualstudio.com',
        'generation_status' => GenerationStatus::Pending,
        'description' => null,
        'content' => null,
    ]);

    (new GenerateToolContent($tool))->handle();

    $tool->refresh();

    expect($tool->generation_status)->toBe(GenerationStatus::Completed)
        ->and($tool->generated_at)->not->toBeNull()
        ->and($tool->description)->toBe('Un éditeur de code léger et puissant développé par Microsoft.')
        ->and($tool->content)->toContain('Visual Studio Code')
        ->and($tool->pros)->toBeArray()->toHaveCount(5)
        ->and($tool->cons)->toBeArray()->toHaveCount(3)
        ->and($tool->features)->toBeArray()->toHaveCount(5)
        ->and($tool->faq)->toBeArray()->toHaveCount(2)
        ->and($tool->pricing)->toBeArray()->toHaveCount(1)
        ->and($tool->platforms)->toBeArray()->toHaveCount(4)
        ->and($tool->meta_title)->toBe('VS Code — Éditeur de code gratuit')
        ->and($tool->meta_description)->toContain('Visual Studio Code');
});

test('sets generation status to failed on API error', function () {
    AnonymousAgent::fake([fn () => throw new \RuntimeException('API Error')]);

    $tool = Tool::factory()->create([
        'generation_status' => GenerationStatus::Pending,
    ]);

    $originalDescription = $tool->description;

    expect(fn () => (new GenerateToolContent($tool))->handle())
        ->toThrow(\RuntimeException::class);

    $tool->refresh();

    expect($tool->generation_status)->toBe(GenerationStatus::Failed)
        ->and($tool->description)->toBe($originalDescription);
});

test('dispatches FetchToolLogo and SuggestAlternativesAndComparisons after success', function () {
    AnonymousAgent::fake([fakeAiResponse()]);
    Bus::fake([FetchToolLogo::class, SuggestAlternativesAndComparisons::class]);

    $tool = Tool::factory()->create([
        'generation_status' => GenerationStatus::Pending,
    ]);

    (new GenerateToolContent($tool))->handle();

    Bus::assertDispatched(FetchToolLogo::class, fn ($job) => $job->tool->is($tool));
    Bus::assertDispatched(SuggestAlternativesAndComparisons::class, fn ($job) => $job->tool->is($tool));
});

test('creates and attaches suggested tags', function () {
    AnonymousAgent::fake([fakeAiResponse([
        'suggested_tags' => ['editeur', 'ide', 'vscode'],
    ])]);
    Bus::fake([FetchToolLogo::class, SuggestAlternativesAndComparisons::class]);

    $tool = Tool::factory()->create([
        'generation_status' => GenerationStatus::Pending,
    ]);

    (new GenerateToolContent($tool))->handle();

    expect($tool->tags)->toHaveCount(3);
    expect(Tag::where('slug', 'editeur')->exists())->toBeTrue();
    expect(Tag::where('slug', 'ide')->exists())->toBeTrue();
    expect(Tag::where('slug', 'vscode')->exists())->toBeTrue();
});

test('does not duplicate existing tags', function () {
    AnonymousAgent::fake([fakeAiResponse([
        'suggested_tags' => ['editeur', 'ide'],
    ])]);
    Bus::fake([FetchToolLogo::class, SuggestAlternativesAndComparisons::class]);

    $existingTag = Tag::factory()->create(['name' => 'editeur', 'slug' => 'editeur']);

    $tool = Tool::factory()->create([
        'generation_status' => GenerationStatus::Pending,
    ]);

    (new GenerateToolContent($tool))->handle();

    expect(Tag::where('slug', 'editeur')->count())->toBe(1);
    expect($tool->tags->pluck('id'))->toContain($existingTag->id);
});

test('job implements ShouldQueue', function () {
    $tool = Tool::factory()->create();
    $job = new GenerateToolContent($tool);

    expect($job)->toBeInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class);
});

test('job has correct tries and timeout', function () {
    $tool = Tool::factory()->create();
    $job = new GenerateToolContent($tool);

    expect($job->tries)->toBe(3)
        ->and($job->timeout)->toBe(120);
});

test('job has backoff configuration', function () {
    $tool = Tool::factory()->create();
    $job = new GenerateToolContent($tool);

    expect($job->backoff())->toBe([10, 30]);
});

test('logs error on failure', function () {
    AnonymousAgent::fake([fn () => throw new \RuntimeException('API Error')]);
    Log::spy();

    $tool = Tool::factory()->create([
        'generation_status' => GenerationStatus::Pending,
    ]);

    try {
        (new GenerateToolContent($tool))->handle();
    } catch (\RuntimeException) {
        // Expected
    }

    Log::shouldHaveReceived('error')
        ->withArgs(fn ($message, $context) => $message === 'GenerateToolContent failed'
            && $context['tool'] === $tool->slug
        )->once();
});

test('does not dispatch jobs on failure', function () {
    AnonymousAgent::fake([fn () => throw new \RuntimeException('API Error')]);
    Bus::fake([FetchToolLogo::class, SuggestAlternativesAndComparisons::class]);

    $tool = Tool::factory()->create([
        'generation_status' => GenerationStatus::Pending,
    ]);

    try {
        (new GenerateToolContent($tool))->handle();
    } catch (\RuntimeException) {
        // Expected
    }

    Bus::assertNotDispatched(FetchToolLogo::class);
    Bus::assertNotDispatched(SuggestAlternativesAndComparisons::class);
});
