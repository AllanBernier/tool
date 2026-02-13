<?php

use App\Models\User;

test('guests are redirected to login for admin sections', function (string $route) {
    $this->get(route($route))->assertRedirect(route('login'));
})->with([
    'outils' => 'admin.outils.index',
    'categories' => 'admin.categories.index',
    'tags' => 'admin.tags.index',
    'comparatifs' => 'dashboard.comparatifs',
]);

test('authenticated users can visit admin sections', function (string $route) {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route($route))
        ->assertOk();
})->with([
    'dashboard' => 'dashboard',
    'outils' => 'admin.outils.index',
    'categories' => 'admin.categories.index',
    'tags' => 'admin.tags.index',
    'comparatifs' => 'dashboard.comparatifs',
]);

test('admin routes have correct URLs', function (string $route, string $expectedUrl) {
    expect(route($route, absolute: false))->toBe($expectedUrl);
})->with([
    'dashboard' => ['dashboard', '/dashboard'],
    'outils' => ['admin.outils.index', '/dashboard/outils'],
    'categories' => ['admin.categories.index', '/dashboard/categories'],
    'tags' => ['admin.tags.index', '/dashboard/tags'],
    'comparatifs' => ['dashboard.comparatifs', '/dashboard/comparatifs'],
]);
