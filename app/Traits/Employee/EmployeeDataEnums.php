<?php

namespace App\Traits\Employee;

use App\Enums\Employee\NoticePeriod;
use App\Enums\Employee\Probation;
use Livewire\Attributes\Computed;

use App\Enums\Employee\CivilStatus;
use App\Enums\Employee\Religion;
use App\Enums\Employee\Residence;

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

    /**
     * Gibt die Optionen für die Kündigungsfrist zurück
     * @return array Array mit Optionen für Dropdown-Menüs und andere UI-Elemente
     */
    #[Computed]
    public function noticePeriodOptions(): array
    {
        return NoticePeriod::getNoticePeriodOptions();
    }
}
