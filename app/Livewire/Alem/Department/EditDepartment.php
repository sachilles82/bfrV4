<?php

namespace App\Livewire\Alem\Department;

use App\Enums\Model\ModelStatus;
use App\Livewire\Alem\Department\Helper\ValidateDepartment;
use App\Livewire\Alem\Department\Helper\WithDepartmentModelStatus;
use App\Models\Alem\Department;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class EditDepartment extends Component
{
    use ValidateDepartment, WithDepartmentModelStatus;

    public $departmentId;
    public $name = '';
    public $description = '';
    public $model_status = '';

    /**
     * Gibt die verfügbaren Status-Optionen zurück
     */
    public function getStatusesProperty()
    {
        return \App\Enums\Model\ModelStatus::cases();
    }

    /**
     * Event-Listener für das Laden der Abteilungsdaten
     */
    #[On('edit-department')]
    public function loadDepartment($id): void
    {
        $this->departmentId = $id;
        $department = Department::findOrFail($id);

        $this->name = $department->name;
        $this->description = $department->description ?? '';
        $this->model_status = $department->model_status;

        $this->dispatch('modal-open', id: 'edit-department');
    }

    /**
     * Abteilung aktualisieren
     */
    public function update(): void
    {
        // Authorization
        if (!auth()->user()->can('update', Department::class)) {
            return;
        }

        $this->validate();

        $department = Department::find($this->departmentId);

        if (!$department) {
            Flux::toast(
                text: __('Department not found.'),
                heading: __('Error'),
                variant: 'danger'
            );
            return;
        }

        $department->update([
            'name' => $this->name,
            'description' => $this->description,
            'model_status' => $this->model_status,
        ]);

        // Event auslösen für die Aktualisierung der Tabelle
        $this->dispatch('department-updated');
        $this->dispatchModelEvent('updated');

        // Modal schließen
        $this->dispatch('modal-close', id: 'edit-department');
        $this->reset(['departmentId', 'name', 'description', 'status']);

        Flux::toast(
            text: __('Department updated successfully.'),
            heading: __('Success'),
            variant: 'success'
        );
    }

    /**
     * Gets all available model status options with their labels, colors, and icons
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
     * Modal schließen
     */
    public function closeModal(): void
    {
        $this->dispatch('modal-close', id: 'edit-department');
        $this->reset(['departmentId', 'name', 'description', 'model_status']);
    }

    public function render(): View
    {
        return view('livewire.alem.department.edit');
    }
}
