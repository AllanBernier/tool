<?php

use App\Models\Category;
use App\Models\Comparison;
use App\Models\Tag;
use App\Models\Tool;
use Illuminate\Support\Facades\Cache;

test('sitemap returns valid xml with correct content type', function () {
    $response = $this->get('/sitemap.xml');

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/xml');

    $xml = simplexml_load_string($response->getContent());
    expect($xml)->not->toBeFalse();
    expect($xml->getName())->toBe('urlset');
});

test('sitemap includes homepage with priority 1.0', function () {
    Cache::flush();

    $response = $this->get('/sitemap.xml');
    $content = $response->getContent();

    expect($content)->toContain('<loc>'.route('home').'</loc>');
    expect($content)->toContain('<priority>1.0</priority>');
});

test('sitemap includes published tools with priority 0.7', function () {
    Cache::flush();
    $tool = Tool::factory()->published()->create();

    $response = $this->get('/sitemap.xml');
    $content = $response->getContent();

    expect($content)->toContain('<loc>'.route('tools.show', $tool).'</loc>');
});

test('sitemap excludes unpublished tools', function () {
    Cache::flush();
    $tool = Tool::factory()->create(['is_published' => false]);

    $response = $this->get('/sitemap.xml');
    $content = $response->getContent();

    expect($content)->not->toContain(route('tools.show', $tool));
});

test('sitemap includes categories with priority 0.8', function () {
    Cache::flush();
    $category = Category::factory()->create();

    $response = $this->get('/sitemap.xml');
    $content = $response->getContent();

    expect($content)->toContain('<loc>'.route('categories.show', $category).'</loc>');
});

test('sitemap includes published comparisons with priority 0.7', function () {
    Cache::flush();
    $comparison = Comparison::factory()->published()->create();

    $response = $this->get('/sitemap.xml');
    $content = $response->getContent();

    expect($content)->toContain('<loc>'.route('comparisons.show', $comparison).'</loc>');
});

test('sitemap includes tags with published tools', function () {
    Cache::flush();
    $tag = Tag::factory()->create();
    $tool = Tool::factory()->published()->create();
    $tool->tags()->attach($tag);

    $response = $this->get('/sitemap.xml');
    $content = $response->getContent();

    expect($content)->toContain('<loc>'.route('tags.show', $tag).'</loc>');
});

test('sitemap excludes tags without published tools', function () {
    Cache::flush();
    $tag = Tag::factory()->create();

    $response = $this->get('/sitemap.xml');
    $content = $response->getContent();

    expect($content)->not->toContain(route('tags.show', $tag));
});

test('sitemap includes index pages', function () {
    Cache::flush();

    $response = $this->get('/sitemap.xml');
    $content = $response->getContent();

    expect($content)->toContain('<loc>'.route('tools.index').'</loc>');
    expect($content)->toContain('<loc>'.route('categories.index').'</loc>');
    expect($content)->toContain('<loc>'.route('comparisons.index').'</loc>');
});

test('sitemap is cached', function () {
    Cache::flush();

    $this->get('/sitemap.xml')->assertOk();

    expect(Cache::has('sitemap'))->toBeTrue();
});

test('robots.txt blocks dashboard and admin routes', function () {
    $content = file_get_contents(public_path('robots.txt'));

    expect($content)->toContain('Disallow: /dashboard');
    expect($content)->toContain('Disallow: /admin');
    expect($content)->toContain('Disallow: /settings');
    expect($content)->toContain('Disallow: /login');
    expect($content)->toContain('Disallow: /register');
});

test('robots.txt references sitemap', function () {
    $content = file_get_contents(public_path('robots.txt'));

    expect($content)->toContain('Sitemap:');
    expect($content)->toContain('sitemap.xml');
});

test('paginated tools index includes pagination seo props', function () {
    Tool::factory()->published()->count(20)->create();

    $response = $this->get('/outils?page=1');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('seo.paginationNext')
            ->where('seo.paginationPrev', null)
        );
});

test('paginated tools index page 2 has prev and next links', function () {
    Tool::factory()->published()->count(40)->create();

    $response = $this->get('/outils?page=2');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('seo.paginationPrev', fn ($value) => str_contains($value, '/outils'))
            ->has('seo.paginationNext')
        );
});

test('paginated comparisons index includes pagination seo props', function () {
    Comparison::factory()->published()->count(20)->create();

    $response = $this->get('/comparatifs?page=1');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('seo.paginationNext')
            ->where('seo.paginationPrev', null)
        );
});
