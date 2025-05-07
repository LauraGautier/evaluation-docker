<?php

use App\Http\Controllers\TaskController;
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

Route::get('/acces-refuse', function () {
    return Inertia::render('AccessDenied');
})->name('access.denied');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Routes communes pour voir une tâche spécifique
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');

    // Routes pour les actions sur les tâches
    Route::post('/tasks/{task}/start', [TaskController::class, 'startTask'])->name('tasks.start');
    Route::post('/tasks/{task}/complete', [TaskController::class, 'completeTask'])->name('tasks.complete');

    // Routes pour administrateur
    Route::middleware([CheckRole::class.':administrateur'])->group(function () {
        Route::get('/admin/dashboard', function () {
            return Inertia::render('Admin/Dashboard');
        })->name('admin.dashboard');
    });

    // Routes pour manager
    Route::middleware([CheckRole::class.':manager'])->group(function () {
        // Dashboard du manager
        Route::get('/manager/dashboard', function () {
            return Inertia::render('Manager/Dashboard');
        })->name('manager.dashboard');

        // Liste des tâches pour le manager - utilise maintenant le contrôleur
        Route::get('/manager/tasks', [TaskController::class, 'managerTasks'])->name('manager.tasks');

        // Création de tâche
        Route::get('/manager/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
        Route::post('/manager/tasks', [TaskController::class, 'store'])->name('tasks.store');
    });

    // Routes pour collaborateur
    Route::middleware([CheckRole::class.':collaborateur'])->group(function () {
        // Dashboard du collaborateur
        Route::get('/collaborateur/dashboard', function () {
            return Inertia::render('Collaborateur/Dashboard');
        })->name('collaborateur.dashboard');

        // Liste des tâches pour le collaborateur - utilise maintenant le contrôleur
        Route::get('/collaborateur/tasks', [TaskController::class, 'collaborateurTasks'])->name('collaborateur.tasks');
    });

    Route::get('/test-email', function () {
        try {
            Mail::raw('Test d\'envoi d\'email via Mailtrap', function ($message) {
                $message->to('lc.gautier@icloud.com')
                        ->subject('Test Mailtrap Configuration');
            });
            return 'Email envoyé avec succès, vérifiez votre boîte Mailtrap';
        } catch (\Exception $e) {
            return 'Erreur lors de l\'envoi de l\'email: ' . $e->getMessage();
        }
    });
});
