<?php

namespace App\Livewire\Alem\QuickCrud\Stage;

use App\Livewire\Alem\QuickCrud\Stage\Helper\ValidateStageForm;
use App\Models\Alem\QuickCrud\Stage;
use App\Traits\Modal\WithPlaceholder;
use App\Traits\Table\WithPerPagePagination;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

//#[Lazy(isolate: false)]// Lazy loading isolate führt zusätzliche query aus, deswegen brauch ich es nicht
class StageForm extends Component
{
    use ValidateStageForm, WithPerPagePagination, WithPlaceholder;

    /**
     * Datenfilter-Modus: 'user', 'team' oder 'company'
     *
     * @var string
     */
    public string $filterMode = 'user';

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
     * Event-Handler: Modal öffnen
     */
    #[On('open-modal-manager')]
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

                $this->dispatch('stage-updated');
//                $this->dispatch('stage-updated', id: $stage->id);

                Flux::toast(
                    text: __('Stage updated successfully.'),
                    heading: __('Success.'),
                    variant: 'success'
                );

            } else {

                $created = Stage::create([
                    'name' => $this->name
                ]);

                $this->dispatch('stage-created', id: $created->id);

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
                'acting_user_id' => $this->authUserId ?? auth()->id(),
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
     * Setzt den Filter-Modus
     *
     * @param string $mode
     * @return void
     */
    public function setFilterMode(string $mode): void
    {
        if (in_array($mode, ['user', 'team', 'company'])) {
            $this->filterMode = $mode;
            $this->resetPage();
        }
    }

    /**
     * Gibt die gefilterte Query zurück basierend auf dem aktuellen Modus
     *
     * @return Builder
     */
    private function getFilteredQuery()
    {
        return match ($this->filterMode) {
            'team' => Stage::teamData(),
            'company' => Stage::companyData(),
            default => Stage::userData(),
        };
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

        if ($this->dataLoaded && Auth::check()) {
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
