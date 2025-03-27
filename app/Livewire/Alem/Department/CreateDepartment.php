<?php

namespace App\Livewire\Alem\Department;

use App\Enums\Model\ModelStatus;
use App\Livewire\Alem\Department\Helper\ValidateDepartment;
use App\Livewire\Alem\Department\Helper\WithDepartmentModelStatus;
use App\Models\Alem\Department;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CreateDepartment extends Component
{
    use ValidateDepartment, WithDepartmentModelStatus;

    // Anzeigeart: 'default' = Index-Seite, 'dropdown' = im Dropdown
    public string $displayMode = 'default';

    public $departmentId = null; // ID um die Validierung für Create/Update zu unterscheiden
    public $name = '';
    public $description = '';
    public $model_status = '';

    /**
     * Initialisiert die Komponente mit Standardwerten
     */
    public function mount(): void
    {
        $this->model_status = ModelStatus::ACTIVE->value;
    }

    /**
     * Gibt die verfügbaren Status-Optionen zurück
     */
    public function getModelStatusOptionsProperty()
    {
        $statuses = [];

        foreach (ModelStatus::cases() as $status) {
            $statuses[] = [
                'value' => $status->value,
                'label' => $status->label(),
                'colors' => $status->colors(),
                'icon' => $status->icon(),
            ];
        }

        return $statuses;
    }

    /**
     * Öffnet das Modal zum Erstellen einer Abteilung
     */
    public function create(): void
    {
        $this->resetForm();
        $this->modal('create-department')->show();
    }

    /**
     * Speichert eine neue Abteilung
     */
    public function save(): void
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
        return view('livewire.alem.department.create');
    }
}
