<?php

use App\Models\User;

test('guests are redirected to login for admin sections', function (string $route) {
    $this->get(route($route))->assertRedirect(route('login'));
})->with([
    'outils' => 'dashboard.outils',
    'categories' => 'admin.categories.index',
    'tags' => 'dashboard.tags',
    'comparatifs' => 'dashboard.comparatifs',
]);

test('authenticated users can visit admin sections', function (string $route) {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route($route))
        ->assertOk();
})->with([
    'dashboard' => 'dashboard',
    'outils' => 'dashboard.outils',
    'categories' => 'admin.categories.index',
    'tags' => 'dashboard.tags',
    'comparatifs' => 'dashboard.comparatifs',
]);

test('admin routes have correct URLs', function (string $route, string $expectedUrl) {
    expect(route($route, absolute: false))->toBe($expectedUrl);
})->with([
    'dashboard' => ['dashboard', '/dashboard'],
    'outils' => ['dashboard.outils', '/dashboard/outils'],
    'categories' => ['admin.categories.index', '/dashboard/categories'],
    'tags' => ['dashboard.tags', '/dashboard/tags'],
    'comparatifs' => ['dashboard.comparatifs', '/dashboard/comparatifs'],
]);
