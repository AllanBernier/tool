<?php

use App\Enums\GenerationStatus;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Tool;

test('factory creates a valid tool', function () {
    $tool = Tool::factory()->create();

    expect($tool)->toBeInstanceOf(Tool::class)
        ->and($tool->id)->not->toBeEmpty()
        ->and($tool->name)->not->toBeEmpty()
        ->and($tool->slug)->not->toBeEmpty()
        ->and($tool->url)->not->toBeEmpty()
        ->and($tool->category_id)->not->toBeEmpty();
});

test('factory published state sets correct attributes', function () {
    $tool = Tool::factory()->published()->create();

    expect($tool->is_published)->toBeTrue()
        ->and($tool->published_at)->not->toBeNull()
        ->and($tool->generation_status)->toBe(GenerationStatus::Completed);
});

test('factory sponsored state sets correct attributes', function () {
    $tool = Tool::factory()->sponsored()->create();

    expect($tool->is_sponsored)->toBeTrue()
        ->and($tool->sponsored_until)->not->toBeNull()
        ->and($tool->sponsored_until->isFuture())->toBeTrue();
});

test('slug is auto-generated from name', function () {
    $tool = Tool::factory()->create(['name' => 'Visual Studio Code']);

    expect($tool->slug)->toBe('visual-studio-code');
});

test('slug auto-generates when not provided', function () {
    $tool = Tool::factory()->create(['name' => 'My Awesome Tool']);

    expect($tool->slug)->toBe('my-awesome-tool');
});

test('slugs are unique', function () {
    Tool::factory()->create(['name' => 'Unique Tool', 'slug' => 'unique-tool']);

    expect(fn () => Tool::factory()->create(['slug' => 'unique-tool']))
        ->toThrow(\Illuminate\Database\QueryException::class);
});

test('category relationship returns BelongsTo', function () {
    $tool = Tool::factory()->create();

    expect($tool->category)->toBeInstanceOf(Category::class);
});

test('tags relationship returns BelongsToMany', function () {
    $tool = Tool::factory()->create();
    $tags = Tag::factory(3)->create();

    $tool->tags()->attach($tags);

    expect($tool->tags)->toHaveCount(3)
        ->each->toBeInstanceOf(Tag::class);
});

test('alternatives relationship returns BelongsToMany of tools', function () {
    $tool = Tool::factory()->create();
    $alternatives = Tool::factory(2)->create();

    $tool->alternatives()->attach($alternatives);

    expect($tool->alternatives)->toHaveCount(2)
        ->each->toBeInstanceOf(Tool::class);
});

test('published scope filters published tools', function () {
    Tool::factory()->published()->create();
    Tool::factory()->create(['is_published' => false]);

    $published = Tool::published()->get();

    expect($published)->toHaveCount(1)
        ->and($published->first()->is_published)->toBeTrue();
});

test('sponsored scope filters active sponsored tools', function () {
    Tool::factory()->sponsored()->create();
    Tool::factory()->create(['is_sponsored' => true, 'sponsored_until' => now()->subDay()]);
    Tool::factory()->create(['is_sponsored' => false]);

    $sponsored = Tool::sponsored()->get();

    expect($sponsored)->toHaveCount(1)
        ->and($sponsored->first()->is_sponsored)->toBeTrue();
});

test('pricing cast returns array', function () {
    $tool = Tool::factory()->create(['pricing' => [['plan' => 'Free', 'price' => 0]]]);

    $tool->refresh();

    expect($tool->pricing)->toBeArray()
        ->and($tool->pricing[0]['plan'])->toBe('Free');
});

test('generation_status cast returns enum', function () {
    $tool = Tool::factory()->create(['generation_status' => 'pending']);

    $tool->refresh();

    expect($tool->generation_status)->toBe(GenerationStatus::Pending);
});

test('json fields cast to arrays', function () {
    $tool = Tool::factory()->create([
        'pros' => ['Fast', 'Free'],
        'cons' => ['Limited'],
        'features' => ['Editor', 'Debugger'],
        'faq' => [['question' => 'Why?', 'answer' => 'Because.']],
        'platforms' => ['windows', 'macos'],
    ]);

    $tool->refresh();

    expect($tool->pros)->toBeArray()
        ->and($tool->cons)->toBeArray()
        ->and($tool->features)->toBeArray()
        ->and($tool->faq)->toBeArray()
        ->and($tool->platforms)->toBeArray();
});

test('boolean fields are cast correctly', function () {
    $tool = Tool::factory()->create(['is_published' => true, 'is_sponsored' => false]);

    $tool->refresh();

    expect($tool->is_published)->toBeBool()->toBeTrue()
        ->and($tool->is_sponsored)->toBeBool()->toBeFalse();
});

test('datetime fields are cast correctly', function () {
    $now = now();
    $tool = Tool::factory()->create([
        'generated_at' => $now,
        'published_at' => $now,
        'sponsored_until' => $now,
    ]);

    $tool->refresh();

    expect($tool->generated_at)->toBeInstanceOf(\Carbon\CarbonImmutable::class)
        ->and($tool->published_at)->toBeInstanceOf(\Carbon\CarbonImmutable::class)
        ->and($tool->sponsored_until)->toBeInstanceOf(\Carbon\CarbonImmutable::class);
});

test('tool uses ulid as primary key', function () {
    $tool = Tool::factory()->create();

    expect($tool->id)->toHaveLength(26);
});

test('route key name is slug', function () {
    $tool = Tool::factory()->create();

    expect($tool->getRouteKeyName())->toBe('slug');
});

test('logo url accessor returns placeholder when no logo path', function () {
    $tool = Tool::factory()->create(['logo_path' => null]);

    expect($tool->logo_url)->toBe('/images/placeholder-logo.svg');
});

test('logo url accessor returns storage url when logo path exists', function () {
    $tool = Tool::factory()->create(['logo_path' => 'vscode.png']);

    expect($tool->logo_url)->toContain('vscode.png');
});
