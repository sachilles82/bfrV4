<?php

namespace App\Livewire\Alem\Department;

use App\Enums\Model\ModelStatus;
use App\Livewire\Alem\Department\Helper\ValidateDepartment;
use App\Models\Alem\Department;
use App\Traits\Modal\WithPlaceholder;
use Flux\Flux;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class CreateDepartment extends Component
{
    use ValidateDepartment, WithPlaceholder;

    public ?int $departmentId = null;
    public string $name = '';
    public ?string $description = '';
    public string $status = '';

    /**
     * Initialisiert die Komponente mit Standardwerten
     */
    public function mount(): void
    {
        // Default values
        $this->status = ModelStatus::ACTIVE->value; // 'active'
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
                'model_status' => $this->status,
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
            $this->dispatch('departmentUpdated');
            
        } catch (\Exception $e) {
            Flux::toast(
                text: __('Error: ') . $e->getMessage(),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    /**
     * Öffnet das Modal zum Bearbeiten einer Abteilung
     * 
     * @param int $departmentId Die ID der zu bearbeitenden Abteilung
     */
    public function showEditModal(int $departmentId): void
    {
        $this->resetForm();
        $this->loadDepartmentById($departmentId);
        $this->modal('department-edit')->show();
    }

    /**
     * Hört auf das Event 'edit-department' und öffnet das Modal zum Bearbeiten
     */
    #[On('edit-department')]
    public function onEditDepartment($data): void
    {
        if (isset($data['id'])) {
            $this->showEditModal($data['id']);
        }
    }

    /**
     * Aktualisiert eine bestehende Abteilung
     */
    public function update(): void
    {
        $this->validate();

        try {
            $department = Department::findOrFail($this->departmentId);
            
            $department->update([
                'name' => $this->name,
                'description' => $this->description,
                'model_status' => $this->status,
            ]);

            $this->resetForm();
            
            Flux::toast(
                text: __('Department updated successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

            $this->dispatch('department-updated');
            $this->dispatch('departmentUpdated');
            
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
        $this->reset(['name', 'description', 'status', 'departmentId']);
        $this->resetErrorBag();
        $this->status = ModelStatus::ACTIVE->value;
        $this->modal('create-department')->close();
        $this->modal('department-edit')->close();
    }

    /**
     * Lädt die Daten des Departments anhand seiner ID
     */
    protected function loadDepartmentById($id): void
    {
        $department = Department::findOrFail($id);
        $this->departmentId = $department->id;
        $this->name = $department->name;
        $this->description = $department->description ?? '';
        $this->status = $department->model_status;
    }

    /**
     * Hört auf das Event 'load-department-data' und lädt die Department-Daten
     */
    #[On('load-department-data')]
    public function loadDepartmentData($departmentId, $name, $description, $status): void
    {
        $this->resetForm();
        
        $this->departmentId = $departmentId;
        $this->name = $name;
        $this->description = $description;
        $this->status = $status;
        
        // Modal öffnen
        $this->modal('department-edit')->show();
    }

    // Echtzeit-Validierungsmethoden
    public function updatedName(): void {
        $this->validateOnly('name');
    }

    public function updatedDescription(): void {
        $this->validateOnly('description');
    }

    public function updatedStatus(): void {
        $this->validateOnly('status');
    }

    public function render(): View
    {
        return view('livewire.alem.department.create', [
            'statuses' => ModelStatus::cases(),
        ]);
    }
}
