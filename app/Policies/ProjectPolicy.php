<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        // Tous les utilisateurs peuvent voir la liste des projets de leur équipe
        return true;
    }

    public function view(User $user, Project $project)
    {
        // Les utilisateurs peuvent voir les projets de leur équipe
        return $user->belongsToTeam($project->team);
    }

    public function create(User $user)
    {
        // Seuls les managers peuvent créer des projets
        return $user->role === 'manager';
    }

    public function update(User $user, Project $project)
    {
        // Seuls les managers peuvent modifier des projets
        return $user->role === 'manager' && $user->belongsToTeam($project->team);
    }

    public function delete(User $user, Project $project)
    {
        // Seuls les managers peuvent supprimer des projets
        return $user->role === 'manager' && $user->belongsToTeam($project->team);
    }
}
