<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Services\UserPresenceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserPresenceController extends Controller
{
    protected $presenceService;

    public function __construct(UserPresenceService $presenceService)
    {
        $this->presenceService = $presenceService;
    }

    /**
     * Afficher les statistiques de présence de l'équipe
     */
    public function teamPresence(Request $request)
    {
        $user = $request->user();

        // Vérifier que l'utilisateur est bien un manager
        if ($user->role !== 'manager') {
            return redirect()->route('access.denied')->with('error', 'Accès non autorisé');
        }

        $team = $user->currentTeam;

        // Période par défaut: 30 derniers jours
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))
            : Carbon::now()->subDays(30);

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))
            : Carbon::now();

        // Calcul du nombre de jours ouvrés dans la période
        $workingDays = $this->countWorkingDays($startDate, $endDate);

        // Récupérer le résumé de présence pour la période
        $presenceData = $this->presenceService->getTeamPresenceSummary($team, $workingDays);

        // Récupérer les données détaillées par jour pour chaque collaborateur
        $dailyPresence = $this->presenceService->getDailyPresenceForTeam($team, $startDate, $endDate);

        return Inertia::render('Manager/TeamPresence', [
            'team' => $team->only('id', 'name'),
            'presenceData' => [
                'summary' => $presenceData,
                'dailyPresence' => $dailyPresence,
                'period' => [
                    'start' => $startDate->format('Y-m-d'),
                    'end' => $endDate->format('Y-m-d'),
                    'workingDays' => $workingDays
                ]
            ]
        ]);
    }

    /**
     * Afficher les statistiques de présence d'un collaborateur spécifique
     */
    public function userPresence(Request $request, User $collaborateur)
    {
        $user = $request->user();

        // Vérifier les permissions
        if ($user->role !== 'manager' && $user->id !== $collaborateur->id) {
            return redirect()->route('access.denied')->with('error', 'Accès non autorisé');
        }

        // Si c'est un manager, vérifier que le collaborateur est dans son équipe
        if ($user->role === 'manager') {
            $team = $user->currentTeam;
            if (!$team->hasUser($collaborateur)) {
                return redirect()->route('access.denied')->with('error', 'Ce collaborateur n\'appartient pas à votre équipe');
            }
        }

        // Période par défaut: 30 derniers jours
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))
            : Carbon::now()->subDays(30);

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))
            : Carbon::now();

        // Récupérer les données de présence quotidienne
        $dailyPresence = $this->presenceService->getDailyPresenceForUser($collaborateur, $startDate, $endDate);

        return Inertia::render('User/Presence', [
            'collaborateur' => $collaborateur->only('id', 'name', 'email'),
            'presenceData' => [
                'dailyPresence' => $dailyPresence,
                'period' => [
                    'start' => $startDate->format('Y-m-d'),
                    'end' => $endDate->format('Y-m-d'),
                ]
            ],
            'isManager' => $user->role === 'manager' && $user->id !== $collaborateur->id
        ]);
    }

    /**
     * Compte le nombre de jours ouvrés (Lundi-Vendredi) entre deux dates
     */
    private function countWorkingDays(Carbon $startDate, Carbon $endDate)
    {
        $days = 0;
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            // 0 = dimanche, 6 = samedi
            if (!in_array($currentDate->dayOfWeek, [0, 6])) {
                $days++;
            }
            $currentDate->addDay();
        }

        return $days;
    }
}
