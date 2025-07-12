<?php

namespace App\Livewire\Alem\Employee\Profile\Personal\Helper;

use App\Traits\Error\BaseHandleCatchError;

/**
 * Spezifischer Error Handler für Personal-bezogene Components
 */
trait HandleCatchError
{
    use BaseHandleCatchError;

    /**
     * Behandelt Fehler beim Speichern von Personaldaten
     */
    private function handleEditingError(\Throwable $e): void
    {
        $this->handleError($e, 'Personaldaten');
    }

    /**
     * Implementierung für Personal Components
     */
    protected function collectIdentificationData(): array
    {
        return [
            'bearbeiteter_user_id' => $this->userId ?? null,
            'employee_id' => property_exists($this, 'employee') && $this->employee ? $this->employee->id : null,
        ];
    }

    /**
     * Implementierung für Personal Components
     */
    protected function collectFormData(): array
    {
        $knownPersonalFields = [
            'birthdate', 'ahv_number', 'country_id',
            'hometown', 'religion', 'residence_permit'
        ];

        $formData = [];

        foreach ($knownPersonalFields as $field) {
            if (property_exists($this, $field)) {
                $formData[$field] = $this->$field ?? null;
            }
        }

        return $formData;
    }
}
