<?php

namespace App\Livewire\HR\Department;

use App\Livewire\HR\Department\Helper\ValidateDepartment;
use App\Models\HR\Department;
use Livewire\Component;
use Illuminate\View\View;

class DepartmentUpdate extends Component
{
    use ValidateDepartment;

    public $departmentId;
    public $department;
    public $name;


    public function mount(Department $department): void
    {
        $this->departmentId = $department;
        $this->name = $department->name;
    }


    public function updateDepartment(): void
    {
        // this authorize
//        $this->authorize('update', $department);
        $this->validate();

        $this->department->update([
            'name' => $this->name,
        ]);

    }

    public function render():View
    {
        return view('livewire.hr.department.update');
    }
}
