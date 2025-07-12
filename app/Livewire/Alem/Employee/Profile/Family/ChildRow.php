<?php

namespace App\Livewire\Alem\Employee\Profile\Family;

use App\Livewire\Alem\Employee\Profile\Family\Helper\HandleCatchError;
use App\Livewire\Alem\Employee\Profile\Family\Helper\ValidateChild;
use App\Models\Alem\Child;
use App\Traits\Enum\GenderOptions;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ChildRow extends Component
{
    use AuthorizesRequests;
    use ValidateChild, HandleCatchError;
    use GenderOptions;

    public Child $child;

    // Form fields
    public $name = null;
    public $gender = null;
    public $birthdate = null;
    public $ahv_number = null;
    public $valid_until = null;

    /** Original Daten des Child aus der Datenbank für Vergleiche */
    public array $originalData = [];

    public function mount(): void
    {
        $this->initializeFormData();
    }

    protected function initializeFormData(): void
    {
        // WICHTIG: Speichere Original-Daten für Vergleich - KEINE empty strings, nur null
        $this->originalData = [
            'name' => $this->child->name,
            'gender' => $this->child->gender?->value,
            'birthdate' => $this->child->birthdate?->format('Y-m-d'),
            'ahv_number' => $this->child->ahv_number,
            'valid_until' => $this->child->valid_until?->format('Y-m-d'),
        ];

        // Setze Form-Felder NUR mit den originalData Werten
        $this->name = $this->originalData['name'];
        $this->gender = $this->originalData['gender'];
        $this->birthdate = $this->originalData['birthdate'];
        $this->ahv_number = $this->originalData['ahv_number'];
        $this->valid_until = $this->originalData['valid_until'];
    }

    public function updateChild(): void
    {
        // Prüfe ob überhaupt Änderungen vorliegen (Methode aus ValidateChild Trait)
        if (!$this->hasAnyChanges()) {
            Flux::toast(
                text: __('No changes detected.'),
                heading: __('No Update'),
                variant: 'warning'
            );
            return;
        }

        // Validiere NUR die geänderten Felder (Methode aus ValidateChild Trait)
        $this->validateOnlyChanged();

        try {
            DB::transaction(function () {
                $updateData = [];

                // Nutze die Methoden aus ValidateChild Trait
                if ($this->nameHasChanged()) {
                    $updateData['name'] = $this->name;
                }
                if ($this->genderHasChanged()) {
                    $updateData['gender'] = $this->gender;
                }
                if ($this->birthdateHasChanged()) {
                    $updateData['birthdate'] = $this->birthdate;

                    // Neu berechnen wenn Birthdate geändert wurde
                    if ($this->birthdate && !$this->validUntilHasChanged()) {
                        $birthdate = \Carbon\Carbon::parse($this->birthdate);
                        $updateData['valid_until'] = $birthdate->copy()->addYears(18);
                    }
                }
                if ($this->ahvNumberHasChanged()) {
                    $updateData['ahv_number'] = $this->ahv_number;
                }
                if ($this->validUntilHasChanged()) {
                    $updateData['valid_until'] = $this->valid_until;
                }

                // Update nur wenn Felder geändert wurden
                if (!empty($updateData)) {
                    $this->child->update($updateData);
                }
            });

            // Aktualisiere Original-Daten nach erfolgreichem Update
            $this->updateOriginalDataAfterSave();

            // Refresh das Model
            $this->child->refresh();

            $this->closeEditChildModal();

            Flux::toast(
                text: __('Child updated successfully.'),
                heading: __('Success'),
                variant: 'success'
            );

        } catch (\Throwable $e) {
            // Nutze HandleCatchError Trait für Fehlerbehandlung
            $this->handleEditingError($e);
        }
    }

    /**
     * Aktualisiert die Original-Daten nach erfolgreichem Save
     */
    private function updateOriginalDataAfterSave(): void
    {
        $this->originalData = [
            'name' => $this->name,
            'gender' => $this->gender,
            'birthdate' => $this->birthdate,
            'ahv_number' => $this->ahv_number,
            'valid_until' => $this->valid_until,
        ];
    }

    /**
     * Schließt das Modal und bereinigt alle Daten
     */
    public function closeEditChildModal(): void
    {

        Flux::modals()->close();

        $this->js("
        setTimeout(() => {
              \$wire.resetFormInputs();
            }, 1);
        ");
    }

    public function resetFormInputs(): void
    {
        $this->resetErrorBag();
    }

    public function render(): View
    {
        return view('livewire.alem.employee.profile.family.child-row');
    }
}
