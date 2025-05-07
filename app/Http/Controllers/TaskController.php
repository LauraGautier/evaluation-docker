<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TaskController extends Controller
{
    // Pour les managers - afficher et gérer toutes les tâches
    public function managerTasks(Request $request)
    {
        $user = $request->user();
        $team = $user->currentTeam;

        $tasks = Task::where('team_id', $team->id)
            ->with(['assignedTo:id,name,email', 'creator:id,name'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Manager/Tasks', [
            'tasks' => $tasks,
            'team' => $team,
        ]);
    }

    public function collaborateurTasks(Request $request)
    {
        $user = $request->user();

        // Récupérer les IDs de toutes les équipes auxquelles l'utilisateur appartient
        $teamIds = $user->allTeams()->pluck('id')->toArray();

        // Récupérer toutes les tâches assignées à l'utilisateur quelle que soit l'équipe
        $tasks = Task::whereIn('team_id', $teamIds)
            ->where('assigned_to', $user->id)
            ->with(['creator:id,name', 'team:id,name'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Collaborateur/Tasks', [
            'tasks' => $tasks,
            'teams' => $user->allTeams()
        ]);
    }

    // Création de tâche - uniquement pour les managers
    public function create(Request $request)
    {
        $team = $request->user()->currentTeam;

        // Récupérer uniquement les membres de type collaborateur de cette équipe
        $teamMembers = $team->users
            ->filter(function ($user) {
                return $user->role === 'collaborateur';
            })
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ];
            });

        return Inertia::render('Manager/TaskCreate', [
            'team' => $team,
            'teamMembers' => $teamMembers,
        ]);
    }

    // Enregistrement d'une nouvelle tâche
    public function store(Request $request)
    {
        $user = $request->user();
        $team = $user->currentTeam;

        // Valider que l'utilisateur est manager
        if ($user->role !== 'manager') {
            return redirect()->back()->with('error', 'Seuls les managers peuvent créer des tâches.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:users,id',
        ]);

        $task = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'team_id' => $team->id,
            'assigned_to' => $validated['assigned_to'],
            'created_by' => $user->id,
            'status' => 'pending',
        ]);

        return redirect()->route('manager.tasks')->with('success', 'Tâche créée avec succès');
    }

    // Afficher les détails d'une tâche (pour les deux rôles)
    public function show(Task $task)
    {
        $user = auth()->user();
        $this->authorize('view', $task);

        $task->load(['assignedTo:id,name,email', 'creator:id,name']);

        return Inertia::render('Tasks/Show', [
            'task' => $task,
            'canEdit' => $user->role === 'manager',
        ]);
    }

    // Démarrer une tâche (pour les collaborateurs)
    public function startTask(Task $task)
    {
        $user = auth()->user();
        $this->authorize('start', $task);

        if ($task->isStarted()) {
            return redirect()->back()->with('error', 'Cette tâche est déjà démarrée');
        }

        $task->update([
            'status' => 'in_progress',
            'start_time' => now(),
        ]);

        return redirect()->back()->with('success', 'Tâche démarrée avec succès');
    }

    // Terminer une tâche (pour les collaborateurs)
    public function completeTask(Task $task)
    {
        $user = auth()->user();
        $this->authorize('complete', $task);

        if ($task->isCompleted()) {
            return redirect()->back()->with('error', 'Cette tâche est déjà terminée');
        }

        // Si la tâche n'a pas été démarrée, on définit le temps de démarrage à maintenant
        if (!$task->start_time) {
            $task->start_time = now();
        }

        $task->update([
            'status' => 'completed',
            'end_time' => now(),
        ]);

        return redirect()->back()->with('success', 'Tâche terminée avec succès');
    }
}
