<?php

namespace App\Livewire\Alem\Employee\Profile\Sos\Helper;

use App\Traits\Error\BaseHandleCatchError;

/**
 * Spezifischer Error Handler für SOS Contact-bezogene Components
 */
trait HandleCatchError
{
    use BaseHandleCatchError;

    /**
     * Behandelt Fehler beim Speichern/Editieren von SOS Kontakten
     */
    private function handleEditingError(\Throwable $e): void
    {
        $this->handleError($e, 'SOS Kontaktdaten');
    }

    /**
     * Alias für Konsistenz
     */
    private function handleSavingError(\Throwable $e): void
    {
        $this->handleEditingError($e);
    }

    /**
     * Implementierung für SOS Contact Components
     */
    protected function collectIdentificationData(): array
    {
        $data = [
            'contact_id' => null,
            'employee_user_id' => null,
        ];

        // Versuche Contact ID zu finden
        if (property_exists($this, 'contact') && $this->contact) {
            $data['contact_id'] = $this->contact->id ?? null;
            $data['employee_user_id'] = $this->contact->user_id ?? null;
        } elseif (property_exists($this, 'contactId')) {
            $data['contact_id'] = $this->contactId;
        }

        // Falls user_id noch nicht gefunden, suche weiter
        if (!$data['employee_user_id'] && property_exists($this, 'userId')) {
            $data['employee_user_id'] = $this->userId;
        }

        return $data;
    }

    /**
     * Implementierung für SOS Contact Components
     */
    protected function collectFormData(): array
    {
        $knownContactFields = [
            'name', 'gender', 'related', 'phone', 'email'
        ];

        $formData = [];

        foreach ($knownContactFields as $field) {
            if (property_exists($this, $field)) {
                $formData[$field] = $this->$field ?? null;
            }
        }

        return $formData;
    }
}
