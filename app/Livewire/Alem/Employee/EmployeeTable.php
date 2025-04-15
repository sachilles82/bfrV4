<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\Role\RoleVisibility;
use App\Enums\User\UserType;
use App\Livewire\Alem\Employee\Helper\Searchable;
use App\Livewire\Alem\Employee\Helper\WithEmployeeModelStatus;
use App\Livewire\Alem\Employee\Helper\WithEmployeeSorting;
use App\Livewire\Alem\Employee\Helper\WithEmployeeStatus;
use App\Models\User;
use App\Traits\Table\WithPerPagePagination;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class EmployeeTable extends Component
{
    use Searchable, WithEmployeeModelStatus, WithEmployeeSorting,
        WithEmployeeStatus,
        WithPerPagePagination;

    /**
     * Tabelle zeigt nur User mit user_typ employee
     */
    protected string $userType = UserType::Employee->value;

    public array $departments = [];

    public array $teams = [];

    public array $professions = [];

    public array $stages = [];

    /**
     * Hört auf das Event 'employee-created', 'employee-updated' und aktualisiert die Tabelle
     */
    #[On(['employee-created', 'employee-updated'])]
    public function refreshTable(): void
    {
        $this->resetPage();
    }

    /**
     * Lifecycle-Hook: Wird aufgerufen, wenn sich ein Filter ändert, die Auswahl zurücksetzen
     */
    public function updated($property): void
    {
        // Unverändert
        if (in_array($property, ['statusFilter', 'employeeStatusFilter'])) {
            $this->selectedIds = [];
            $this->reset('search', 'sortCol', 'sortAsc');

            if ($property === 'statusFilter') {
                $this->reset('employeeStatusFilter');
            }

            $this->dispatch('update-table');
        }
    }

    /**
     * Alle Filter zurücksetzen
     */
    public function resetFilters(): void
    {
        $this->resetPage();
        $this->reset('search');
        $this->reset('sortCol', 'sortAsc', 'statusFilter', 'employeeStatusFilter');
        $this->selectedIds = [];
        $this->dispatch('resetFilters');
        $this->dispatch('update-table');
    }

    /**
     * Öffnet den Bearbeitungsmodus für einen Mitarbeiter
     */
    public function edit($id): void
    {
        $this->dispatch('edit-employee', $id);
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        $authCurrentTeamId = auth()->user()?->currentTeam?->id;

        $query = User::query()
            ->select([
                'users.id', 'users.department_id', 'users.name', 'users.last_name', 'users.phone_1',
                'users.email', 'users.joined_at', 'users.created_at', 'users.model_status',
                'users.profile_photo_path', 'users.slug', 'users.company_id',
                // 'users.team_id' nicht auswählen, es sei denn, du brauchst es explizit hier
            ])
            ->where('users.user_type', $this->userType)
            ->whereHas('employee');

        // *** NEU: Filter nach aktueller Team ID über Jetstream-Mitgliedschaft ***
        if ($authCurrentTeamId) {
            $query->whereHas('teams', function ($q) use ($authCurrentTeamId) {
                // Filtere Benutzer, die Mitglied im aktuellen Team des Admins sind
                $q->where('teams.id', $authCurrentTeamId); // Filtert über die Pivot-Tabelle team_user
            });
        } else {
            // Wenn der Admin kein currentTeam hat (sollte nicht vorkommen, aber sicher ist sicher)
            Log::warning('Admin ohne currentTeam versucht auf EmployeeTable zuzugreifen.', ['user_id' => auth()->id()]);
            $query->whereRaw('1 = 0'); // Keine Mitarbeiter anzeigen
        }

        // Eager Loading (wie zuvor, ohne spezielle Scope-Behandlung für Department)
        $query->with([
            'employee' => function ($q_employee) {
                $q_employee->select(['id', 'user_id', 'employee_status', 'profession_id', 'stage_id'])
                    ->with([
                        'profession:id,name',
                        'stage:id,name',
                    ]);
            },
            'department:id,name', // Department normal laden
            // Lade die Teams des *Mitarbeiters*, falls benötigt für die Anzeige in der Tabelle
            'teams:id,name',
            'roles' => function ($q_roles) {
                $q_roles->where('visible', RoleVisibility::Visible->value)
                    ->select('id', 'name');
            },
        ]);

        // Filter und Sortierung anwenden (unverändert)
        $this->applySearch($query);
        $this->applySorting($query);
        $this->applyStatusFilter($query);

        if ($this->employeeStatusFilter) {
            $query->whereHas('employee', function ($q) {
                $q->where('employee_status', $this->employeeStatusFilter);
            });
        }

        $users = $query->simplePaginate($this->perPage);
        $this->idsOnPage = $users->pluck('id')->map(fn ($id) => (string) $id)->toArray();

        return view('livewire.alem.employee.table', [
            'users' => $users,
            'statuses' => ModelStatus::cases(),
            'employeeStatuses' => EmployeeStatus::cases(),
        ]);
    }
}
