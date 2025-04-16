<?php

namespace App\Http\Controllers\Alem\Employee;

use App\Enums\User\UserType;
use App\Http\Controllers\Controller;
use App\Models\User;
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
        // Optimiertes Laden des Benutzers mit allen benötigten Relationen
        // Cache-Schlüssel enthält den Slug und den aktiven Tab
        $cacheKey = "employee_profile_{$slug}_{$activeTab}";
        
        $user = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($slug) {
            return User::with([
                // Grundlegende Benutzerinformationen
                'department:id,name',
                // Mitarbeiterinformationen
                'employee' => function ($query) {
                    $query->select([
                        'id', 'user_id', 'employee_status', 'profession_id', 'stage_id',
                        'supervisor_id', 'probation_enum', 'probation_at', 'notice_at',
                        'notice_enum', 'leave_at', 'personal_number', 'employment_type',
                        'ahv_number', 'birthdate', 'nationality', 'hometown',
                        'religion', 'civil_status', 'residence_permit'
                    ]);
                },
                'employee.profession:id,name',
                'employee.stage:id,name',
                'employee.supervisorUser:id,name,last_name,email',
                // Teams und Rollen
                'ownedTeams:id,name,user_id',
                'teams:id,name',
                'roles:id,name'
            ])
            ->select([
                'id', 'name', 'last_name', 'email', 'phone_1', 'gender',
                'department_id', 'slug', 'model_status', 'joined_at',
                'user_type', 'company_id', 'profile_photo_path'
            ])
            ->where('slug', $slug)
            ->where('user_type', UserType::Employee->value)
            ->first();
        });

        if (!$user || !$user->employee) {
            abort(404, 'Mitarbeiter nicht gefunden.');
        }

        return view('laravel.alem.employee.show',
            compact('user', 'activeTab')
        );
    }
}
