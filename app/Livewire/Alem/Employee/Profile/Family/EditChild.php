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
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

#[Lazy]
class EditChild extends Component
{
    use AuthorizesRequests;
    use ValidateChild, HandleCatchError;
    use GenderOptions;

    // Child identification
    #[Locked]
    public ?int $childId = null;
    public ?Child $child = null;

    // Parent User ID - wird beim Event gesetzt
    #[Locked]
    public ?int $userId = null;

    // Child Form Fields
    public ?string $name = null;
    public ?string $gender = null;
    public ?string $birthdate = null;
    public ?string $ahv_number = null;
    public ?string $valid_until = null;

    /** Original Daten des Child aus der Datenbank für Vergleiche */
    public array $originalData = [];

    #[On('edit-child-modal')]
    public function openEditChildModal($childId): void
    {
        $this->childId = $childId;

        // $this->authorize('update', [Child::class, $childId]);

        // Lade Child mit User ID
        $this->child = Child::select([
            'id', 'user_id', 'name', 'gender',
            'birthdate', 'ahv_number', 'valid_until'
        ])->findOrFail($this->childId);

        // Setze userId vom Child
        if ($this->userId === null) {
            $this->userId = $this->child->user_id;
        }

        // Sicherheitsprüfung - Child muss zum korrekten User gehören
        if ($this->child->user_id !== $this->userId) {
            abort(403, 'Unauthorized access to child data');
        }

        // Lade die Daten in die Form
        $this->loadChildData();

        // Öffne Flux Modal
        $this->modal('edit-child')->show();
    }

    /**
     * Befülle die Form mit Child Daten
     * Speichere Original-Daten aus der Datenbank für den Vergleich
     */
    protected function loadChildData(): void
    {
        if (!$this->child) return;

        // WICHTIG: Speichere Original-Daten für Vergleich
        $this->originalData = [
            'name' => $this->child->name,
            'gender' => $this->child->gender?->value,
            'birthdate' => $this->child->birthdate?->format('Y-m-d'),
            'ahv_number' => $this->child->ahv_number,
            'valid_until' => $this->child->valid_until?->format('Y-m-d'),
        ];

        // Setze Form-Felder
        $this->name = $this->child->name;
        $this->gender = $this->child->gender?->value;
        $this->birthdate = $this->child->birthdate?->format('Y-m-d');
        $this->ahv_number = $this->child->ahv_number;
        $this->valid_until = $this->child->valid_until?->format('Y-m-d');
    }

    /**
     * Aktualisiert die Child-Daten in der Datenbank
     */
    public function updateChild(): void
    {
        // Prüfe ob überhaupt Änderungen vorliegen
        if (!$this->hasAnyChanges()) {
            Flux::toast(
                text: __('No changes detected.'),
                heading: __('No Update'),
                variant: 'warning'
            );
            return;
        }

        // Validiere NUR die geänderten Felder
        $this->validateOnlyChanged();

        try {
            DB::transaction(function () {
                $updateData = [];

                if ($this->nameHasChanged()) {
                    $updateData['name'] = $this->name;
                }
                if ($this->genderHasChanged()) {
                    $updateData['gender'] = $this->gender;
                }
                if ($this->birthdateHasChanged()) {
                    $updateData['birthdate'] = $this->birthdate;

                    // Neu berechnen wenn Birthdate geändert wurde
                    if ($this->birthdate && !$this->valid_until) {
                        $birthdate = \Carbon\Carbon::parse($this->birthdate);
                        $updateData['valid_until'] = $birthdate->copy()->addYears(18)->format('Y-m-d');
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

            $this->closeEditChildModal();
            $this->dispatch('child-updated');

            Flux::toast(
                text: __('Child updated successfully.'),
                heading: __('Success'),
                variant: 'success'
            );

        } catch (\Throwable $e) {
            $this->handleEditingError($e);
        }
    }

    /**
     * Prüft ob irgendwelche Änderungen vorliegen
     */
    public function hasAnyChanges(): bool
    {
        return $this->nameHasChanged() ||
            $this->genderHasChanged() ||
            $this->birthdateHasChanged() ||
            $this->ahvNumberHasChanged() ||
            $this->validUntilHasChanged();
    }

    /**
     * Validiert nur die geänderten Felder
     */
    private function validateOnlyChanged(): void
    {
        $rules = [];

        if ($this->nameHasChanged()) {
            $rules['name'] = 'required|string|max:255';
        }
        if ($this->genderHasChanged()) {
            $rules['gender'] = 'nullable|string';
        }
        if ($this->birthdateHasChanged()) {
            $rules['birthdate'] = 'nullable|date|before:today';
        }
        if ($this->ahvNumberHasChanged()) {
            $rules['ahv_number'] = 'nullable|string|max:20';
        }
        if ($this->validUntilHasChanged()) {
            $rules['valid_until'] = 'nullable|date|after:today';
        }

        if (!empty($rules)) {
            $this->validate($rules);
        }
    }

    /**
     * Helper-Methoden für Änderungsprüfungen
     */
    private function nameHasChanged(): bool
    {
        return $this->name !== $this->originalData['name'];
    }

    private function genderHasChanged(): bool
    {
        return $this->gender !== $this->originalData['gender'];
    }

    private function birthdateHasChanged(): bool
    {
        return $this->birthdate !== $this->originalData['birthdate'];
    }

    private function ahvNumberHasChanged(): bool
    {
        return $this->ahv_number !== $this->originalData['ahv_number'];
    }

    private function validUntilHasChanged(): bool
    {
        return $this->valid_until !== $this->originalData['valid_until'];
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
            'childId', 'child',
            'name', 'gender', 'birthdate',
            'ahv_number', 'valid_until',
            'originalData'
        ]);
    }

    public function render(): View
    {
        return view('livewire.alem.employee.profile.family.edit-child');
    }

    /**
     * Placeholder für Lazy Loading
     */

}
