<?php

namespace App\Livewire\Alem\Department;

use App\Livewire\Alem\Department\Helper\ValidateDepartment;
use App\Livewire\Alem\Department\Helper\WithDepartmentSorting;
use App\Models\Alem\Department;
use App\Traits\Model\WithModelStatusOptions;
use Flux\Flux;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Component;

class EditDepartment extends Component
{
    use ValidateDepartment, WithDepartmentSorting, AuthorizesRequests,
        WithModelStatusOptions;

    public ?int $departmentId = null; //!Muss in jeder Komponente sein, die ein WithModelStatusOptions hat

    public $name;
    public $description;
    public $model_status;


    /**
     * Lade das Department zur Bearbeitung
     */
    #[On('edit-department')]
    public function loadDepartment($id): void
    {
        try {
            $department = Department::findOrFail($id);

            // $this->authorize('update', $department);

            $this->departmentId = $department->id;
            $this->name = $department->name;
            $this->description = $department->description ?? '';
            $this->model_status = $department->model_status;

            $this->modal('edit-department')->show();

        } catch (AuthorizationException $ae) {
            Flux::toast(
                text: __('You are not authorized to edit this department.'),
                heading: __('Error'),
                variant: 'danger'
            );
        } catch (\Exception $e) {
            Flux::toast(
                text: __('Error loading department: ') . $e->getMessage(),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    public function updateDepartment(): void
    {
        $this->validate();

        try {
            $department = Department::findOrFail($this->departmentId);

            // $this->authorize('update', $department);

            $department->update([
                'name' => $this->name,
                'description' => $this->description,
                'model_status' => $this->model_status,
            ]);

            $this->closeModal();

            Flux::toast(
                text: __('Department updated successfully.'),
                heading: __('Success'),
                variant: 'success'
            );

            $this->dispatch('department-updated');

        } catch (AuthorizationException $ae) {
            Flux::toast(
                text: __('You are not authorized to update this department.'),
                heading: __('Error'),
                variant: 'danger'
            );
        } catch (\Exception $e) {
            Flux::toast(
                text: __('Error updating department: ') . $e->getMessage(),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    public function closeModal(): void
    {
        $this->reset(['departmentId', 'name', 'description', 'model_status']);
        $this->modal('edit-department')->close();
    }

    public function render(): View
    {
        return view('livewire.alem.department.edit', [
            'modelStatusOptions' => $this->modelStatusOptions,
        ]);
    }
}
