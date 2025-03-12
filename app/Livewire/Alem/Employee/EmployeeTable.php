<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Livewire\Alem\Employee\Helper\Searchable;
use App\Models\User;
use App\Traits\Model\ModelStatusAction;
use App\Traits\Table\WithPerPagePagination;
use App\Traits\Table\WithSorting;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;

class EmployeeTable extends Component
{
    use Searchable, WithPerPagePagination, WithSorting, ModelStatusAction;

    public $selectedIds = [];
    public $idsOnPage = [];
    public $name = '';

    #[Url]
    public $statusFilter = 'active';

    #[Url]
    public $employeeStatusFilter = '';

    /**
     * Die Modellklasse für ModelStatusAction
     */
    protected function getModelClass(): string
    {
        return User::class;
    }

    /**
     * Der Anzeigename für das Modell
     */
    protected function getModelDisplayName(): string
    {
        return 'Employee';
    }

    /**
     * Der pluralisierte Anzeigename für das Modell
     */
    protected function getModelDisplayNamePlural(): string
    {
        return 'Employees';
    }

    /**
     * Der Benutzertyp für die Filterung
     */
    protected string $userType = 'employee';

    /**
     * Name des Events, das nach Status-Änderungen ausgelöst wird
     */
    protected function getStatusUpdateEventName(): string
    {
        return 'employeeUpdated';
    }

    /**
     * Lifecycle-Hook: Wird aufgerufen, wenn sich eine Property ändert
     */
    public function updated($property): void
    {
        // Wenn sich ein Filter ändert, die Auswahl zurücksetzen
        if (in_array($property, ['statusFilter', 'employeeStatusFilter'])) {
            $this->resetSelections();
            $this->dispatch('update-table');
        }
    }

    /**
     * Setzt die Auswahl zurück
     */
    protected function resetSelections(): void
    {
        $this->selectedIds = [];
    }

    /**
     * Alle Filter zurücksetzen
     */
    public function resetFilters(): void
    {
        $this->reset('search', 'sortCol', 'sortAsc', 'statusFilter', 'employeeStatusFilter');
        $this->resetSelections();
        $this->dispatch('resetFilters');
        $this->dispatch('update-table');
    }

    /**
     * Nur den Employee-Status-Filter zurücksetzen
     */
    public function resetEmployeeStatusFilter(): void
    {
        $this->employeeStatusFilter = '';
        $this->resetSelections();
        $this->dispatch('update-table');
    }

    /**
     * Sortierung auf die Abfrage anwenden
     */
    protected function applySorting(Builder $query): Builder
    {
        if ($this->sortCol) {
            $column = match ($this->sortCol) {
                'name' => 'name',
                'email' => 'email',
                default => 'created_at',
            };
            $query->orderBy($column, $this->sortAsc ? 'asc' : 'desc');
        }
        return $query;
    }

    /**
     * Setzt den Mitarbeiterstatus-Filter zurück und bereinigt andere Suchkriterien
     */
    public function setAllStatus(): void
    {
        $this->employeeStatusFilter = '';
        $this->reset('search', 'sortCol', 'sortAsc');
        $this->resetSelections();
        $this->dispatch('resetFilters');
        $this->dispatch('update-table');
    }

    /**
     * Employee-Status-Filter auf die Abfrage anwenden
     * Wenn kein Filter gesetzt ist, werden alle Mitarbeiter zurückgegeben
     */
    protected function applyEmployeeStatusFilter(Builder $query): Builder
    {
        if (!empty($this->employeeStatusFilter)) {
            $query->whereHas('employee', function ($query) {
                $query->where('employee_status', $this->employeeStatusFilter);
            });
        }
        return $query;
    }

    public function render(): View
    {
        $authUser = auth()->user();

        $query = User::query()
            ->with(['employee', 'teams:id,name', 'roles:id,name'])
            ->where('company_id', $authUser->company_id)
            ->where('user_type', $this->userType);

        $this->applySearch($query);
        $this->applySorting($query);
        $this->applyStatusFilter($query);

        // Filter nach EmployeeStatus, falls gesetzt
        $this->applyEmployeeStatusFilter($query);

        $users = $query->orderBy('created_at', 'desc')->paginate($this->perPage);
        $this->idsOnPage = $users->pluck('id')->map(fn($id) => (string)$id)->toArray();

        return view('livewire.alem.employee.table', [
            'users' => $users,
            'statuses' => ModelStatus::cases(),
            'employeeStatuses' => EmployeeStatus::cases(),
        ]);
    }
}
