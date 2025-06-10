<?php

namespace App\Livewire\Alem\QuickCrud\Stage;

use App\Livewire\Alem\Employee\CreateEmployee;
use App\Livewire\Alem\QuickCrud\Stage\Helper\DataFilter;
use App\Livewire\Alem\QuickCrud\Stage\Helper\ValidateStageForm;
use App\Models\Alem\Employee;
use App\Models\Alem\QuickCrud\Stage;
use App\Traits\Modal\WithPlaceholder;
use App\Traits\Table\WithPerPagePagination;
use Flux\Flux;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class StageForm extends Component
{
    use ValidateStageForm, DataFilter, WithPerPagePagination, WithPlaceholder;

    /**
     * SICHERHEIT: Locked Properties können nicht von außen manipuliert werden
     * @var int|null
     *  ID des authentifizierten Benutzers. Wird von der übergeordneten View übergeben.
     */
    #[Locked]
    public ?int $authUserId = null;

    #[Locked]
    public ?int $currentTeamId = null;

    #[Locked]
    public ?int $companyId = null;

    /** Stage-Felder */
    #[Locked]
    public ?int $stageId = null;
    public ?string $name = null;

    public bool $editing = false;
    public bool $dataLoaded = false;

    public function mount(?int $authUserId = null, ?int $currentTeamId = null, ?int $companyId = null): void
    {
        $this->authUserId = $authUserId ?? auth()->id();
        $this->currentTeamId = $currentTeamId;
        $this->companyId = $companyId;
    }

    /**
     * Event-Handler: Modal öffnen
     */
    #[On('open-stage-manager')]
    public function openStageFormModal(): void
    {
        $this->dataLoaded = true;
        $this->resetFormFields();
    }

    /**
     * Speichert oder aktualisiert eine Stage.
     */
    public function saveStage(): void
    {
        $this->validate();

        try {
            DB::transaction(function () {
                if ($this->editing && $this->stageId) {
                    // Update
                    $stage = $this->getFilteredQuery()
                        ->findOrFail($this->stageId);

                    $stage->update([
                        'name' => $this->name,
                    ]);

                    // Dispatch Event mit ID
                    $this->dispatch('stage-updated', id: $stage->id);

                    Flux::toast(
                        text: __('Stage updated successfully.'),
                        heading: __('Success.'),
                        variant: 'success'
                    );

                } else {
                    // Create
                    $stage = Stage::create([
                        'name' => $this->name,
                        'created_by' => $this->authUserId,
                        'team_id' => $this->currentTeamId,
                        'company_id' => $this->companyId,
                    ]);

                    // Dispatch Event mit ID
                    $this->dispatch('stage-created', id: $stage->id);

                    Flux::toast(
                        text: __('Stage created successfully.'),
                        heading: __('Success.'),
                        variant: 'success'
                    );
                }
            });

            $this->closeStageFormModal();

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error("Fehler beim Erstellen der Stage: " . $e->getMessage(), [
                'exception' => $e,
                'acting_user_id' => $this->authUserId,
                'stage_id' => $this->stageId,
                'formData' => ['name' => $this->name]
            ]);

            Flux::toast(
                text: __('An error occurred while saving the Stage.'),
                heading: __('Error.'),
                variant: 'danger'
            );
        }
    }

    /**
     * Lädt einen Datensatz zur Bearbeitung.
     */
    public function editStage(int $id): void
    {
        try {

            $stage = $this->getFilteredQuery()
                ->findOrFail($id);

            $this->stageId = $stage->id;
            $this->name = $stage->name;

            $this->editing = true;
            $this->resetErrorBag();

        } catch (ModelNotFoundException $e) {

            Flux::toast(
                text: __('Stage not found or you do not have permission to edit it.'),
                heading: __('Error'),
                variant: 'danger'
            );

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
     * Nur Stages, die vom authentifizierten Benutzer erstellt wurden, können gelöscht werden.
     */
    public function deleteStage(int $id): void
    {
        try {

            $stage = $this->getFilteredQuery()
                ->findOrFail($id);

            $stage->delete();

            Flux::toast(
                text: __('Stage deleted successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

            $this->closeStageFormModal();

            $this->dispatch('stage-deleted');

        } catch (ModelNotFoundException $e) {

            Flux::toast(
                text: __('Stage not found or you do not have permission to delete it.'),
                heading: __('Error'),
                variant: 'danger'
            );

        } catch (\Throwable $e) {

            Flux::toast(
                text: __('Cannot delete this stage.'),
                heading: __('Error'),
                variant: 'danger'
            );

        }
    }


    /**
     * Setzt Formular zurück und löscht alle Error-Bags.
     */
    public function resetFormFields(): void
    {
        $this->reset(['stageId', 'name', 'editing']);
        $this->resetErrorBag();
        $this->resetPage();
    }


    /**
     * Setzt das Formular zurück und schließt das Modal.
     */
    public function closeStageFormModal(): void
    {
        $this->modal('create-stage')->close();

        // Setzt verzögert 1ms die Formularfelder zurück
        $this->js("
        setTimeout(() => {
            \$wire.resetFormFields();
        }, 1);
    ");

        $this->dataLoaded = false;
    }

    /**
     * Render-Methode
     */
    public function render(): View
    {
        $stages = collect();

        if ($this->dataLoaded && $this->authUserId) {
            $query = $this->getFilteredQuery()
                ->select('id', 'name', 'updated_at')
                ->orderBy('updated_at', 'desc');

            $stages = $this->applySimplePagination($query);
        }

        return view('livewire.alem.quick-crud.stage.stage-form', [
            'stages' => $stages,
            'filterMode' => $this->filterMode,
        ]);
    }
}
