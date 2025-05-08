<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'team_id',
        'created_by',
        'status',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relations
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    // Méthode pour calculer le temps total passé sur le projet (en minutes)
    public function getTotalTimeSpentAttribute()
    {
        return $this->tasks()
            ->whereNotNull('start_time')
            ->whereNotNull('end_time')
            ->get()
            ->sum(function ($task) {
                return $task->end_time->diffInMinutes($task->start_time);
            });
    }

    // Méthode pour formater le temps passé en heures et minutes
    public function getFormattedTimeSpentAttribute()
    {
        $totalMinutes = $this->getTotalTimeSpentAttribute();
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        return $hours . 'h ' . $minutes . 'm';
    }

    // Remplacez cette méthode par une version qui ne dépend pas des objectifs
    public function getObjectivesCompletionPercentageAttribute()
    {
        return 0; // Retourner 0 temporairement puisque les objectifs n'existent pas encore
    }
}

