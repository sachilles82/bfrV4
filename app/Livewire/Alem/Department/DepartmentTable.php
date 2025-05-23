<?php

namespace App\Livewire\Alem\Department;

use App\Enums\Model\ModelStatus;
use App\Livewire\Alem\Department\Helper\Searchable;
use App\Livewire\Alem\Department\Helper\WithDepartmentModelStatus;
use App\Livewire\Alem\Department\Helper\WithDepartmentSorting;
use App\Models\Alem\Department;
use App\Traits\Employee\WithUserAvatars;
use App\Traits\Table\WithPerPagePagination;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class DepartmentTable extends Component
{
    use Searchable, WithDepartmentModelStatus, WithDepartmentSorting,
        WithPerPagePagination,
        WithUserAvatars;

    public $departmentId; // Wird benötigt damit ich die Daten in der EditDepartment Komponente laden kann

    /**
     * Hört auf das Event 'department-created', 'department-updated' und aktualisiert die Tabelle
     */
    #[On(['department-created', 'department-updated'])]
    public function refreshTable(): void
    {
        $this->resetPage();
    }

    /**
     * Lifecycle-Hook: Wird aufgerufen, wenn sich ein Filter ändert, die Auswahl zurücksetzen
     */
    public function updated($property): void
    {
        if (in_array($property, ['statusFilter'])) {
            $this->selectedIds = [];
            $this->reset('search', 'sortCol', 'sortAsc');
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
        $this->reset('sortCol', 'sortAsc', 'statusFilter');
        $this->selectedIds = [];
        $this->dispatch('resetFilters');
        $this->dispatch('update-table');
    }

    /**
     * Öffnet den Bearbeitungsmodus für einen Mitarbeiter
     */
    public function edit($id): void
    {
        $this->dispatch('edit-department', $id);
    }

    //    #[On('created')]
    //    public function handleCreated(): void
    //    {
    //        $this->resetPage();
    //        $this->reset('search');
    //        $this->dispatch('update-table');
    //    }

    public function render(): View
    {
        $query = Department::with(['creator', 'team', 'company', 'users']);

        $this->applySearch($query);
        $this->applySorting($query);
        $this->applyStatusFilter($query);

        $departments = $query->orderBy('created_at', 'desc')->paginate($this->perPage);
        $this->idsOnPage = $departments->pluck('id')->map(fn ($id) => (string) $id)->toArray();

        return view('livewire.alem.department.table', [
            'departments' => $departments,
            'statuses' => ModelStatus::cases(),
        ]);
    }
}
