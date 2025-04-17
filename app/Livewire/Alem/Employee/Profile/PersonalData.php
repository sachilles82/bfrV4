<?php

namespace App\Livewire\Alem\Employee\Profile;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Employee\NoticePeriod;
use App\Enums\Employee\Probation;
use App\Livewire\Alem\Employee\Profile\Helper\ValidatePersonalData;
use App\Models\Alem\Employee\Employee;
use App\Models\Alem\Employee\Setting\Profession;
use App\Models\Alem\Employee\Setting\Stage;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Lazy(isolate: false)]
class PersonalData extends Component
{
    use AuthorizesRequests, ValidatePersonalData;

    // User mit den Daten
    public User $user;

    public $joined_at = '';

    // Employee mit den Daten
    public ?Employee $employee = null;

    public $personal_number = '';

    public $profession = '';

    public $stage = '';

    public $employment_type = '';

    public $supervisor = '';

    //    $joined_at; wird im User Model gespeichert
    public $probation_enum = '';

    public $probation_at = '';

    public $notice_at = '';

    public $notice_enum = '';

    public $leave_at = '';

    public $employee_status = '';

    /**
     * Der Mount-Hook erhält einen User und lädt zusätzlich die Employee-Daten,
     * falls vorhanden.
     */
    public function mount(User $user): void
    {
        $this->user = $user; // Fehlende Zuweisung hinzugefügt

        // Optimierung: Nur die benötigten Relationen und Felder laden
        if (!$user->relationLoaded('employee')) {
            $user->load(['employee' => function ($query) {
                $query->select([
                    'id', 'user_id', 'employee_status', 'profession_id', 'stage_id',
                    'supervisor_id', 'probation_enum', 'probation_at', 'notice_at',
                    'notice_enum', 'leave_at', 'personal_number', 'employment_type'
                ]);
            }, 'employee.profession:id,name', 'employee.stage:id,name']);
        }

        $this->employee = $this->user->employee;
        $this->joined_at = $this->user->joined_at?->format('Y-m-d') ?? '';

        if ($this->employee) {
            $this->employee_status = $this->employee->employee_status?->value ?? '';
            $this->personal_number = $this->employee->personal_number ?? '';
            $this->employment_type = $this->employee->employment_type ?? '';
            $this->supervisor = $this->employee->supervisor_id ?? '';
            $this->leave_at = $this->employee->leave_at?->format('Y-m-d') ?? '';
            $this->probation_at = $this->employee->probation_at?->format('Y-m-d') ?? '';
            $this->probation_enum = $this->employee->probation_enum?->value ?? '';
            $this->notice_at = $this->employee->notice_at?->format('Y-m-d') ?? '';
            $this->notice_enum = $this->employee->notice_enum?->value ?? '';
            $this->profession = $this->employee->profession_id ?? '';
            $this->stage = $this->employee->stage_id ?? '';
        }
    }

    /**
     * Get all employee status options for the dropdown
     */
    public function getEmployeeStatusOptionsProperty()
    {
        // Cache der Statusoptionen für bessere Performance
        return Cache::remember('employee_status_options', now()->addHours(24), function () {
            return collect(EmployeeStatus::cases())->map(function ($status) {
                return [
                    'value' => $status->value,
                    'label' => $status->label(),
                    'icon' => $status->icon(),
                    'colors' => $status->colors(),
                ];
            });
        });
    }

    /**
     * Get all users with the manager role for supervisor selection
     */
    public function getSupervisorsProperty()
    {
        // Cache-Schlüssel basierend auf Teams des aktuellen Benutzers
        $userTeamIds = $this->user->teams->pluck('id')->implode('-');
        $cacheKey = "supervisors_user_teams_{$userTeamIds}_exclude_{$this->user->id}";

        return Cache::remember($cacheKey, now()->addMinutes(30), function () {
            // Manager-Rolle identifizieren
            $managerRole = Role::select('id')->where('name', 'Manager')->first();

            if (!$managerRole) {
                return collect();
            }

            // Team-IDs des aktuellen Benutzers abrufen
            $userTeamIds = $this->user->teams->pluck('id')->toArray();

            if (empty($userTeamIds)) {
                return collect();
            }

            // Benutzer mit Manager-Rolle finden, die in mindestens einem der Teams des aktuellen Benutzers sind
            return User::join('model_has_roles', function ($join) use ($managerRole) {
                    $join->on('users.id', '=', 'model_has_roles.model_id')
                         ->where('model_has_roles.model_type', User::class)
                         ->where('model_has_roles.role_id', $managerRole->id);
                })
                ->join('team_user', function ($join) use ($userTeamIds) {
                    $join->on('users.id', '=', 'team_user.user_id')
                         ->whereIn('team_user.team_id', $userTeamIds);
                })
                ->where('users.id', '!=', $this->user->id)
                ->whereNull('users.deleted_at')
                ->select(['users.id', 'users.name', 'users.last_name', 'users.email'])
                ->groupBy('users.id', 'users.name', 'users.last_name', 'users.email') // Verhindert Duplikate bei mehreren gemeinsamen Teams
                ->orderBy('users.name')
                ->get();
        });
    }

