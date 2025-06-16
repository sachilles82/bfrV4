<?php

namespace App\Http\Controllers\Alem\Employee;

use App\Enums\User\UserType;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    /**
     * Zeigt das Profil eines Mitarbeiters an
     *
     * @param  string  $slug  Der Slug des Mitarbeiters (z.B. 'vorname-nachname-index')
     * @param  string  $activeTab  Der aktive Tab in der Ansicht (Standard ist 'employee-update')
     */
    public function show(string $slug, string $activeTab = 'employee-update'): View
    {
        $authUser = Auth::user();
        $currentTeamId = $authUser->currentTeam->id;
        $companyId = $authUser->company_id;

        // Optimiertes Laden des Benutzers mit allen benötigten Relationen
        // Cache-Schlüssel enthält den Slug und den aktiven Tab
        $cacheKey = "employee_profile_{$slug}_{$activeTab}";

        $user =
            Cache::remember($cacheKey, now()->addHours(1), function () use ($slug) {
            // Nur minimale User-Daten laden (ID und slug)
            return User::select(['id', 'slug', 'user_type'])
                ->where('slug', $slug)
                ->where('user_type', UserType::Employee->value)
                ->first();
        });

        if (!$user) {
            abort(404, 'Mitarbeiter nicht gefunden.');
        }

        return view('laravel.alem.employee.show',
            compact('user', 'activeTab')
            [
            'authUserId' => $authUser->id,
            'currentTeamId' => $currentTeamId,
            'companyId' => $companyId,
        ]
        );
    }
}
