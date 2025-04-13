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
    use Searchable, WithPerPagePagination, WithEmployeeSorting,
        WithEmployeeModelStatus,
        WithEmployeeStatus;

    /** Tabelle zeigt nur User mit user_typ employee */
    protected string $userType = UserType::Employee->value;

    // --- NEU: Properties zum Empfangen der Daten aus dem Controller ---
    // Werden automatisch durch Livewire befüllt (:departments="$departments" etc.)
    public array $departments = [];
    public array $teams = [];
    public array $professions = [];
    public array $stages = [];
    // --------------------------------------------------------------------

    /**
     * Hört auf das Event 'employee-created', 'employee-updated' und aktualisiert die Tabelle
     */
    #[On(['employee-created', 'employee-updated'])]
    public function refreshTable(): void
    {
        $this->resetPage();
    }

    /** Lifecycle-Hook: Wird aufgerufen, wenn sich ein Filter ändert, die Auswahl zurücksetzen */
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
        // Unverändert
        $this->resetPage();
        $this->reset('search');
        $this->reset('sortCol', 'sortAsc', 'statusFilter', 'employeeStatusFilter');
        $this->selectedIds = [];
        $this->dispatch('resetFilters');
        $this->dispatch('update-table');
    }

    // --- ENTFERNT: getCacheKey wird hier nicht mehr für Listen benötigt ---
    // protected function getCacheKey(string $suffix): string { ... }
    // -----------------------------------------------------------------

    // --- ENTFERNT: Computed Property für availableTeams ---
    // public function getAvailableTeamsProperty() { ... }
    // Die $teams Property wird jetzt von außen befüllt.
    // ----------------------------------------------------

    /**
     * Öffnet den Bearbeitungsmodus für einen Mitarbeiter
     */
    public function edit($id): void
    {
        // Unverändert
        $this->dispatch('edit-employee', $id);
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        // Hole die Company ID des eingeloggten Users (wird für Query benötigt)
        $authCompanyId = auth()->user()?->company_id;

        // Beginne die Query (unverändert)
        $query = User::query()
            ->select([
                'id', 'department_id', 'name', 'last_name', 'phone_1',
                'email', 'joined_at', 'created_at', 'model_status', 'profile_photo_path', 'slug','company_id'
            ])
            ->where('user_type', $this->userType)
            ->whereHas('employee');

        // Filter nach Company ID (unverändert)
        if ($authCompanyId) {
            $query->where('company_id', $authCompanyId);
        } else {
            Log::warning('User ohne company_id versucht auf EmployeeTable zuzugreifen.', ['user_id' => auth()->id()]);
            $query->whereRaw('1 = 0');
        }

        // Eager Loading (unverändert)
        $query->with([
            'employee' => function($query) {
                $query->select(['id', 'user_id', 'employee_status', 'profession_id', 'stage_id']);
            },
            'employee.profession:id,name',
            'employee.stage:id,name',
            'department:id,name',
            'teams:id,name',
            'roles' => function($query) {
                $query->where('visible', RoleVisibility::Visible->value)
                    ->select('id', 'name');
            }
        ]);

        // Filter und Sortierung anwenden (unverändert)
        $this->applySearch($query);
        $this->applySorting($query);
        $this->applyStatusFilter($query);

        if ($this->employeeStatusFilter) {
            $query->whereHas('employee', function($q) {
                $q->where('employee_status', $this->employeeStatusFilter);
            });
        }

        // Paginierung (unverändert)
        $users = $query->simplePaginate($this->perPage);
        $this->idsOnPage = $users->pluck('id')->map(fn($id) => (string)$id)->toArray();

        // --- View zurückgeben (angepasst) ---
        // 'availableTeams' wird nicht mehr übergeben. Die View kann bei Bedarf
        // auf die public property $this->teams zugreifen (oder $teams direkt).
        return view('livewire.alem.employee.table', [
            'users' => $users,
            'statuses' => ModelStatus::cases(),
            'employeeStatuses' => EmployeeStatus::cases(),
            // 'availableTeams' => $this->availableTeams, // Entfernt
        ]);
        // -----------------------------------
    }
}
