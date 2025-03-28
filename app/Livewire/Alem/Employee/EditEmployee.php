<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\Role\RoleHasAccessTo;
use App\Enums\Role\RoleVisibility;
use App\Enums\Role\UserHasRole;
use App\Enums\User\Gender;
use App\Livewire\Alem\Employee\Helper\ValidateEmployee;
use App\Models\Alem\Department;
use App\Models\Alem\Employee\Employee;
use App\Models\Spatie\Role;
use App\Models\User;
use App\Models\Alem\Employee\Setting\Profession;
use App\Models\Alem\Employee\Setting\Stage;
use Illuminate\Support\Carbon;
use Flux\Flux;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class EditEmployee extends Component
{
    use ValidateEmployee, AuthorizesRequests;

    //locked in livewire
    public int $employeeId;

    public ?int $userId = null;

    public $selectedRoles = [];
    public $model_status;
    public $employee_status;
    public bool $invitations = false;

    /**
     * Benutzer-Felder (User Fields)
     */
    public $gender;
    public $name;
    public $last_name;
    public $email;
    public ?int $department = null;
    public ?Carbon $joined_at = null;
    public $selectedTeams = [];

    /**
     * Mitarbeiter-spezifische Felder (Employee Fields)
     */
    public ?int $profession = null;
    public ?int $stage = null;
    public ?int $supervisor = null;

    /**
     * Laden eines Benutzers zur Bearbeitung
     */
    #[On('edit-employee')]
    public function loadUser($id): void
    {
        try {
            $user = User::select(['id', 'name', 'last_name', 'email', 'gender', 'model_status', 'joined_at', 'department_id'])
                ->with([
                    'employee:id,user_id,employee_status,profession_id,stage_id,supervisor_id',
                    'employee.profession:id,name',
                    'employee.stage:id,name',
                    'department:id,name',
                    'roles:id,name',
                    'teams:id,name'
                ])
                ->findOrFail($id);

            // Berechtigungsprüfung
//            $this->authorize('update', $user);

            // User-Daten
            $this->userId = $user->id;
            $this->name = $user->name;
            $this->last_name = $user->last_name;
            $this->email = $user->email;
            $this->gender = $user->gender;
            $this->model_status = $user->model_status;
            $this->joined_at = $user->joined_at;
            $this->department = $user->department_id;

            // Teams
            $this->selectedTeams = $user->teams->pluck('id')->toArray();

            // Rollen
            $this->selectedRoles = $user->roles->pluck('id')->toArray();

            // Employee-Daten, falls vorhanden
            if ($user->employee) {
                $this->employeeId = $user->employee->id;
                $this->employee_status = $user->employee->employee_status;
                $this->profession = $user->employee->profession_id;
                $this->stage = $user->employee->stage_id;
                $this->supervisor = $user->employee->supervisor_id;
            }

        } catch (AuthorizationException $ae) {
            Flux::toast(
                text: __('You are not authorized to edit this employee.'),
                heading: __('Error'),
                variant: 'danger'
            );
        } catch (\Exception $e) {
            Flux::toast(
                text: __('Error loading employee: ') . $e->getMessage(),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    /**
     * Aktualisiert den Benutzer und die zugehörigen Employee-Daten
     */
    public function updateEmployee(): void
    {
        $this->validate();

        try {
            $user = User::findOrFail($this->userId);

            // Berechtigungsprüfung (uncomment if needed)
            // $this->authorize('update', $user);

            // User-Daten aktualisieren
            $user->update([
                'name' => $this->name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'gender' => $this->gender,
                'model_status' => $this->model_status,
                'joined_at' => $this->joined_at,
                'department_id' => $this->department,
            ]);

            // Rollen aktualisieren
            $user->roles()->sync($this->selectedRoles);

            // Teams aktualisieren
            $user->teams()->sync($this->selectedTeams);

            // Employee-Daten aktualisieren
            if ($user->employee) {
                $user->employee->update([
                    'employee_status' => $this->employee_status,
                    'profession_id' => $this->profession,
                    'stage_id' => $this->stage,
                    'supervisor_id' => $this->supervisor,
                ]);
            }

            $this->closeModal();

            Flux::toast(
                text: __('Employee updated successfully.'),
                heading: __('Success'),
                variant: 'success'
            );

            $this->dispatch('employee-updated');

        } catch (AuthorizationException $ae) {
            Flux::toast(
                text: __('You are not authorized to update this employee.'),
                heading: __('Error'),
                variant: 'danger'
            );
        } catch (\Exception $e) {
            Flux::toast(
                text: __('Error updating employee: ') . $e->getMessage(),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    /**
     * Modal schließen und Formular zurücksetzen
     */
    public function closeModal(): void
    {
        $this->reset([
            'userId', 'employeeId', 'name', 'last_name', 'email', 'gender',
            'model_status', 'employee_status', 'joined_at', 'department',
            'profession', 'stage', 'supervisor', 'selectedRoles', 'selectedTeams'
        ]);

        $this->modal('edit-employee')->close();
    }

    //-------------------------------------------------------------------------
    // COMPUTED PROPERTIES (GETTER)
    //-------------------------------------------------------------------------

    #[On('professionUpdated')]
    public function getProfessionsProperty()
    {
        return Profession::where('company_id', auth()->user()->company_id)
            ->orderBy('id')
            ->get();
    }

    #[On('stageUpdated')]
    public function getStagesProperty()
    {
        return Stage::where('company_id', auth()->user()->company_id)
            ->orderBy('id')
            ->get();
    }

    #[On('roleUpdated')]
    public function getRolesProperty()
    {
        return Role::where(function ($query) {
            $query->where('access', RoleHasAccessTo::EmployeePanel)
                ->where('visible', RoleVisibility::Visible);
        })
            ->where('created_by', 1)
            ->orWhere('created_by', auth()->id())
            ->get();
    }

    public function getTeamsProperty()
    {
        return auth()->user()->allTeams();
    }

    #[On('departmentUpdated')]
    public function getDepartmentsProperty()
    {
        $teamId = !empty($this->selectedTeams) ? $this->selectedTeams[0] : null;

        $query = Department::where('model_status', ModelStatus::ACTIVE->value)
            ->where('company_id', auth()->user()->company_id);

        if ($teamId) {
            $query->where('team_id', $teamId);
        }

        return $query->get();
    }

    public function getSupervisorsProperty()
    {
        $managerRole = Role::where('name', UserHasRole::Manager->value)->first();
        $ownerRole = Role::where('name', UserHasRole::Owner->value)->first();

        if (!$managerRole && !$ownerRole) {
            return collect();
        }

        $roleIds = collect([$managerRole?->id, $ownerRole?->id])->filter()->toArray();

        return User::query()
            ->select(['id', 'name', 'last_name', 'profile_photo_path'])
            ->where('company_id', auth()->user()->company_id)
            ->where('id', '!=', $this->userId)
            ->whereHas('roles', function ($query) use ($roleIds) {
                $query->whereIn('id', $roleIds);
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * Gets all available model status options with their labels, colors, and icons
     */
    public function getModelStatusOptionsProperty()
    {
        $statuses = [];

        foreach (ModelStatus::cases() as $status) {
            $statuses[] = [
                'value' => $status->value,
                'label' => $status->label(),
                'colors' => $status->colors(),
                'icon' => $status->icon(),
            ];
        }

        return $statuses;
    }

    public function render(): View
    {
        return view('livewire.alem.employee.edit');
    }
}
