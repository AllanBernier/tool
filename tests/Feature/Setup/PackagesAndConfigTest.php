<?php

use Illuminate\Support\Facades\Storage;

test('ai config file exists and is published', function () {
    expect(config_path('ai.php'))->toBeFile();
});

test('openai is configured as default ai provider', function () {
    expect(config('ai.default'))->toBe('openai');
});

test('openai provider is configured with env key', function () {
    expect(config('ai.providers.openai'))->toBeArray()
        ->and(config('ai.providers.openai.driver'))->toBe('openai');
});

test('logos disk is configured in filesystems', function () {
    expect(config('filesystems.disks.logos'))->toBeArray()
        ->and(config('filesystems.disks.logos.driver'))->toBe('local')
        ->and(config('filesystems.disks.logos.visibility'))->toBe('public');
});

test('logos disk root points to storage/app/public/logos', function () {
    expect(config('filesystems.disks.logos.root'))
        ->toBe(storage_path('app/public/logos'));
});

test('logos directory exists', function () {
    expect(storage_path('app/public/logos'))->toBeDirectory();
});

test('logos are publicly accessible via storage symlink', function () {
    Storage::disk('logos')->put('test-logo.txt', 'test');

    expect(public_path('storage/logos/test-logo.txt'))->toBeFile();

    Storage::disk('logos')->delete('test-logo.txt');
});

test('queue connection is set to database in env', function () {
    $env = file_get_contents(base_path('.env'));

    expect($env)->toContain('QUEUE_CONNECTION=database');
});

test('jobs table migration exists', function () {
    $migrations = glob(database_path('migrations/*create_jobs_table*'));

    expect($migrations)->not->toBeEmpty();
});

test('env example has openai api key placeholder', function () {
    $envExample = file_get_contents(base_path('.env.example'));

    expect($envExample)->toContain('OPENAI_API_KEY=');
});
