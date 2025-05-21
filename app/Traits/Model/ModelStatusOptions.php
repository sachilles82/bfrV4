<?php

namespace App\Traits\Model;

use App\Enums\Model\ModelStatus;
use Livewire\Attributes\Computed;

trait ModelStatusOptions
{
    /**
     * Gibt die Optionen für den Modelstatus zurück
     */
    #[Computed]
    public function modelStatusOptions(): array
    {
        return ModelStatus::getModelOptions();
    }

}
