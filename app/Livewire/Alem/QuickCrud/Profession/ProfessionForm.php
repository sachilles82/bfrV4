<?php

namespace App\Livewire\Alem\QuickCrud\Profession;

use App\Livewire\Alem\QuickCrud\Profession\Helper\DataFilter;
use App\Livewire\Alem\QuickCrud\Profession\Helper\ValidateProfessionForm;
use App\Models\Alem\QuickCrud\Profession;
use App\Traits\Modal\WithPlaceholder;
use App\Traits\Table\WithPerPagePagination;
use Flux\Flux;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

//#[Lazy]
class ProfessionForm extends Component
{
    use ValidateProfessionForm, DataFilter, WithPerPagePagination, WithPlaceholder;

    /**
     * Profession ID (gesperrt für Sicherheit)
     *
     * @var int|null
     */
    #[Locked]
    public ?int $professionId = null;

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
        $this->authUserId = $authUserId ?? auth()->id();
        $this->currentTeamId = $currentTeamId;
        $this->companyId = $companyId;
    }

    /**
     * Event-Handler: Modal öffnen
     */
    #[On('open-profession-manager')]
    public function openProfessionFormModal(): void
    {
        $this->resetFormData();
        $this->dataLoaded = true;
    }

    /**
     * Speichert oder aktualisiert eine Profession.
     */
    public function saveProfession(): void
    {
        $this->validate();

        try {
            DB::transaction(function () {

                if ($this->editing && $this->professionId) {

                    $profession = $this->getFilteredQuery()
                        ->findOrFail($this->professionId);

                    $profession->update([
                        'name' => $this->name,
                    ]);

                    $this->dispatch('profession-updated', id: $profession->id);

                    Flux::toast(
                        text: __('Profession updated successfully.'),
                        heading: __('Success.'),
                        variant: 'success'
                    );

                } else {

                    $profession = Profession::create([
                        'name' => $this->name,

                        /**
                         * company_id, team_id, created_by werden automatisch
                         * durch ManagesContextAndOwnership Trait gesetzt
                         */
                    ]);

                    $this->dispatch('profession-created', id: $profession->id);

                    Flux::toast(
                        text: __('Profession created successfully.'),
                        heading: __('Success.'),
                        variant: 'success'
                    );
                }
            });

            $this->closeProfessionFormModal();

//            \Log::info('🔥 Cache Warming...');
            Profession::getCompanyProfessions($this->companyId); // Befüllt den Cache


        } catch (\Throwable $e) {

            DB::rollBack();
//            Log::error("Fehler beim Erstellen der Profession: " . $e->getMessage(), [
//                'exception' => $e,
//                'acting_user_id' => $this->authUserId,
//                'profession_id' => $this->professionId,
//                'formData' => collect($this->only([
//                    'name'
//                ]))->toArray()
//            ]);

            Flux::toast(
                text: __('An error occurred while saving the Profession.'),
                heading: __('Error.'),
                variant: 'danger'
            );
        }
    }

    /**
     * Lädt einen Datensatz zur Bearbeitung.
     */
    public function editProfession(int $id): void
    {
        try {
            $profession = $this->getFilteredQuery()
                ->findOrFail($id);

            $this->professionId = $profession->id;
            $this->name = $profession->name;

            $this->editing = true;
            $this->resetErrorBag();

        } catch (ModelNotFoundException $e) {

            Flux::toast(
                text: __('Profession not found or you do not have permission to edit it.'),
                heading: __('Error'),
                variant: 'danger'
            );

        } catch (\Throwable $e) {

            Flux::toast(
                text: __('You cannot edit this Profession.'),
                heading: __('Error'),
                variant: 'danger'
            );

        }
    }

    /**
     * Löscht eine Profession
     *
     * Cache wird automatisch durch Model Delete Event geleert!
     */
    public function deleteProfession($id): void
    {
        try {

            $profession = $this->getFilteredQuery()
                ->findOrFail($id);

            $profession->delete();

            Flux::toast(
                text: __('Profession deleted successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

            $this->closeProfessionFormModal();

            $this->dispatch('profession-deleted');

        } catch (ModelNotFoundException $e) {

            Flux::toast(
                text: __('Profession not found or you do not have permission to delete it.'),
                heading: __('Error'),
                variant: 'danger'
            );

        } catch (\Throwable $e) {

            Flux::toast(
                text: __('You cannot delete this Profession.'),
                heading: __('Error'),
                variant: 'danger'
            );

        }
    }

    /**
     * Schließt das Modal und bereinigt alle Daten
     */
    public function resetFormData(): void
    {
        $this->resetErrorBag();
        $this->reset([
            'professionId', 'name', 'editing'
        ]);
        $this->resetPage();
    }

    /**
     * Setzt das Formular zurück und schließt das Modal.
     */
    public function closeProfessionFormModal(): void
    {
        $this->modal('create-profession')->close();

        // Setzt verzögert 1ms die Formularfelder zurück
        $this->js("
        setTimeout(() => {
            \$wire.resetFormData();
        }, 1);
    ");

        $this->dataLoaded = false;
    }

    public function render(): View
    {
        $professions = collect();

        if ($this->dataLoaded && $this->authUserId) {
            $query = $this->getFilteredQuery()
                ->select('id', 'name', 'updated_at')
                ->orderBy('updated_at', 'desc');

            $professions = $this->applySimplePagination($query);
        }

        return view('livewire.alem.quick-crud.profession.profession-form', [
            'professions' => $professions,
            'filterMode' => $this->filterMode,
        ]);
    }
}
