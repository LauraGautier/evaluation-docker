<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Inertia\Inertia;
use Illuminate\Auth\Middleware\Authenticate;
use Laravel\Jetstream\Http\Middleware\AuthenticateSession;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use App\Http\Controllers\{
    TaskController,
    ProjectController,
    ObjectiveController,
    DashboardController
};
use App\Http\Middleware\CheckRole;

// ------------------
// 🌐 Routes publiques
// ------------------

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

// -----------------------------
// 🔐 Routes protégées (auth + verified)
// -----------------------------

Route::middleware([
    Authenticate::class,
    AuthenticateSession::class,
    EnsureEmailIsVerified::class,
])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // ----------------------
    // 📌 Tâches (accessibles à tous les rôles connectés)
    // ----------------------

    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{task}/start', [TaskController::class, 'startTask'])->name('tasks.start');
    Route::post('/tasks/{task}/complete', [TaskController::class, 'completeTask'])->name('tasks.complete');

    // ----------------------
    // 📁 Projets (accès général en lecture seule)
    // ----------------------

    Route::resource('projects', ProjectController::class)->only([
        'index', 'show', 'edit', 'create'
    ]);

    // ----------------------
    // 📈 KPI Collaborateur (individuel)
    // ----------------------

    Route::get('/collaborateur/{collaborateur}/kpi', [TaskController::class, 'collaborateurKpi'])->name('collaborateur.kpi');
    Route::get('/my-kpi', [TaskController::class, 'collaborateurKpi'])->name('my.kpi');

    Route::get('/collaborateur/{collaborateur}/kpi/pdf', [TaskController::class, 'exportCollaborateurKpiPdf'])->name('collaborateur.kpi.pdf');
    Route::get('/my-kpi/pdf', [TaskController::class, 'exportCollaborateurKpiPdf'])->name('my.kpi.pdf');

    // ----------------------
    // 📬 Test Email
    // ----------------------

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

    // ----------------------
    // 👑 Routes administrateur
    // ----------------------

    Route::middleware([CheckRole::class.':administrateur'])->group(function () {
        Route::get('/admin/dashboard', function () {
            return Inertia::render('Admin/Dashboard');
        })->name('admin.dashboard');
    });

    // ----------------------
    // 🧑‍💼 Routes manager
    // ----------------------

    Route::middleware([CheckRole::class.':manager'])->group(function () {

        Route::get('/manager/dashboard', [DashboardController::class, 'managerDashboard'])->name('manager.dashboard');

        // KPI équipe
        Route::get('/manager/team-kpi', [TaskController::class, 'teamKpi'])->name('manager.team.kpi');
        Route::get('/manager/team-kpi/pdf', [TaskController::class, 'exportTeamKpiPdf'])->name('manager.team.kpi.pdf');

        // Projets manager (création / mise à jour / suppression)
        Route::resource('projects', ProjectController::class)->except([
            'index', 'show'
        ]);

        // Gestion des tâches
        Route::get('/manager/tasks', [TaskController::class, 'managerTasks'])->name('manager.tasks');
        Route::get('/manager/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
        Route::post('/manager/tasks', [TaskController::class, 'store'])->name('tasks.store');

        // Objectifs liés à un projet
        Route::post('/projects/{project}/objectives', [ObjectiveController::class, 'store'])->name('objectives.store');
        Route::put('/objectives/{objective}', [ObjectiveController::class, 'update'])->name('objectives.update');
        Route::delete('/objectives/{objective}', [ObjectiveController::class, 'destroy'])->name('objectives.destroy');
        Route::post('/objectives/{objective}/toggle', [ObjectiveController::class, 'toggleCompletion'])->name('objectives.toggle');
    });

    // ----------------------
    // 👤 Routes collaborateur
    // ----------------------

    Route::middleware([CheckRole::class.':collaborateur'])->group(function () {
        Route::get('/collaborateur/dashboard', [DashboardController::class, 'collaborateurDashboard'])
        ->middleware([CheckRole::class.':collaborateur'])
        ->name('collaborateur.dashboard');

        Route::get('/collaborateur/tasks', [TaskController::class, 'collaborateurTasks'])->name('collaborateur.tasks');
    });
});
