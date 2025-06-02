<?php

namespace App\Livewire\Alem\QuickCrud\Profession;

use App\Livewire\Alem\QuickCrud\Profession\Helper\ValidateProfessionForm;
use App\Models\Alem\QuickCrud\Profession;
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

class ProfessionForm extends Component
{
    use ValidateProfessionForm, WithPerPagePagination, WithPlaceholder;

    /**
     * Datenfilter-Modus: 'user', 'team' oder 'company'
     *
     * @var string
     */
    public string $filterMode = 'user';

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
     * Event-Handler: Modal öffnen
     */
    #[On('open-profession-manager')]
    public function openProfessionFormModal(): void
    {
        $this->dataLoaded = true;
        $this->resetFormFields();
    }

    /**
     * Speichert oder aktualisiert eine Profession.
     */
    public function saveProfession(): void
    {
        $this->validate();

        try {
            DB::beginTransaction();

            if ($this->editing && $this->professionId) {

                $profession =$this->getFilteredQuery()
                    ->findOrFail($this->professionId);

                $profession->update([
                    'name' => $this->name,
                ]);

                $this->dispatch('profession-updated');

                Flux::toast(
                    text: __('Profession updated successfully.'),
                    heading: __('Success.'),
                    variant: 'success'
                );

            } else {

                $profession = Profession::create([
                    'name' => $this->name,
                ]);

                $this->dispatch('profession-created', id: $profession->id);

                Flux::toast(
                    text: __('Profession created successfully.'),
                    heading: __('Success.'),
                    variant: 'success'
                );
            }

            DB::commit();

            $this->closeProfessionFormModal();

        } catch (\Throwable $e) {

            DB::rollBack();
            Log::error("Fehler beim Erstellen der Profession: " . $e->getMessage(), [
                'exception' => $e,
                'acting_user_id' => $this->authUserId ?? auth()->id(),
                'profession_id' => $this->professionId,
                'formData' => collect($this->only([
                    'name'
                ]))->toArray()
            ]);

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
                text: __('Cannot edit this Profession.'),
                heading: __('Error'),
                variant: 'danger'
            );

        }
    }

    /**
     * Löscht eine Profession.
     *  Nur Profession, die vom authentifizierten Benutzer erstellt wurden, können gelöscht werden.
     */
    public function deleteProfession($id): void
    {
        try {

            $profession =$this->getFilteredQuery()
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
                text: __('Cannot delete this Profession.'),
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
            'team' => Profession::teamData(),
            'company' => Profession::companyData(),
            default => Profession::userData(),
        };
    }

    /**
     * Setzt Formular zurück und löscht alle Error-Bags.
     */
    public function resetFormFields(): void
    {
        $this->reset(['professionId', 'name', 'editing']);
        $this->resetErrorBag();
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
            \$wire.resetFormFields();
        }, 1);
    ");

        $this->dataLoaded = false;
    }

    public function render(): View
    {
        $professions = collect();

        if ($this->dataLoaded && Auth::check()) {
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
