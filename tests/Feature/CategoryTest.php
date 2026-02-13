<?php

use App\Models\Category;

test('factory creates a valid category', function () {
    $category = Category::factory()->create();

    expect($category)->toBeInstanceOf(Category::class)
        ->and($category->id)->not->toBeEmpty()
        ->and($category->name)->not->toBeEmpty()
        ->and($category->slug)->not->toBeEmpty()
        ->and($category->description)->not->toBeEmpty()
        ->and($category->icon)->not->toBeEmpty();
});

test('slug is auto-generated from name', function () {
    $category = Category::factory()->create(['name' => 'IDE & Éditeurs de code']);

    expect($category->slug)->toBe('ide-editeurs-de-code');
});

test('slug auto-generates when not provided', function () {
    $category = Category::factory()->create(['name' => 'My Test Category']);

    expect($category->slug)->toBe('my-test-category');
});

test('slugs are unique', function () {
    Category::factory()->create(['name' => 'Unique Category', 'slug' => 'unique-category']);

    expect(fn () => Category::factory()->create(['slug' => 'unique-category']))
        ->toThrow(\Illuminate\Database\QueryException::class);
});

test('tools relationship is defined as hasMany', function () {
    $category = new Category;

    $reflection = new ReflectionMethod($category, 'tools');

    expect($reflection->getReturnType()?->getName())
        ->toBe(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

test('category uses ulid as primary key', function () {
    $category = Category::factory()->create();

    expect($category->id)->toHaveLength(26);
});

test('route key name is slug', function () {
    $category = Category::factory()->create();

    expect($category->getRouteKeyName())->toBe('slug');
});

test('seeder creates 15 predefined categories', function () {
    $this->seed(\Database\Seeders\CategorySeeder::class);

    expect(Category::count())->toBe(15);

    $expectedNames = [
        'IDE & Éditeurs de code',
        'CI/CD & DevOps',
        'Hébergement & Cloud',
        'Bases de données',
        'Monitoring & Observabilité',
        'Gestion de projet',
        'Communication & Collaboration',
        'Design & Prototypage',
        'Outils IA & Assistants de code',
        'Sécurité & Tests',
        'API & Backend',
        'Frontend & Frameworks',
        'Open Source',
        'No-Code / Low-Code',
        'Documentation & Knowledge',
    ];

    foreach ($expectedNames as $name) {
        expect(Category::where('name', $name)->exists())->toBeTrue("Category '{$name}' should exist");
    }
});

test('seeder assigns sort order to each category', function () {
    $this->seed(\Database\Seeders\CategorySeeder::class);

    $categories = Category::orderBy('sort_order')->get();

    expect($categories->first()->sort_order)->toBe(1)
        ->and($categories->last()->sort_order)->toBe(15);
});

test('seeder assigns icons to each category', function () {
    $this->seed(\Database\Seeders\CategorySeeder::class);

    $categories = Category::all();

    foreach ($categories as $category) {
        expect($category->icon)->not->toBeEmpty("Category '{$category->name}' should have an icon");
    }
});

test('seeder generates slugs for each category', function () {
    $this->seed(\Database\Seeders\CategorySeeder::class);

    $categories = Category::all();

    foreach ($categories as $category) {
        expect($category->slug)->not->toBeEmpty("Category '{$category->name}' should have a slug");
    }
});
