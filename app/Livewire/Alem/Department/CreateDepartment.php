<?php

namespace App\Livewire\Alem\Department;

use App\Livewire\Alem\Department\Helper\ValidateDepartment;
use App\Models\Alem\Department;
use App\Traits\Modal\WithPlaceholder;
use Illuminate\View\View;
use Livewire\Component;

class CreateDepartment extends Component
{
    use ValidateDepartment, WithPlaceholder;

    public ?int $departmentId = null;
    public string $name = '';

    public function create(): void
    {
        $this->modal('create-department')->show();
    }


    public function save(): void
    {
        $this->validate();

        $this->modal('create-department')->close();
        Department::create($this->only([
            'name'
        ]));

        $this->dispatch('departmentUpdated' );

        $this->reset('name');
    }

    public function render(): View
    {
        return view('livewire.alem.department.create');
    }
}
