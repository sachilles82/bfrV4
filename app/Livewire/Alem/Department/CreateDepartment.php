<?php

namespace App\Livewire\Alem\Department;

use App\Enums\Model\ModelStatus;
use App\Livewire\Alem\Department\Helper\ValidateDepartment;
use App\Models\Alem\Department;
use App\Traits\Modal\WithPlaceholder;
use App\Traits\Model\WithModelStatusOptions;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Livewire\Component;

class CreateDepartment extends Component
{
    use AuthorizesRequests, ValidateDepartment, WithModelStatusOptions,
        WithPlaceholder;

    public ?int $departmentId = null; // !Muss in jeder Komponente sein

    public $name;

    public $description;

    public $model_status;

    public string $displayMode = 'default'; // habe ich wegen dem Create Button "open-manager"gemacht

    public function mount(): void
    {
        $this->model_status = ModelStatus::ACTIVE->value;
    }

    public function saveDepartment(): void
    {
        $this->validate();

        try {
            $department = Department::create([
                'name' => $this->name,
                'description' => $this->description,
                'model_status' => $this->model_status,
                'company_id' => auth()->user()->company_id,
                'created_by' => auth()->id(),
            ]);

            $this->dispatch('department-created', id: $department->id);

            $this->resetForm();

            Flux::toast(
                text: __('Department created successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );


        } catch (\Exception $e) {
            Flux::toast(
                text: __('Error: ').$e->getMessage(),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'description', 'model_status']);
        $this->resetErrorBag();
        $this->model_status = ModelStatus::ACTIVE->value;
        $this->modal('create-department')->close();
    }

    public function render(): View
    {
        return view('livewire.alem.department.create', [
            'modelStatusOptions' => $this->modelStatusOptions,
        ]);
    }
}
