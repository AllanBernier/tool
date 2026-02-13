<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\ToolController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('admin.')->group(function () {
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::post('categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');

    Route::resource('tags', TagController::class)->except(['show']);

    Route::resource('outils', ToolController::class)->parameters(['outils' => 'tool']);
    Route::post('outils/{tool}/toggle-publish', [ToolController::class, 'togglePublish'])->name('tools.toggle-publish');
});
