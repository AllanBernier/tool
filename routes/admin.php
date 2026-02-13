<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ComparisonController;
use App\Http\Controllers\Admin\GenerationController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\ToolController;
use App\Http\Middleware\DisableSsrForAdmin;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', DisableSsrForAdmin::class])->prefix('dashboard')->name('admin.')->group(function () {
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::post('categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');

    Route::resource('tags', TagController::class)->except(['show']);

    Route::resource('outils', ToolController::class)->parameters(['outils' => 'tool']);
    Route::post('outils/{tool}/toggle-publish', [ToolController::class, 'togglePublish'])->name('tools.toggle-publish');

    Route::resource('comparatifs', ComparisonController::class)->parameters(['comparatifs' => 'comparison'])->except(['show']);
    Route::post('comparatifs/{comparison}/toggle-publish', [ComparisonController::class, 'togglePublish'])->name('comparisons.toggle-publish');

    Route::post('outils/{tool}/generate', [GenerationController::class, 'generateTool'])->name('generate.tool');
    Route::post('comparatifs/{comparison}/generate', [GenerationController::class, 'generateComparison'])->name('generate.comparison');
    Route::post('outils/{tool}/suggest-alternatives', [GenerationController::class, 'suggestAlternatives'])->name('generate.alternatives');
    Route::post('outils/{tool}/fetch-logo', [GenerationController::class, 'fetchLogo'])->name('generate.logo');
});
