<?php

namespace App\Livewire\Alem\Department;

use App\Enums\Model\ModelStatus;
use App\Livewire\Alem\Department\Helper\Searchable;
use App\Livewire\Alem\Department\Helper\ValidateDepartment;
use App\Models\Alem\Department;
use App\Traits\Model\ModelStatusAction;
use App\Traits\Table\WithPerPagePagination;
use App\Traits\Table\WithSorting;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class DepartmentTable extends Component
{
    use Searchable, WithPerPagePagination, WithSorting, ValidateDepartment, ModelStatusAction;

    public $selectedIds = [];
    public $idsOnPage = [];
    public $statusFilter = 'active';

    public $departmentId;
    public $name = '';

    /**
     * Die Modellklasse für ModelStatusAction
     */
    protected function getModelClass(): string
    {
        return Department::class;
    }

    /**
     * Der Anzeigename für das Modell
     */
    protected function getModelDisplayName(): string
    {
        return 'Department';
    }

    /**
     * Der pluralisierte Anzeigename für das Modell
     */
    protected function getModelDisplayNamePlural(): string
    {
        return 'Departments';
    }

    /**
     * Der Benutzertyp für die Filterung
     */
    protected string $DepartmentType = 'department';

    /**
     * Name des Events, das nach Status-Änderungen ausgelöst wird
     */
    protected function getStatusUpdateEventName(): string
    {
        return 'departmentUpdated';
    }


    public function loadDepartment(): void
    {
        $department = Department::find($this->departmentId);
        $this->name = $department->name;
    }

    public function showEditModal($id): void
    {
        $this->reset();
        $this->resetErrorBag();
        // find department
        $this->departmentId = $id;
        // load department
        $this->loadDepartment();
        // show Modal
        $this->modal('department-edit')->show();
    }


    public function update(): void
    {
        // Authorization hinzufügen

        $this->validate();

        $department = Department::find($this->departmentId);

        $department -> update($this->only([
            'name'
        ]));

        $this->modal('department-edit')->close();
    }


    /** Delete Function **/
    public function delete($id): void
    {
        $department = Department::find($id);

        // Authorization hinzufügen
        $department->delete();
    }
    /** Delete Function **/


    #[On('created')]
    public function resetFilters(): void
    {
        $this->resetPage();
        $this->reset('search');
    }


    public function render(): View
    {
        $query = Department::with(['creator', 'team', 'company']);

        // Wende den Status-Filter an (aus dem ModelStatusAction Trait)
        $this->applyStatusFilter($query);

        $query->orderBy('created_at', 'desc');

        $this->applySearch($query);

        $departments = $this->applySimplePagination($query);

        $this->idsOnPage = $departments->pluck('id')->map(fn($id) => (string)$id)->toArray();

        return view('livewire.alem.department.table', [
            'departments' => $departments,
            'statuses' => ModelStatus::cases(),
        ]);
    }
}
