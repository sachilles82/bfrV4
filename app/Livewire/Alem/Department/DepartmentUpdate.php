<?php

namespace App\Livewire\Alem\Department;

use App\Livewire\Alem\Department\Helper\ValidateDepartment;
use App\Models\Alem\Department;
use Illuminate\View\View;
use Livewire\Component;

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
        return view('livewire.alem.department.update');
    }
}
