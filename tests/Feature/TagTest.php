<?php

use App\Models\Tag;

test('factory creates a valid tag', function () {
    $tag = Tag::factory()->create();

    expect($tag)->toBeInstanceOf(Tag::class)
        ->and($tag->id)->not->toBeEmpty()
        ->and($tag->name)->not->toBeEmpty()
        ->and($tag->slug)->not->toBeEmpty();
});

test('slug is auto-generated from name', function () {
    $tag = Tag::factory()->create(['name' => 'Machine Learning']);

    expect($tag->slug)->toBe('machine-learning');
});

test('slug auto-generates when not provided', function () {
    $tag = Tag::factory()->create(['name' => 'Open Source']);

    expect($tag->slug)->toBe('open-source');
});

test('slugs are unique', function () {
    Tag::factory()->create(['name' => 'Unique Tag', 'slug' => 'unique-tag']);

    expect(fn () => Tag::factory()->create(['slug' => 'unique-tag']))
        ->toThrow(\Illuminate\Database\QueryException::class);
});

test('tools relationship is defined as belongsToMany', function () {
    $tag = new Tag;

    $reflection = new ReflectionMethod($tag, 'tools');

    expect($reflection->getReturnType()?->getName())
        ->toBe(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class);
});

test('tag uses ulid as primary key', function () {
    $tag = Tag::factory()->create();

    expect($tag->id)->toHaveLength(26);
});

test('route key name is slug', function () {
    $tag = Tag::factory()->create();

    expect($tag->getRouteKeyName())->toBe('slug');
});
