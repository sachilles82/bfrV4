<?php

namespace App\Livewire\Alem\Employee\Profile\Family;

use App\Models\Alem\Child;
use App\Traits\Enum\GenderOptions;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ChildRow extends Component
{
    use AuthorizesRequests;
    use GenderOptions;

    public Child $child;

    // Child Form Fields für Edit
    public ?string $name = null;
    public ?string $gender = null;
    public ?string $birthdate = null;
    public ?string $ahv_number = null;
    public ?string $valid_until = null;

    public function mount(Child $child): void
    {
        $this->child = $child;
        $this->resetFormInputs();
    }

    public function delete(): void
    {
        try {
            // Authorization check
            // $this->authorize('delete', $this->child);

            $this->child->delete();

            $this->dispatch('child-deleted');

            Flux::toast(
                text: __('Child removed successfully.'),
                heading: __('Success'),
                variant: 'success'
            );

        } catch (\Throwable $e) {
            Flux::toast(
                text: __('Error deleting child.'),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', Rule::enum(\App\Enums\User\Gender::class)],
            'birthdate' => ['nullable', 'date', 'before:today'],
            'ahv_number' => ['nullable', 'string', 'regex:/^\d{3}\.\d{4}\.\d{4}\.\d{2}$/'],
            'valid_until' => ['nullable', 'date', 'after:today'],
        ]);

        try {
            DB::transaction(function () {
                // Berechne valid_until wenn birthdate gesetzt ist
                if ($this->birthdate && !$this->valid_until) {
                    $birthdate = \Carbon\Carbon::parse($this->birthdate);
                    $this->valid_until = $birthdate->copy()->addYears(18)->format('Y-m-d');
                }

                $this->child->update([
                    'name' => $this->name,
                    'gender' => $this->gender,
                    'birthdate' => $this->birthdate,
                    'ahv_number' => $this->ahv_number,
                    'valid_until' => $this->valid_until,
                ]);
            });

            $this->closeEditModal();
            $this->dispatch('child-updated');

            Flux::toast(
                text: __('Child updated successfully.'),
                heading: __('Success'),
                variant: 'success'
            );

        } catch (\Throwable $e) {
            Flux::toast(
                text: __('Error updating child.'),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    public function closeEditModal(): void
    {
        $this->modal('edit-child-' . $this->child->id)->close();
        $this->resetFormInputs();
    }

    private function resetFormInputs(): void
    {
        $this->name = $this->child->name;
        $this->gender = $this->child->gender?->value;
        $this->birthdate = $this->child->birthdate?->format('Y-m-d');
        $this->ahv_number = $this->child->ahv_number;
        $this->valid_until = $this->child->valid_until?->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.alem.employee.profile.family.child-row');
    }
}
