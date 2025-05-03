<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Middleware\CheckRole;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Routes pour administrateur
    Route::middleware([CheckRole::class.':admin'])->group(function () {
        Route::get('/admin/dashboard', function () {
            return Inertia::render('Admin/Dashboard');
        })->name('admin.dashboard');
    });

    // Routes pour manager
    Route::middleware([CheckRole::class.':manager'])->group(function () {
        Route::get('/manager/dashboard', function () {
            return Inertia::render('Manager/Dashboard');
        })->name('manager.dashboard');
    });

    // Routes pour collaborateur
    Route::middleware([CheckRole::class.':collaborateur'])->group(function () {
        Route::get('/collaborateur/dashboard', function () {
            return Inertia::render('Collaborateur/Dashboard');
        })->name('collaborateur.dashboard');
    });
});
