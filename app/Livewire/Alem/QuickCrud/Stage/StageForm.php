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

//#[Lazy(isolate: false)]// Lazy loading isolate führt zusätzliche query aus, deswegen brauch ich es nicht
class StageForm extends Component
{
    use ValidateStageForm, DataFilter, WithPerPagePagination, WithPlaceholder;

    /**
     * Stage ID (gesperrt für Sicherheit)
     *
     * @var int|null
     */
    #[Locked]
    public ?int $stageId = null;

    public ?string $name = null;

    public bool $editing = false;
    public bool $dataLoaded = false;

    /**
     * ID des authentifizierten Benutzers.
     * Wird von der übergeordneten View übergeben.
     */
    // Properties für die übergebenen Daten
    public ?int $authUserId = null;
    public ?int $currentTeamId = null;
    public ?int $companyId = null;

    public function mount(?int $authUserId = null, ?int $currentTeamId = null, ?int $companyId = null): void
    {
        // Wenn keine authUserId übergeben wird, versuche, sie vom aktuellen Benutzer zu holen
        // Dies dient als Fallback, falls die Komponente an anderer Stelle ohne Übergabe verwendet wird.
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
            DB::beginTransaction();

            if ($this->editing && $this->stageId) {

                $stage = $this->getFilteredQuery()
                    ->findOrFail($this->stageId);

                $stage->update([
                    'name' => $this->name
                ]);

                // Manuell den Company-Cache leeren
                Stage::flushCompanyCache($this->companyId);

                $this->dispatch('stage-updated', id: $stage->id);

                Flux::toast(
                    text: __('Stage updated successfully.'),
                    heading: __('Success.'),
                    variant: 'success'
                );

            } else {

                $stage = Stage::create([
                    'name' => $this->name
                ]);

                // Manuell den Company-Cache leeren
                Stage::flushCompanyCache($this->companyId);

                $this->dispatch('stage-created', id: $stage->id)->to(CreateEmployee::class);;;

                Flux::toast(
                    text: __('Stage created successfully.'),
                    heading: __('Success.'),
                    variant: 'success'
                );
            }

            DB::commit();

            $this->closeStageFormModal();

        } catch (\Throwable $e) {

            DB::rollBack();
            Log::error("Fehler beim Erstellen der Stage: " . $e->getMessage(), [
                'exception' => $e,
                'acting_user_id' => $this->authUserId,
                'stage_id' => $this->stageId,
                'formData' => collect($this->only([
                    'name'
                ]))->toArray()
            ]);

            Flux::toast(
                text: __('An error occurred while saving the stage.'),
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

            // Manuell den Company-Cache leeren
            Stage::flushCompanyCache($this->companyId);

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
