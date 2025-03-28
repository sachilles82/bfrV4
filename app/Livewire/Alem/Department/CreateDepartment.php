<?php

namespace App\Livewire\Alem\Department;

use App\Enums\Model\ModelStatus;
use App\Livewire\Alem\Department\Helper\ValidateDepartment;
use App\Models\Alem\Department;
use App\Traits\Modal\WithPlaceholder;
use App\Traits\Model\WithModelStatusOptions;
use Flux\Flux;
use Illuminate\View\View;
use Livewire\Component;

class CreateDepartment extends Component
{
    use ValidateDepartment, WithPlaceholder,
        WithModelStatusOptions;

    public ?int $departmentId = null;

    public $name;
    public $description;
    public $model_status;

    public string $displayMode = 'default';

    /**
     * Initialisiert die Komponente mit Standardwerten
     */
    public function mount(): void
    {
        $this->model_status = ModelStatus::ACTIVE->value; // 'active'
    }


    public function saveDepartment(): void
    {
        $this->validate();

        try {
            Department::create([
                'name' => $this->name,
                'description' => $this->description,
                'model_status' => $this->model_status,
                'company_id' => auth()->user()->company_id,
                'created_by' => auth()->id(),
            ]);

            $this->resetForm();

            Flux::toast(
                text: __('Department created successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

            $this->dispatch('department-created');

        } catch (\Exception $e) {
            Flux::toast(
                text: __('Error: ') . $e->getMessage(),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    /**
     * Setzt das Formular zurück
     */
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
