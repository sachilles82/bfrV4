<?php

namespace App\Traits\Enum;

use App\Enums\User\Gender;
use Livewire\Attributes\Computed;

trait GenderOptions
{
    /**
     * Gibt die Optionen für das Geschlecht zurück
     */
    #[Computed]
    public function genderOptions(): array
    {
        return Gender::getGenderOptions();
    }
}
