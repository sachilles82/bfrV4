<?php

namespace App\Traits\Model;

use App\Enums\Model\ModelStatus;
use Livewire\Attributes\Computed;

trait ModelStatusOptions
{
    /**
     * Gibt alle Optionen für den Modelstatus zurück (inkl. TRASHED).
     * Wird typischerweise für Filter in Tabellen verwendet.
     */
    #[Computed]
    public function modelStatusOptions(): array
    {
        return ModelStatus::getModelOptions(false); // explizit false für alle Optionen
    }

    /**
     * Gibt die Optionen für den Modelstatus zurück, die in Formularen verwendet werden sollen (exkl. TRASHED).
     */
    #[Computed]
    public function modelStatusOptionsForForms(): array
    {
        return ModelStatus::getModelOptions(true); // true, um TRASHED auszuschließen
    }

}
