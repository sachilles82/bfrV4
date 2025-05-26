<?php

namespace App\Livewire\Alem\QuickCrud\Stage;

use App\Livewire\Alem\QuickCrud\Stage\Helper\ValidateStageForm;
use App\Models\Alem\QuickCrud\Stage;
use App\Traits\Modal\WithPlaceholder;
use App\Traits\Table\WithPerPagePagination;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class StageForm extends Component
{
    use ValidateStageForm, WithPerPagePagination, WithPlaceholder;

    #[Locked]
    public ?int $stageId = null;

    public ?string $name = null;
    public bool $editing = false;

    /**
     * Flag, ob das Modal (und damit die Daten) bereits geladen wurden
     */
    public bool $dataLoaded = false;

    /**
     * Event-Handler: Modal öffnen
     */
    #[On('open-modal-manager')]
    public function openModal(): void
    {
        $this->dataLoaded = true;
        $this->resetPage();
        $this->reset(['stageId', 'name', 'editing']);
        $this->resetValidation();
    }

    /**
     * Speichert oder aktualisiert eine Stage.
     */
    public function saveStage(): void
    {
        $this->validate();

        try {
            if ($this->editing && $this->stageId) {
                $user = Auth::user();
                $stage = Stage::query()
                    ->where(function ($q) use ($user) {
                        $q->where('created_by', $user->id)
                            ->orWhere('created_by', 1);

                        if ($user->company_id) {
                            $q->orWhereHas('creator', fn($q2) =>
                            $q2->where('company_id', $user->company_id)
                            );
                        }
                    })
                    ->findOrFail($this->stageId);

                $stage->update(['name' => $this->name]);
                $this->dispatch('stage-updated');
                Flux::toast(
                    text: __('Stage updated successfully.'),
                    heading: __('Success.'),
                    variant: 'success'
                );
            } else {
                $created = Stage::create(['name' => $this->name]);
                $this->dispatch('stage-created', id: $created->id);
                Flux::toast(
                    text: __('Stage created successfully.'),
                    heading: __('Success.'),
                    variant: 'success'
                );
            }

            $this->resetForm();
            $this->resetPage();

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Flux::toast(
                text: __('An error occurred while saving the Stage.'),
                heading: __('Error.'),
                variant: 'error'
            );
        }
    }

    /**
     * Lädt einen Datensatz zur Bearbeitung.
     */
    public function editStage(int $id): void
    {
        try {
            $user = Auth::user();
            $stage = Stage::query()
                ->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)
                        ->orWhere('created_by', 1);

                    if ($user->company_id) {
                        $q->orWhereHas('creator', fn($q2) =>
                        $q2->where('company_id', $user->company_id)
                        );
                    }
                })
                ->findOrFail($id);

            $this->stageId = $stage->id;
            $this->name = $stage->name;
            $this->editing = true;
            $this->resetValidation();

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
    public function deleteStage(int $id): void
    {
        try {
            $user = Auth::user();
            $stage = Stage::query()
                ->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)
                        ->orWhere('created_by', 1);

                    if ($user->company_id) {
                        $q->orWhereHas('creator', fn($q2) =>
                        $q2->where('company_id', $user->company_id)
                        );
                    }
                })
                ->findOrFail($id);

            $stage->delete();

            Flux::toast(
                text: __('Stage deleted successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

            $this->resetPage();
            $this->dispatch('stage-deleted');

        } catch (\Throwable $e) {
            Flux::toast(
                text: __('Cannot delete this stage.'),
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
        $this->reset(['stageId', 'name', 'editing']);
        $this->resetValidation();
    }

    /**
     * Schließt das Modal und setzt Formular zurück.
     */
    public function finish(): void
    {
        $this->modal('create-stage')->close();
        $this->resetForm();
    }

    /**
     * Render-Methode
     */
    public function render(): View
    {
        $stages = collect();

        if ($this->dataLoaded) {
            $user = Auth::user();

            $query = Stage::query()
                ->select('id', 'name', 'created_by')
                ->with('creator:id,name')
                ->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id ?? 0)
                        ->orWhere('created_by', 1);

                    if ($user && $user->company_id) {
                        $q->orWhereHas('creator', fn($q2) =>
                        $q2->where('company_id', $user->company_id)
                        );
                    }
                })
                ->orderBy('updated_at', 'desc');

            $stages = $this->applySimplePagination($query);
        }

        return view('livewire.alem.quick-crud.stage.stage-form', [
            'stages' => $stages,
        ]);
    }
}
