<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ObjectiveController;
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

    Route::resource('projects', ProjectController::class)
    ->only(['index', 'show', 'edit', 'create']);

    Route::get('/collaborateur/{collaborateur}/kpi', [TaskController::class, 'collaborateurKpi'])
    ->name('collaborateur.kpi');
    Route::get('/my-kpi', [TaskController::class, 'collaborateurKpi'])
    ->name('my.kpi');

    Route::get('/collaborateur/{collaborateur}/kpi/pdf', [TaskController::class, 'exportCollaborateurKpiPdf'])
    ->name('collaborateur.kpi.pdf');
    Route::get('/my-kpi/pdf', [TaskController::class, 'exportCollaborateurKpiPdf'])
    ->name('my.kpi.pdf');

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

        Route::get('/manager/team-kpi', [TaskController::class, 'teamKpi'])
        ->name('manager.team.kpi');

        Route::get('/manager/team-kpi/pdf', [TaskController::class, 'exportTeamKpiPdf'])
        ->name('manager.team.kpi.pdf');

        Route::resource('projects', ProjectController::class)
        ->except(['index', 'show']);

        // Liste des tâches pour le manager - utilise maintenant le contrôleur
        Route::get('/manager/tasks', [TaskController::class, 'managerTasks'])->name('manager.tasks');

        // Création de tâche
        Route::get('/manager/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
        Route::post('/manager/tasks', [TaskController::class, 'store'])->name('tasks.store');

        Route::post('/projects/{project}/objectives', [ObjectiveController::class, 'store'])->name('objectives.store');
        Route::put('/objectives/{objective}', [ObjectiveController::class, 'update'])->name('objectives.update');
        Route::delete('/objectives/{objective}', [ObjectiveController::class, 'destroy'])->name('objectives.destroy');
        Route::post('/objectives/{objective}/toggle', [ObjectiveController::class, 'toggleCompletion'])->name('objectives.toggle');
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
