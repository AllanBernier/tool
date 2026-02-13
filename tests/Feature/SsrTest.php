<?php

test('ssr is enabled in inertia config', function () {
    expect(config('inertia.ssr.enabled'))->toBeTrue();
});

test('ssr bundle exists at expected path', function () {
    $detector = new \Inertia\Ssr\BundleDetector;

    expect($detector->detect())->not->toBeNull();
});

test('ssr entry point file exists', function () {
    expect(file_exists(resource_path('js/ssr.ts')))->toBeTrue();
});

test('vite config includes ssr entry point', function () {
    $viteConfig = file_get_contents(base_path('vite.config.ts'));

    expect($viteConfig)->toContain("ssr: 'resources/js/ssr.ts'");
});

test('app.ts uses createSSRApp for hydration', function () {
    $appTs = file_get_contents(resource_path('js/app.ts'));

    expect($appTs)->toContain('createSSRApp');
    expect($appTs)->not->toContain('createApp');
});

test('ssr.ts uses createSSRApp and renderToString', function () {
    $ssrTs = file_get_contents(resource_path('js/ssr.ts'));

    expect($ssrTs)->toContain('createSSRApp');
    expect($ssrTs)->toContain('renderToString');
    expect($ssrTs)->toContain('createServer');
});

test('admin routes have ssr disabled middleware', function () {
    $user = \App\Models\User::factory()->create();

    $this->actingAs($user)->get(route('dashboard'));

    expect(config('inertia.ssr.enabled'))->toBeFalse();
});

test('public pages do not disable ssr', function () {
    $this->get('/');

    expect(config('inertia.ssr.enabled'))->toBeTrue();
});

test('build:ssr npm script exists in package.json', function () {
    $packageJson = json_decode(file_get_contents(base_path('package.json')), true);

    expect($packageJson['scripts'])->toHaveKey('build:ssr');
    expect($packageJson['scripts']['build:ssr'])->toContain('vite build --ssr');
});
