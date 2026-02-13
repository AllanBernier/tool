<?php

use App\Models\Category;

test('home page shares footer categories', function () {
    Category::factory()->create(['name' => 'Frontend', 'sort_order' => 2]);
    Category::factory()->create(['name' => 'Backend', 'sort_order' => 1]);

    $response = $this->get('/');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('footerCategories', 2)
            ->where('footerCategories.0.name', 'Backend')
            ->where('footerCategories.0.slug', 'backend')
            ->where('footerCategories.1.name', 'Frontend')
        );
});

test('footer categories are ordered by sort_order', function () {
    Category::factory()->create(['name' => 'Z Category', 'sort_order' => 1]);
    Category::factory()->create(['name' => 'A Category', 'sort_order' => 3]);
    Category::factory()->create(['name' => 'M Category', 'sort_order' => 2]);

    $response = $this->get('/');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('footerCategories.0.name', 'Z Category')
            ->where('footerCategories.1.name', 'M Category')
            ->where('footerCategories.2.name', 'A Category')
        );
});

test('footer categories only return name and slug fields', function () {
    Category::factory()->create(['name' => 'Testing', 'description' => 'A test category']);

    $response = $this->get('/');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('footerCategories', 1)
            ->has('footerCategories.0', fn ($category) => $category
                ->has('name')
                ->has('slug')
                ->missing('description')
                ->missing('icon')
                ->missing('meta_title')
                ->missing('meta_description')
            )
        );
});

test('footer categories are shared on authenticated pages too', function () {
    $user = \App\Models\User::factory()->create();
    Category::factory()->create(['name' => 'DevOps']);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('footerCategories', 1)
            ->where('footerCategories.0.name', 'DevOps')
        );
});
