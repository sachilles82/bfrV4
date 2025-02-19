<?php

namespace App\Livewire\Alem\Department;

use App\Livewire\Alem\Department\Helper\ValidateDepartment;
use App\Models\Alem\Department;
use Illuminate\View\View;
use Livewire\Component;

class CreateDepartment extends Component
{
    use ValidateDepartment;

    public ?int $departmentId = null;
    public string $name = '';

    public function create(): void
    {
        $this->modal('department-add')->show();
    }


    public function save(): void
    {
        $this->validate();

        $this->modal('department-add')->close();
        Department::create($this->only([
            'name'
        ]));

        $this->dispatch('created' );

        $this->reset('name');
    }

    public function render(): View
    {
        return view('livewire.alem.department.create');
    }
}
