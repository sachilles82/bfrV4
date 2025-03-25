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
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class PersonalData extends Component
{
    use ValidatePersonalData, AuthorizesRequests;

    // User und Employee Datensätze
    public User $user;
    public ?Employee $employee = null;

    // Employee Daten
    public $employee_status = '';
    public $personal_number = '';
    public $employment_type = '';
    public $supervisor = ''; // This will store the supervisor_id

    public $joined_at = '';
    public $probation_at = '';
    public $probation_enum = '';

    public $notice_at = '';
    public $notice_enum = '';
    public $leave_at = '';

    public $profession = '';
    public $stage = '';

    /**
     * Der Mount-Hook erhält einen User und lädt zusätzlich die Employee-Daten,
     * falls vorhanden.
     */
    public function mount(User $user): void
    {
        // Eager load relations, Employee mit seinen Beziehungen
        $user->load(['employee.professionRelation', 'employee.stageRelation', 'employee.supervisorUser']);

        $this->user = $user;
        $this->employee = $user->employee;

        // Joined_at aus dem User-Modell laden
        $this->joined_at = $user->joined_at ? $user->joined_at->format('Y-m-d') : '';

        // Wenn ein Employee-Datensatz existiert, lade die Daten
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
            $this->profession = $this->employee->profession ?? '';
            $this->stage = $this->employee->stage ?? '';
        }
    }

    /**
     * Get all employee status options for the dropdown
     */
    public function getEmployeeStatusOptionsProperty()
    {
        return collect(EmployeeStatus::cases())->map(function ($status) {
            return [
                'value' => $status->value,
                'label' => $status->label(),
                'icon' => $status->icon(),
                'colors' => $status->colors()
            ];
        });
    }

    /**
     * Get all users with the manager role for supervisor selection
     */
    public function getSupervisorsProperty()
    {
        // Get manager role
        $managerRole = Role::where('name', 'Manager')->first();

        if (!$managerRole) {
            // If the role doesn't exist, return an empty collection
            return collect();
        }

        // Get users with manager role, excluding current user
        return User::role($managerRole)
            ->where('id', '!=', $this->user->id) // Exclude current user
            ->orderBy('name')
            ->get();
    }

    /**
     * Get all probation options for the dropdown
     */
    public function getProbationOptionsProperty()
    {
        return Probation::options();
    }

    /**
     * Get all notice period options for the dropdown
     */
    public function getNoticePeriodOptionsProperty()
    {
        return NoticePeriod::options();
    }

    /**
     * Get all available professions
     */
    #[On('professionUpdated')]
    public function getProfessionsProperty()
    {
        // Alle verfügbaren Professions laden, gefiltered nach Team
        $teamId = $this->user->currentTeam?->id;

        return Profession::when($teamId, function ($query) use ($teamId) {
            return $query->where('team_id', $teamId);
        })->get();
    }

    /**
     * Get all available stages
     */
    #[On('stageUpdated')]
    public function getStagesProperty()
    {
        // Alle verfügbaren Stages laden, gefiltered nach Team
        $teamId = $this->user->currentTeam?->id;

        return Stage::when($teamId, function ($query) use ($teamId) {
            return $query->where('team_id', $teamId);
        })->get();
    }

    /**
     * Aktualisiert die Employee-Daten oder erstellt einen neuen Datensatz, falls noch keiner existiert.
     */
    public function updateEmployee(): void
    {
        //$this->authorize('update', $this->user);

//        $this->validate();

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
                'profession' => $this->profession ?: null,
                'stage' => $this->stage ?: null
            ];

            // Update oder Create Employee
            if ($this->employee) {
                $this->employee->update($employeeData);
            } else {
                // Wenn noch kein Employee-Datensatz existiert, erstelle einen neuen
                $employeeData['user_id'] = $this->user->id;

                // UUID generieren falls benötigt
                if (!isset($employeeData['uuid'])) {
                    $employeeData['uuid'] = \Illuminate\Support\Str::uuid()->toString();
                }

                $this->employee = Employee::create($employeeData);

                // Den Employee-Datensatz neu laden
                $this->user->load('employee');
            }

            Flux::toast(
                text: __('Employee data updated successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

            $this->dispatch('employee-updated');

        } catch (\Exception $e) {
            Flux::toast(
                text: __('Error updating employee data: ') . $e->getMessage(),
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
        $employeeStatusOptions = $this->employeeStatusOptions;
        $probationOptions = $this->probationOptions;
        $noticePeriodOptions = $this->noticePeriodOptions;
        $supervisors = $this->supervisors;

        return view('livewire.alem.employee.profile.personal-data', [
            'employeeStatusOptions' => $employeeStatusOptions,
            'probationOptions' => $probationOptions,
            'noticePeriodOptions' => $noticePeriodOptions,
            'supervisors' => $supervisors
        ]);
    }
}
