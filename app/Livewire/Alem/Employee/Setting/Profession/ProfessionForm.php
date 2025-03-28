<?php

namespace App\Livewire\Alem\Employee\Setting\Profession;

use App\Livewire\Alem\Employee\Setting\Profession\Helper\ValidateProfessionForm;
use App\Models\Alem\Employee\Setting\Profession;
use App\Traits\Modal\WithPlaceholder;
use App\Traits\Table\WithPerPagePagination;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ProfessionForm extends Component
{
    use ValidateProfessionForm, WithPerPagePagination, WithPlaceholder;

    #[Locked]
    public ?int $professionId = null;

    public ?string $name = null;
    public bool $editing = false;

    /**
     * Speichert oder aktualisiert eine Profession.
     */
    public function saveProfession(): void
    {
        try {

            $this->validate();

            if ($this->editing && $this->professionId) {
                $profession = Profession::where('created_by', Auth::id())
                    ->findOrFail($this->professionId);

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

            $this->dispatch('profession-updated');

        } catch (\Throwable $e) {
            // Validierungsfehler direkt weiterwerfen
            if ($e instanceof ValidationException) {
                throw $e;
            }

            Flux::toast(
                text: __('An error occurred while saving the Profession.'),
                heading: __('Error.'),
                variant: 'error'
            );
        }

        $this->finish();
    }

    /**
     * Lädt einen Datensatz zur Bearbeitung.
     */
    public function editProfession(int $id): void
    {
        try {
            $profession = Profession::where('created_by', Auth::id())
                ->findOrFail($id);

            $this->professionId = $profession->id;
            $this->name = $profession->name;
            $this->editing = true;

        } catch (\Throwable $e) {
            Flux::toast(
                text: __('Cannot edit this profession.'),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    /**
     * Löscht eine Profession.
     */
    public function deleteProfession($id): void
    {
        try {
            $profession = Profession::where('created_by', Auth::id())
                ->findOrFail($id);

            $profession->delete();
            $this->finish();
            $this->dispatch('profession-updated');

            Flux::toast(
                text: __('Profession deleted successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

        } catch (\Throwable $e) {
            Flux::toast(
                text: __('Cannot delete this profession.'),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    /**
     * Setzt den Formularstatus zurück und schließt das Modal.
     */
    public function finish(): void
    {
        $this->modal('create-profession')->close();
        $this->reset(['professionId', 'name', 'editing']);
        $this->resetValidation();
    }

    /**
     * Wird vom "Cancel"-Button aufgerufen.
     */
    public function resetForm(): void
    {
        $this->finish();
    }

    public function render(): View
    {
        $query = Profession::where('created_by',
                 Auth::id())->orderBy('id');
        $professions = $this->applySimplePagination($query);

        return view('livewire.alem.employee.setting.profession.profession-form', [
            'professions' => $professions,
        ]);
    }
}
