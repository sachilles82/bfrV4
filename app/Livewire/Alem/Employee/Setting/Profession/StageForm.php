<?php

namespace App\Livewire\Alem\Employee\Setting\Profession;

use App\Livewire\Alem\Employee\Setting\Profession\Helper\ValidateStageForm;
use App\Models\Alem\Employee\Setting\Stage;
use App\Traits\Modal\WithPlaceholder;
use App\Traits\Table\WithPerPagePagination;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class StageForm extends Component
{
    use ValidateStageForm, WithPerPagePagination, WithPlaceholder;

    #[Locked]
    public ?int $stageId = null;

    public ?string $name = null;

    public bool $editing = false;

    /**
     * Speichert oder aktualisiert eine Stage.
     */
    public function saveStage(): void
    {
        try {
            // Validierung durchführen – bei Fehlern wird automatisch eine ValidationException geworfen
            $this->validate();

            if ($this->editing && $this->stageId) {
                $stage = Stage::where('created_by', Auth::id())
                    ->findOrFail($this->stageId);

                $stage->update([
                    'name' => $this->name,
                ]);

                Flux::toast(
                    text: __('Stage updated successfully.'),
                    heading: __('Success.'),
                    variant: 'success'
                );
            } else {

                Stage::create([
                    'name' => $this->name,
                ]);

                Flux::toast(
                    text: __('Stage created successfully.'),
                    heading: __('Success.'),
                    variant: 'success'
                );
            }

            $this->dispatch('stage-updated');

        } catch (\Throwable $e) {
            if ($e instanceof ValidationException) {
                throw $e;
            }

            Flux::toast(
                text: __('An error occurred while saving the Stage.'),
                heading: __('Error.'),
                variant: 'error'
            );
        }

        $this->finish();
    }

    /**
     * Lädt einen Datensatz zur Bearbeitung.
     */
    public function editStage(int $id): void
    {
        try {
            $stage = Stage::where('created_by', Auth::id())
                ->findOrFail($id);

            $this->stageId = $stage->id;
            $this->name = $stage->name;
            $this->editing = true;

        } catch (\Throwable $e) {
            Flux::toast(
                text: __('Cannot edit this stage.'),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    /**
     * Löscht eine Stage.
     */
    public function deleteStage($id): void
    {
        try {
            $stage = Stage::where('created_by', Auth::id())
                ->findOrFail($id);

            $stage->delete();
            $this->dispatch('stage-updated');

            Flux::toast(
                text: __('Stage deleted successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

            $this->finish();

        } catch (\Throwable $e) {
            Flux::toast(
                text: __('Cannot delete this stage.'),
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
        $this->modal('create-stage')->close();
        $this->reset(['stageId', 'name', 'editing']);
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
        $query = Stage::where('created_by',
            Auth::id())->orderBy('id');
        $stages = $this->applySimplePagination($query);

        return view('livewire.alem.employee.setting.profession.stage-form', [
            'stages' => $stages,
        ]);
    }
}
