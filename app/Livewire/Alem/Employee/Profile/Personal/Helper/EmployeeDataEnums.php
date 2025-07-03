<?php

namespace App\Livewire\Alem\Employee\Profile\Personal\Helper;

use App\Enums\Employee\CivilStatus;
use App\Enums\Employee\Probation;
use App\Enums\Employee\Religion;
use App\Enums\Employee\Residence;
use Livewire\Attributes\Computed;

trait EmployeeDataEnums
{
    /**
     * Gibt die Optionen für Religion zurück
     * @return array Array mit value/label Paaren für Religion
     */
    #[Computed]
    public function religionOptions(): array
    {
        return Religion::getReligionOptions();
    }

    /**
     * Gibt die Optionen für den Zivilstand zurück
     * @return array Array mit value/label Paaren für Zivilstand
     */
    #[Computed]
    public function civilStatusOptions(): array
    {
        return CivilStatus::getCivilOptions();
    }

    /**
     * Gibt die Optionen für die Aufenthaltsbewilligung zurück
     * @return array Array mit value/label Paaren für Aufenthaltsbewilligung
     */
    #[Computed]
    public function residencePermitOptions(): array
    {
        return Residence::getResidenceOptions();
    }

    /**
     * Gibt die Optionen für die Probezeit zurück
     * @return array Array mit value/label Paaren für Probezeit
     */
    #[Computed]
    public function probationOptions(): array
    {
        return Probation::getProbationOptions();
    }
}
