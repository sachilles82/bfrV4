<?php

namespace App\Livewire\Alem\Employee\Profile\Sos;

use App\Livewire\Alem\Employee\Profile\Sos\Helper\HandleCatchError;
use App\Livewire\Alem\Employee\Profile\Sos\Helper\ValidateContact;
use App\Models\Alem\SOS;
use App\Traits\Enum\GenderOptions;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ContactRow extends Component
{
    use AuthorizesRequests;
    use ValidateContact, HandleCatchError;
    use GenderOptions;

    public SOS $contact;

    // Form fields
    public $name = null;
    public $gender = null;
    public $related = null;
    public $phone = null;
    public $email = null;

    /** Original Daten des SOS Contact aus der Datenbank für Vergleiche */
    public array $originalData = [];

    public function mount(): void
    {
        $this->initializeFormData();
    }

    protected function initializeFormData(): void
    {
        // WICHTIG: Speichere Original-Daten für Vergleich - KEINE empty strings, nur null
        $this->originalData = [
            'name' => $this->contact->name,
            'gender' => $this->contact->gender?->value,
            'related' => $this->contact->related,
            'phone' => $this->contact->phone,
            'email' => $this->contact->email,
        ];

        // Setze Form-Felder NUR mit den originalData Werten
        $this->name = $this->originalData['name'];
        $this->gender = $this->originalData['gender'];
        $this->related = $this->originalData['related'];
        $this->phone = $this->originalData['phone'];
        $this->email = $this->originalData['email'];
    }

    public function updateContact(): void
    {
        // Prüfe ob überhaupt Änderungen vorliegen (Methode aus ValidateContact Trait)
        if (!$this->hasAnyChanges()) {
            Flux::toast(
                text: __('No changes detected.'),
                heading: __('No Update'),
                variant: 'warning'
            );
            return;
        }

        // Validiere NUR die geänderten Felder (Methode aus ValidateContact Trait)
        $this->validateOnlyChanged();

        try {
            DB::transaction(function () {
                $updateData = [];

                // Nutze die Methoden aus ValidateContact Trait
                if ($this->nameHasChanged()) {
                    $updateData['name'] = $this->name;
                }
                if ($this->genderHasChanged()) {
                    $updateData['gender'] = $this->gender;
                }
                if ($this->relatedHasChanged()) {
                    $updateData['related'] = $this->related;
                }
                if ($this->phoneHasChanged()) {
                    $updateData['phone'] = $this->sanitizePhoneNumber($this->phone);
                }
                if ($this->emailHasChanged()) {
                    $updateData['email'] = $this->email;
                }

                // Update nur wenn Felder geändert wurden
                if (!empty($updateData)) {
                    $this->contact->update($updateData);
                }
            });

            // Aktualisiere Original-Daten nach erfolgreichem Update
            $this->updateOriginalDataAfterSave();

            // Refresh das Model
            $this->contact->refresh();

            $this->closeEditContactModal();

            Flux::toast(
                text: __('Contact updated successfully.'),
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
            'related' => $this->related,
            'phone' => $this->phone,
            'email' => $this->email,
        ];
    }

    /**
     * Schließt das Modal und bereinigt alle Daten
     */
    public function closeEditContactModal(): void
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
        return view('livewire.alem.employee.profile.sos.contact-row');
    }
}
