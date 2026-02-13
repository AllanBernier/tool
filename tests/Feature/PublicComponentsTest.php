<?php

$publicComponents = [
    'ToolCard',
    'ComparisonCard',
    'CategoryCard',
    'TagBadge',
    'PlatformBadge',
    'PricingTable',
    'FaqAccordion',
    'ProsCons',
    'MarkdownContent',
];

foreach ($publicComponents as $component) {
    test("{$component}.vue component file exists", function () use ($component) {
        expect(resource_path("js/components/public/{$component}.vue"))->toBeFile();
    });
}

test('models types file exists with required type exports', function () {
    $typesFile = file_get_contents(resource_path('js/types/models.ts'));

    expect($typesFile)
        ->toContain('export type Tool')
        ->toContain('export type Comparison')
        ->toContain('export type Category')
        ->toContain('export type Tag')
        ->toContain('export type PricingPlan')
        ->toContain('export type FaqItem');
});

test('types index re-exports models', function () {
    $indexFile = file_get_contents(resource_path('js/types/index.ts'));

    expect($indexFile)->toContain('./models');
});

test('markdown-it package is installed', function () {
    $packageJson = json_decode(file_get_contents(base_path('package.json')), true);

    expect($packageJson['dependencies'])->toHaveKey('markdown-it');
});

test('dompurify package is installed', function () {
    $packageJson = json_decode(file_get_contents(base_path('package.json')), true);

    expect($packageJson['dependencies'])->toHaveKey('dompurify');
});

test('tailwindcss typography plugin is installed', function () {
    $packageJson = json_decode(file_get_contents(base_path('package.json')), true);

    expect($packageJson['dependencies'])->toHaveKey('@tailwindcss/typography');
});

test('typography plugin is loaded in app.css', function () {
    $css = file_get_contents(resource_path('css/app.css'));

    expect($css)->toContain('@tailwindcss/typography');
});

test('ToolCard component uses Link, TagBadge, PlatformBadge, and Badge', function () {
    $content = file_get_contents(resource_path('js/components/public/ToolCard.vue'));

    expect($content)
        ->toContain("from '@inertiajs/vue3'")
        ->toContain('TagBadge')
        ->toContain('PlatformBadge')
        ->toContain('is_sponsored')
        ->toContain('/outil/')
        ->toContain('tool.slug');
});

test('ComparisonCard component displays tool logos and VS text', function () {
    $content = file_get_contents(resource_path('js/components/public/ComparisonCard.vue'));

    expect($content)
        ->toContain("from '@inertiajs/vue3'")
        ->toContain('tool_a')
        ->toContain('tool_b')
        ->toContain('VS')
        ->toContain('/comparatif/')
        ->toContain('comparison.slug');
});

test('CategoryCard component uses dynamic Lucide icon and tool count', function () {
    $content = file_get_contents(resource_path('js/components/public/CategoryCard.vue'));

    expect($content)
        ->toContain("from '@inertiajs/vue3'")
        ->toContain('lucide-vue-next')
        ->toContain('tools_count')
        ->toContain('/categorie/')
        ->toContain('category.slug');
});

test('TagBadge component links to tag page', function () {
    $content = file_get_contents(resource_path('js/components/public/TagBadge.vue'));

    expect($content)
        ->toContain("from '@inertiajs/vue3'")
        ->toContain('/tag/')
        ->toContain('tag.slug');
});

test('PlatformBadge component supports all 5 platform types', function () {
    $content = file_get_contents(resource_path('js/components/public/PlatformBadge.vue'));

    expect($content)
        ->toContain('web')
        ->toContain('desktop')
        ->toContain('mobile')
        ->toContain('api')
        ->toContain('cli');
});

test('PricingTable component handles free and paid plans', function () {
    $content = file_get_contents(resource_path('js/components/public/PricingTable.vue'));

    expect($content)
        ->toContain('is_popular')
        ->toContain('Gratuit')
        ->toContain('Freemium')
        ->toContain('Payant');
});

test('FaqAccordion component uses Reka UI Collapsible', function () {
    $content = file_get_contents(resource_path('js/components/public/FaqAccordion.vue'));

    expect($content)
        ->toContain('Collapsible')
        ->toContain('CollapsibleContent')
        ->toContain('CollapsibleTrigger')
        ->toContain('question')
        ->toContain('answer');
});

test('ProsCons component displays pros with check and cons with X icons', function () {
    $content = file_get_contents(resource_path('js/components/public/ProsCons.vue'));

    expect($content)
        ->toContain("from 'lucide-vue-next'")
        ->toContain('Check')
        ->toContain('Avantages')
        ->toContain('Inconvénients');
});

test('MarkdownContent component uses markdown-it and DOMPurify for XSS safety', function () {
    $content = file_get_contents(resource_path('js/components/public/MarkdownContent.vue'));

    expect($content)
        ->toContain('markdown-it')
        ->toContain('dompurify')
        ->toContain('DOMPurify.sanitize')
        ->toContain('prose');
});

test('frontend build compiles successfully', function () {
    $result = exec('cd '.base_path().' && npm run build 2>&1', $output, $exitCode);

    expect($exitCode)->toBe(0);
});
