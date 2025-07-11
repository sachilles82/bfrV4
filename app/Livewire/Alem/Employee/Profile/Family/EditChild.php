<?php

namespace App\Livewire\Alem\Employee\Profile\Family;

use App\Livewire\Alem\Employee\Profile\Family\Helper\HandleCatchError;
use App\Livewire\Alem\Employee\Profile\Family\Helper\ValidateChild;
use App\Models\Alem\Child;
use App\Traits\Enum\GenderOptions;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

#[Lazy(isolate: false)]
class EditChild extends Component
{
    use AuthorizesRequests;
    use ValidateChild, HandleCatchError;
    use GenderOptions;

    // Child identification
    #[Locked]
    public ?int $childId = null;

    // Parent User ID - wird vom Parent Component übergeben
    #[Locked]
    public int $userId;

    // Child Form Fields
    public ?string $name = null;
    public ?string $gender = null;
    public ?string $birthdate = null;
    public ?string $ahv_number = null;
    public ?string $valid_until = null;

    /** Original Daten des Child aus der Datenbank für Vergleiche */
    public array $originalData = [];

    /**
     * Child als Computed Property
     * Wird automatisch gecached und nach Validation Error neu geladen
     */
    #[Computed]
    public function child(): ?Child
    {
        if (!$this->childId) {
            return null;
        }

        return Child::where('id', $this->childId)
            ->where('user_id', $this->userId)
            ->select([
                'id', 'user_id', 'name', 'gender',
                'birthdate', 'ahv_number', 'valid_until'
            ])
            ->first();
    }

    #[On('edit-child-modal')]
    public function openEditChildModal($childId): void
    {
        $this->childId = $childId;

        // Prüfe ob Child existiert und zum User gehört
        if (!$this->child) {
            abort(403, 'Unauthorized access to child data');
        }

        // Lade die Daten in die Form
        $this->loadChildData();

    }

    /**
     * Befülle die Form mit Child Daten
     * Speichere Original-Daten aus der Datenbank für den Vergleich
     */
    protected function loadChildData(): void
    {
        $child = $this->child;

        if (!$child) return;

        // WICHTIG: Speichere Original-Daten für Vergleich
        $this->originalData = [
            'name' => $child->name,
            'gender' => $child->gender?->value,
            'birthdate' => $child->birthdate?->format('Y-m-d'),
            'ahv_number' => $child->ahv_number,
            'valid_until' => $child->valid_until?->format('Y-m-d'),
        ];

        // Setze Form-Felder
        $this->name = $child->name;
        $this->gender = $child->gender?->value;
        $this->birthdate = $child->birthdate?->format('Y-m-d');
        $this->ahv_number = $child->ahv_number;
        $this->valid_until = $child->valid_until?->format('Y-m-d');
    }

    /**
     * Aktualisiert die Child-Daten in der Datenbank
     */
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
                    // Nutze fresh() um sicherzustellen, dass wir das aktuelle Model haben
                    Child::where('id', $this->childId)
                        ->where('user_id', $this->userId)
                        ->update($updateData);
                }
            });

            // Aktualisiere Original-Daten nach erfolgreichem Update
            $this->updateOriginalDataAfterSave();

            // Clear Computed Property Cache nach Update
            unset($this->child);

            $this->closeEditChildModal();

            $this->dispatch('child-updated');

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
        $this->modal('edit-child')->close();

        $this->js("
            setTimeout(() => {
                \$wire.resetFormInputs();
            }, 300);
        ");
    }

    public function resetFormInputs(): void
    {
        $this->resetErrorBag();

        $this->reset([
            'childId',
            'name', 'gender', 'birthdate',
            'ahv_number', 'valid_until',
            'originalData'
        ]);

        // Clear Computed Property Cache
        unset($this->child);
    }

    public function render(): View
    {
        return view('livewire.alem.employee.profile.family.edit-child');
    }

}
