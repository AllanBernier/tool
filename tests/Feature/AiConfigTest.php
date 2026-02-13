<?php

it('uses openai as the default ai provider', function () {
    expect(config('ai.default'))->toBe('openai');
});

it('has openai provider configured', function () {
    $openai = config('ai.providers.openai');

    expect($openai)->not->toBeNull()
        ->and($openai['driver'])->toBe('openai');
});

it('reads openai api key from environment', function () {
    expect(config('ai.providers.openai.key'))->not->toBeNull();
});

it('has openai as default for embeddings', function () {
    expect(config('ai.default_for_embeddings'))->toBe('openai');
});
