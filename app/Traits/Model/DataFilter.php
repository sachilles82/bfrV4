<?php

namespace App\Traits\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Trait für flexible Datenfilterung basierend auf User, Team oder Company
 *
 * Bietet drei Hauptmethoden:
 * - userData(): Filtert nur Daten des authentifizierten Benutzers
 * - teamData(): Filtert nur Daten des aktuellen Teams
 * - companyData(): Filtert nur Daten der aktuellen Company
 */
trait DataFilter
{
    /**
     * Filtert Query nach Daten des authentifizierten Benutzers
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeUserData(Builder $query): Builder
    {
        $userId = Auth::id();

        if (!$userId) {
            return $query->whereRaw('0 = 1');
        }

        return $query->where($this->getTable() . '.created_by', $userId);
    }

    /**
     * Filtert Query nach Daten des aktuellen Teams
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeTeamData(Builder $query): Builder
    {
        $user = Auth::user();

        if (!$user || !$user->current_team_id) {
            return $query->whereRaw('0 = 1');
        }

        return $query->where($this->getTable() . '.team_id', $user->current_team_id);
    }

    /**
     * Filtert Query nach Daten der aktuellen Company
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeCompanyData(Builder $query): Builder
    {
        $user = Auth::user();

        if (!$user || !$user->company_id) {
            return $query->whereRaw('0 = 1');
        }

        return $query->where($this->getTable() . '.company_id', $user->company_id);
    }
}
