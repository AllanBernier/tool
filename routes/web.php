<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', HomeController::class)->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::get('dashboard/comparatifs', function () {
        return Inertia::render('dashboard/Comparatifs');
    })->name('dashboard.comparatifs');
});

require __DIR__.'/settings.php';
require __DIR__.'/admin.php';
