<?php

namespace App\Livewire\Alem\Employee\Setting\Profession;

use App\Livewire\Alem\Employee\Setting\Profession\Helper\ValidateProfessionForm;
use App\Models\Alem\Employee\Setting\Profession;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class ProfessionForm extends Component
{
    use ValidateProfessionForm;

    public $professionId = null;
    public $name = null;
    public $editing = false;

    public function saveProfession(): void
    {
        $this->validate();

        try {
            if ($this->editing && $this->professionId) {
                $profession = Profession::where('created_by', Auth::id())->findOrFail($this->professionId);
                $profession->update([
                    'name' => $this->name,
                ]);

                Flux::toast(
                    text: __('Profession updated successfully.'),
                    heading: __('Success.'),
                    variant: 'success'
                );
            } else {
                Profession::create([
                    'name' => $this->name,
                ]);

                Flux::toast(
                    text: __('Profession created successfully.'),
                    heading: __('Success.'),
                    variant: 'success'
                );
            }

            $this->resetForm();
            // Informiere andere Komponenten, z. B. den Employee-Form, dass sich die Liste geändert hat.
            $this->dispatch('professionUpdated');

        } catch (\Exception $e) {
            Flux::toast(
                text: __('Error saving profession: ') . $e->getMessage(),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    public function editProfession($id): void
    {
        try {
            $profession = Profession::where('created_by', Auth::id())->findOrFail($id);
            $this->professionId = $profession->id;
            $this->name = $profession->name;
            $this->editing = true;
        } catch (\Exception $e) {
            Flux::toast(
                text: __('Cannot edit this profession.'),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    public function deleteProfession($id): void
    {
        try {
            $profession = Profession::where('created_by', Auth::id())->findOrFail($id);
            $profession->delete();

            Flux::toast(
                text: __('Profession deleted successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

            $this->emit('professionUpdated');
        } catch (\Exception $e) {
            Flux::toast(
                text: __('Cannot delete this profession.'),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    public function resetForm(): void
    {
        $this->reset(['professionId', 'name', 'editing']);
        $this->resetValidation();
        // Optional: Schließe den Modal über ein Browser-Event
        $this->dispatch('closeProfessionModal');
    }

    public function render(): View
    {
        $professions = Profession::where('created_by', Auth::id())
            ->orderBy('name')->get();
        return view('livewire.alem.employee.setting.profession.profession-form', [
            'professions' => $professions,
        ]);
    }
}
