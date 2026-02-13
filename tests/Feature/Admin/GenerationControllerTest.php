<?php

use App\Enums\GenerationStatus;
use App\Jobs\FetchToolLogo;
use App\Jobs\GenerateComparisonContent;
use App\Jobs\GenerateToolContent;
use App\Jobs\SuggestAlternativesAndComparisons;
use App\Models\Comparison;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

test('generateTool dispatches GenerateToolContent job', function () {
    Queue::fake();

    $user = User::factory()->create();
    $tool = Tool::factory()->create(['generation_status' => GenerationStatus::Pending]);

    $response = $this->actingAs($user)->post(route('admin.generate.tool', $tool));

    Queue::assertPushed(GenerateToolContent::class, fn ($job) => $job->tool->id === $tool->id);
    $response->assertRedirect();
    $response->assertSessionHas('success');
});

test('generateTool prevents dispatch when status is generating', function () {
    Queue::fake();

    $user = User::factory()->create();
    $tool = Tool::factory()->create(['generation_status' => GenerationStatus::Generating]);

    $response = $this->actingAs($user)->post(route('admin.generate.tool', $tool));

    Queue::assertNotPushed(GenerateToolContent::class);
    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('generateComparison dispatches GenerateComparisonContent job', function () {
    Queue::fake();

    $user = User::factory()->create();
    $comparison = Comparison::factory()->create(['generation_status' => GenerationStatus::Pending]);

    $response = $this->actingAs($user)->post(route('admin.generate.comparison', $comparison));

    Queue::assertPushed(GenerateComparisonContent::class, fn ($job) => $job->comparison->id === $comparison->id);
    $response->assertRedirect();
    $response->assertSessionHas('success');
});

test('generateComparison prevents dispatch when status is generating', function () {
    Queue::fake();

    $user = User::factory()->create();
    $comparison = Comparison::factory()->create(['generation_status' => GenerationStatus::Generating]);

    $response = $this->actingAs($user)->post(route('admin.generate.comparison', $comparison));

    Queue::assertNotPushed(GenerateComparisonContent::class);
    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('suggestAlternatives dispatches SuggestAlternativesAndComparisons job', function () {
    Queue::fake();

    $user = User::factory()->create();
    $tool = Tool::factory()->create();

    $response = $this->actingAs($user)->post(route('admin.generate.alternatives', $tool));

    Queue::assertPushed(SuggestAlternativesAndComparisons::class, fn ($job) => $job->tool->id === $tool->id);
    $response->assertRedirect();
    $response->assertSessionHas('success');
});

test('fetchLogo dispatches FetchToolLogo job', function () {
    Queue::fake();

    $user = User::factory()->create();
    $tool = Tool::factory()->create();

    $response = $this->actingAs($user)->post(route('admin.generate.logo', $tool));

    Queue::assertPushed(FetchToolLogo::class, fn ($job) => $job->tool->id === $tool->id);
    $response->assertRedirect();
    $response->assertSessionHas('success');
});

test('unauthenticated access to generateTool is redirected', function () {
    $tool = Tool::factory()->create();

    $response = $this->post(route('admin.generate.tool', $tool));

    $response->assertRedirect(route('login'));
});

test('unauthenticated access to generateComparison is redirected', function () {
    $comparison = Comparison::factory()->create();

    $response = $this->post(route('admin.generate.comparison', $comparison));

    $response->assertRedirect(route('login'));
});

test('unauthenticated access to suggestAlternatives is redirected', function () {
    $tool = Tool::factory()->create();

    $response = $this->post(route('admin.generate.alternatives', $tool));

    $response->assertRedirect(route('login'));
});

test('unauthenticated access to fetchLogo is redirected', function () {
    $tool = Tool::factory()->create();

    $response = $this->post(route('admin.generate.logo', $tool));

    $response->assertRedirect(route('login'));
});