    /**
     * Get all probation options for the dropdown
     */
    public function getProbationOptionsProperty()
    {
        return Cache::remember('employee_probation_options', now()->addDays(7), function () {
            return Probation::options();
        });
    }

    /**
     * Get all notice period options for the dropdown
     */
    public function getNoticePeriodOptionsProperty()
    {
        return Cache::remember('employee_notice_period_options', now()->addDays(7), function () {
            return NoticePeriod::options();
        });
    }

    /**
     * Get all available professions
     */
    #[On('professionUpdated')]
    public function getProfessionsProperty()
    {
        // Cache-Schlüssel mit Team-ID
        $teamId = $this->user->currentTeam?->id ?? 0;
        $cacheKey = "professions_team_{$teamId}";

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($teamId) {
            return Profession::select(['id', 'name', 'team_id'])
                ->when($teamId, function ($query) use ($teamId) {
                    return $query->where('team_id', $teamId);
                })
                ->get();
        });
    }

    /**
     * Get all available stages
     */
    #[On('stageUpdated')]
    public function getStagesProperty()
    {
        // Cache-Schlüssel mit Team-ID
        $teamId = $this->user->currentTeam?->id ?? 0;
        $cacheKey = "stages_team_{$teamId}";

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($teamId) {
            return Stage::select(['id', 'name', 'team_id'])
                ->when($teamId, function ($query) use ($teamId) {
                    return $query->where('team_id', $teamId);
                })
                ->get();
        });
    }

    /**
     * Aktualisiert die Employee-Daten oder erstellt einen neuen Datensatz, falls noch keiner existiert.
     */
    public function updateEmployee(): void
    {
        // $this->authorize('update', $this->user);

        $this->validate();

        try {
            // Zuerst den User aktualisieren für joined_at
            $this->user->update([
                'joined_at' => $this->joined_at,
            ]);

            // Employee-Daten für Update vorbereiten
            $employeeData = [
                'employee_status' => $this->employee_status,
                'personal_number' => $this->personal_number,
                'employment_type' => $this->employment_type,
                'supervisor_id' => $this->supervisor ?: null,
                'leave_at' => $this->leave_at ?: null,
                'probation_at' => $this->probation_at ?: null,
                'probation_enum' => $this->probation_enum,
                'notice_at' => $this->notice_at ?: null,
                'notice_enum' => $this->notice_enum,
                'profession_id' => $this->profession ?: null,
                'stage_id' => $this->stage ?: null,
            ];

            // Update oder Create Employee
            if ($this->employee) {
                $this->employee->update($employeeData);
            } else {
                // Wenn noch kein Employee-Datensatz existiert, erstelle einen neuen
                $employeeData['user_id'] = $this->user->id;

                // UUID generieren falls benötigt
                if (! isset($employeeData['uuid'])) {
                    $employeeData['uuid'] = \Illuminate\Support\Str::uuid()->toString();
                }

                $this->employee = Employee::create($employeeData);

                // Den Employee-Datensatz neu laden
                $this->user->load('employee');
            }

            // Cache für betroffene Daten ungültig machen
            $userTeamIds = $this->user->teams->pluck('id')->implode('-');
            Cache::forget("supervisors_user_teams_{$userTeamIds}_exclude_{$this->user->id}");

            Flux::toast(
                text: __('Employee data updated successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

            $this->dispatch('employee-updated');

        } catch (\Exception $e) {
            Flux::toast(
                text: __('Error updating employee data: ').$e->getMessage(),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    /**
     * Make computed properties available to the blade template
     */
    public function render(): View
    {
        // Computed Properties vorab laden, um Eager Loading zu fördern
        $employeeStatusOptions = $this->employeeStatusOptions;
        $probationOptions = $this->probationOptions;
        $noticePeriodOptions = $this->noticePeriodOptions;
        $supervisors = $this->supervisors;

        return view('livewire.alem.employee.profile.personal-data', [
            'employeeStatusOptions' => $employeeStatusOptions,
            'probationOptions' => $probationOptions,
            'noticePeriodOptions' => $noticePeriodOptions,
            'supervisors' => $supervisors,
        ]);
    }
}
