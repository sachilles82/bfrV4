<?php

namespace App\Traits\Model;

use Illuminate\Database\Eloquent\Builder;

/**
 * Alternative zum ManageDataFilter für Komponenten mit übergebenen User-Daten
 */
trait ManageDataFilter
{
    /**
     * Filtert Query nach Daten eines bestimmten Benutzers
     */
    public function scopeUserDataFor(Builder $query, int $userId): Builder
    {
        return $query->where($this->getTable() . '.created_by', $userId);
    }

    /**
     * Filtert Query nach Daten eines bestimmten Teams
     */
    public function scopeTeamDataFor(Builder $query, int $teamId): Builder
    {
        return $query->where($this->getTable() . '.team_id', $teamId);
    }

    /**
     * Filtert Query nach Daten einer bestimmten Company
     */
    public function scopeCompanyDataFor(Builder $query, int $companyId): Builder
    {
        return $query->where($this->getTable() . '.company_id', $companyId);
    }
}
