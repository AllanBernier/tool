<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicCategoryController;
use App\Http\Controllers\PublicToolController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', HomeController::class)->name('home');

Route::get('/outils', [PublicToolController::class, 'index'])->name('tools.index');
Route::get('/outil/{tool:slug}', [PublicToolController::class, 'show'])->name('tools.show');

Route::get('/categories', [PublicCategoryController::class, 'index'])->name('categories.index');
Route::get('/categorie/{category:slug}', [PublicCategoryController::class, 'show'])->name('categories.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::get('dashboard/comparatifs', function () {
        return Inertia::render('dashboard/Comparatifs');
    })->name('dashboard.comparatifs');
});

require __DIR__.'/settings.php';
require __DIR__.'/admin.php';
