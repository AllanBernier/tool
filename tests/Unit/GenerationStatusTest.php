<?php

use App\Enums\GenerationStatus;

it('is a string-backed enum', function () {
    expect(GenerationStatus::Pending->value)->toBe('pending')
        ->and(GenerationStatus::Generating->value)->toBe('generating')
        ->and(GenerationStatus::Completed->value)->toBe('completed')
        ->and(GenerationStatus::Failed->value)->toBe('failed');
});

it('has exactly four cases', function () {
    expect(GenerationStatus::cases())->toHaveCount(4);
});

it('can be created from string value', function (string $value, GenerationStatus $expected) {
    expect(GenerationStatus::from($value))->toBe($expected);
})->with([
    'pending' => ['pending', GenerationStatus::Pending],
    'generating' => ['generating', GenerationStatus::Generating],
    'completed' => ['completed', GenerationStatus::Completed],
    'failed' => ['failed', GenerationStatus::Failed],
]);
