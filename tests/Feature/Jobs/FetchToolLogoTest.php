<?php

use App\Jobs\FetchToolLogo;
use App\Models\Tool;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('logos');
});

test('fetches favicon from site HTML and stores logo', function () {
    $tool = Tool::factory()->create(['url' => 'https://example.com', 'logo_path' => null]);

    $html = '<html><head><link rel="icon" href="/favicon.png"></head><body></body></html>';
    $fakeImage = 'fake-image-content';

    Http::fake([
        'example.com' => Http::response($html),
        'example.com/favicon.png' => Http::response($fakeImage),
    ]);

    (new FetchToolLogo($tool))->handle();

    Storage::disk('logos')->assertExists($tool->slug.'.png');
    expect($tool->fresh()->logo_path)->toBe($tool->slug.'.png');
});

test('falls back to Google Favicon API when no favicon in HTML', function () {
    $tool = Tool::factory()->create(['url' => 'https://example.com', 'logo_path' => null]);

    $html = '<html><head><title>No icon</title></head><body></body></html>';
    $fakeImage = 'google-favicon-content';

    Http::fake([
        'example.com' => Http::response($html),
        'www.google.com/s2/favicons*' => Http::response($fakeImage),
    ]);

    (new FetchToolLogo($tool))->handle();

    Storage::disk('logos')->assertExists($tool->slug.'.png');
    expect($tool->fresh()->logo_path)->toBe($tool->slug.'.png');
});

test('falls back to Google API when site request fails', function () {
    $tool = Tool::factory()->create(['url' => 'https://example.com', 'logo_path' => null]);

    $fakeImage = 'google-favicon-content';

    Http::fake([
        'example.com' => Http::response('', 500),
        'www.google.com/s2/favicons*' => Http::response($fakeImage),
    ]);

    (new FetchToolLogo($tool))->handle();

    Storage::disk('logos')->assertExists($tool->slug.'.png');
    expect($tool->fresh()->logo_path)->toBe($tool->slug.'.png');
});

test('does not crash when all fetches fail and logo_path remains null', function () {
    $tool = Tool::factory()->create(['url' => 'https://example.com', 'logo_path' => null]);

    Http::fake([
        'example.com' => Http::response('', 500),
        'www.google.com/s2/favicons*' => Http::response('', 500),
    ]);

    (new FetchToolLogo($tool))->handle();

    Storage::disk('logos')->assertMissing($tool->slug.'.png');
    expect($tool->fresh()->logo_path)->toBeNull();
});

test('job implements ShouldQueue', function () {
    $tool = Tool::factory()->create();
    $job = new FetchToolLogo($tool);

    expect($job)->toBeInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class);
});

test('job has correct tries and timeout', function () {
    $tool = Tool::factory()->create();
    $job = new FetchToolLogo($tool);

    expect($job->tries)->toBe(3)
        ->and($job->timeout)->toBe(30);
});

test('job has backoff configuration', function () {
    $tool = Tool::factory()->create();
    $job = new FetchToolLogo($tool);

    expect($job->backoff())->toBe([5, 15, 30]);
});

test('extracts favicon with href before rel attribute order', function () {
    $tool = Tool::factory()->create(['url' => 'https://example.com', 'logo_path' => null]);

    $html = '<html><head><link href="/icons/logo.png" rel="icon"></head></html>';
    $fakeImage = 'image-data';

    Http::fake([
        'example.com' => Http::response($html),
        'example.com/icons/logo.png' => Http::response($fakeImage),
    ]);

    (new FetchToolLogo($tool))->handle();

    Storage::disk('logos')->assertExists($tool->slug.'.png');
    expect($tool->fresh()->logo_path)->toBe($tool->slug.'.png');
});

test('resolves absolute favicon URLs correctly', function () {
    $tool = Tool::factory()->create(['url' => 'https://example.com', 'logo_path' => null]);

    $html = '<html><head><link rel="icon" href="https://cdn.example.com/icon.png"></head></html>';
    $fakeImage = 'cdn-image';

    Http::fake([
        'example.com' => Http::response($html),
        'cdn.example.com/icon.png' => Http::response($fakeImage),
    ]);

    (new FetchToolLogo($tool))->handle();

    Storage::disk('logos')->assertExists($tool->slug.'.png');
});
