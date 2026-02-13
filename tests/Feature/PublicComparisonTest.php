<?php

use App\Models\Comparison;
use App\Models\Tool;

test('show displays a published comparison', function () {
    $toolA = Tool::factory()->published()->create();
    $toolB = Tool::factory()->published()->create();
    $comparison = Comparison::factory()->published()->create([
        'tool_a_id' => $toolA->id,
        'tool_b_id' => $toolB->id,
    ]);

    $response = $this->get("/comparatif/{$comparison->slug}");

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Public/Comparisons/Show')
            ->has('comparison', fn ($prop) => $prop
                ->where('slug', $comparison->slug)
                ->has('tool_a')
                ->has('tool_b')
                ->etc()
            )
        );
});

test('show returns 404 for unpublished comparison', function () {
    $comparison = Comparison::factory()->create(['is_published' => false]);

    $response = $this->get("/comparatif/{$comparison->slug}");

    $response->assertNotFound();
});

test('show returns 404 for non-existent slug', function () {
    $response = $this->get('/comparatif/non-existent-comparison');

    $response->assertNotFound();
});

test('index lists published comparisons', function () {
    Comparison::factory()->published()->count(3)->create();
    Comparison::factory()->create(['is_published' => false]);

    $response = $this->get('/comparatifs');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Public/Comparisons/Index')
            ->has('comparisons.data', 3)
        );
});
