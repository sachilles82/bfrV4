<?php

namespace App\Livewire\Alem\Department;

use App\Livewire\Alem\Department\Helper\Searchable;
use App\Livewire\Alem\Department\Helper\ValidateDepartment;
use App\Models\Alem\Department;
use App\Traits\Table\WithPerPagePagination;
use App\Traits\Table\WithSorting;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class DepartmentTable extends Component
{
    use Searchable, WithPerPagePagination, WithSorting, ValidateDepartment;

    public $selectedIds = [];
    public $idsOnPage = [];

    public $departmentId;
    public $name = '';


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

        $query->orderBy('created_at', 'desc');

        $this->applySearch($query);

        $departments = $this->applySimplePagination($query);

        $this->idsOnPage = $departments->pluck('id')->map(fn($id) => (string)$id)->toArray();

        return view('livewire.alem.department.table',
            compact('departments')
        );
    }
}
