<?php

namespace App\Traits\Employee;

use App\Enums\Employee\EmployeeStatus;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait für die Verwaltung des Mitarbeiterstatus
 *
 * Stellt Methoden bereit, um den Status eines Mitarbeiters zu prüfen und zu ändern.
 */
trait EmployeeStatusManagement
{
    /**
     * Überprüft, ob der Mitarbeiter im Probezeitstatus ist.
     */
    public function isOnProbation(): bool
    {
        return $this->employee_status === EmployeeStatus::PROBATION;
    }

    /**
     * Überprüft, ob der Mitarbeiter im Onboarding-Status ist.
     */
    public function isOnboarding(): bool
    {
        return $this->employee_status === EmployeeStatus::ONBOARDING;
    }

    /**
     * Überprüft, ob der Mitarbeiter voll angestellt ist.
     */
    public function isEmployed(): bool
    {
        return $this->employee_status === EmployeeStatus::EMPLOYED;
    }

    /**
     * Überprüft, ob der Mitarbeiter im Urlaub ist.
     */
    public function isOnLeave(): bool
    {
        return $this->employee_status === EmployeeStatus::ONLEAVE;
    }

    /**
     * Überprüft, ob der Mitarbeiter die Firma verlassen hat.
     */
    public function hasLeft(): bool
    {
        return $this->employee_status === EmployeeStatus::LEAVE;
    }

    /**
     * Setzt den Status des Mitarbeiters.
     *
     * @param  EmployeeStatus  $status  Der neue Status
     */
    public function setEmployeeStatus(EmployeeStatus $status): self
    {
        $this->update(['employee_status' => $status]);

        return $this;
    }

    /**
     * Setzt den Mitarbeiter auf Probezeit.
     */
    public function setProbation(): self
    {
        return $this->setEmployeeStatus(EmployeeStatus::PROBATION);
    }

    /**
     * Setzt den Mitarbeiter auf Onboarding.
     */
    public function setOnboarding(): self
    {
        return $this->setEmployeeStatus(EmployeeStatus::ONBOARDING);
    }

    /**
     * Setzt den Mitarbeiter auf vollzeit angestellt.
     */
    public function setEmployed(): self
    {
        return $this->setEmployeeStatus(EmployeeStatus::EMPLOYED);
    }

    /**
     * Setzt den Mitarbeiter auf Urlaub.
     */
    public function setOnLeave(): self
    {
        return $this->setEmployeeStatus(EmployeeStatus::ONLEAVE);
    }

    /**
     * Setzt den Mitarbeiter auf verlassen.
     */
    public function setLeft(): self
    {
        return $this->setEmployeeStatus(EmployeeStatus::LEAVE);
    }

    /**
     * Scope für Mitarbeiter auf Probezeit.
     *
     * @param  Builder  $query
     */
    public function scopeOnProbation($query): Builder
    {
        return $query->where('employee_status', EmployeeStatus::PROBATION);
    }

    /**
     * Scope für Mitarbeiter im Onboarding.
     *
     * @param  Builder  $query
     */
    public function scopeOnboarding($query): Builder
    {
        return $query->where('employee_status', EmployeeStatus::ONBOARDING);
    }

    /**
     * Scope für vollzeit angestellte Mitarbeiter.
     *
     * @param  Builder  $query
     */
    public function scopeEmployed($query): Builder
    {
        return $query->where('employee_status', EmployeeStatus::EMPLOYED);
    }

    /**
     * Scope für Mitarbeiter im Urlaub.
     *
     * @param  Builder  $query
     */
    public function scopeOnLeave($query): Builder
    {
        return $query->where('employee_status', EmployeeStatus::ONLEAVE);
    }

    /**
     * Scope für Mitarbeiter, die die Firma verlassen haben.
     *
     * @param  Builder  $query
     */
    public function scopeLeft($query): Builder
    {
        return $query->where('employee_status', EmployeeStatus::LEAVE);
    }
}
