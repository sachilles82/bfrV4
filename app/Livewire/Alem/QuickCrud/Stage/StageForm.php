<?php

namespace App\Livewire\Alem\QuickCrud\Stage;

use App\Livewire\Alem\QuickCrud\Stage\Helper\ValidateStageForm;
use App\Models\Alem\QuickCrud\Stage;
use App\Models\User;
use App\Traits\Modal\WithPlaceholder;
use App\Traits\Table\WithPerPagePagination;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
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

    public Collection $stages;

    /**
     * Initialisiert die Komponente.
     * Wir initialisieren $stages hier als leere Collection,
     * damit die Blade-View beim ersten Rendern keine Fehler wirft.
     */
    public function mount(): void
    {
        $this->stages = new Collection();
    }

    /**
     * Speichert oder aktualisiert eine Stage.
     */
    public function saveStage(): void
    {
        try {
            $this->validate();

            if ($this->editing && $this->stageId) {
                $user = Auth::user();
                $stage = Stage::query()
                    ->where(function ($query) use ($user) {
                        $query->where('created_by', $user->id)
                            ->orWhere('created_by', 1);
                        if ($user->company_id) {
                            $query->orWhereHas('creator', function ($q) use ($user) {
                                $q->where('company_id', $user->company_id);
                            });
                        }
                    })
                    ->findOrFail($this->stageId);

                $stage->update([
                    'name' => $this->name,
                ]);

                $this->dispatch('stage-updated');
                Flux::toast(text: __('Stage updated successfully.'), heading: __('Success.'), variant: 'success');
            } else {
                $stage = Stage::create([
                    'name' => $this->name,
                    // 'created_by' => Auth::id(), // Sicherstellen, dass created_by gesetzt wird
                ]);
                $this->dispatch('stage-created', id: $stage->id);
                Flux::toast(text: __('Stage created successfully.'), heading: __('Success.'), variant: 'success');
            }
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Flux::toast(text: __('An error occurred while saving the Stage.'), heading: __('Error.'), variant: 'error');
        }

        $this->finish();
        $this->loadStages();
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

            // Optional: Wenn das Modal geöffnet wird, um zu bearbeiten,
            // könnten wir hier auch die Stages laden, falls sie noch nicht geladen wurden.
            // if ($this->stages->isEmpty()) {
            //     $this->loadStages();
            // }

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
            $this->finish();


            $this->dispatch('stage-deleted');

            Flux::toast(
                text: __('Stage deleted successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

            $this->loadStages();

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

    /**
     * Lädt die Stage-Daten für die Tabelle.
     * Diese Methode wird durch das Event 'open-modal-manager' aufgerufen.
     */
    #[On('open-modal-manager')]
    public function loadStages(): void
    {
        $authUser = Auth::user();

        if (! $authUser) {
            $this->stages = new Collection();
            return;
        }

        $authUserId = $authUser->id;
        $authUserCompanyId = $authUser->company_id;

        $query = Stage::query()
            ->select('id', 'name', 'created_by')
            ->with('creator:id,name')
            ->where(function ($subQuery) use ($authUserId, $authUserCompanyId) {
                $subQuery->where('created_by', $authUserId)
                    ->orWhere('created_by', 1);

                if ($authUserCompanyId) {
                    $subQuery->orWhereHas('creator', function ($userQuery) use ($authUserCompanyId) {
                        $userQuery->where('company_id', $authUserCompanyId);
                    });
                }
            })
            ->orderBy('id');

        $paginator = $this->applySimplePagination($query);
        $this->stages = $paginator->getCollection();
    }

    public function render(): View
    {
        return view('livewire.alem.quick-crud.stage.stage-form');
    }
}
