<?php

namespace App\Traits\Model;

use Illuminate\Database\Eloquent\Builder;

trait DataFilter
{
    /**
     * Datenfilter-Modus: 'user', 'team' oder 'company'
     *
     * @var string
     */
    public string $filterMode = 'user';

    /**
     * Required properties that must be set in the using class:
     * - authUserId: int|null
     * - currentTeamId: int|null
     * - companyId: int|null
     */

    /**
     * Setzt den Filter-Modus
     *
     * @param string $mode
     * @return void
     */
    public function setFilterMode(string $mode): void
    {
        if (in_array($mode, ['user', 'team', 'company'])) {
            $this->filterMode = $mode;
            if (method_exists($this, 'resetPage')) {
                $this->resetPage();
            }
        }
    }

    /**
     * Gibt die gefilterte Query zurück basierend auf dem aktuellen Modus
     * Nutzt die übergebenen Properties statt Auth::user()
     * 
     * @param Builder $query The query to filter
     * @param string|null $tablePrefix Optional table prefix for the where clauses
     * @return Builder
     */
    protected function getFilteredQuery(Builder $query, ?string $tablePrefix = null): Builder
    {
        $prefix = $tablePrefix ? $tablePrefix . '.' : '';
        
        return match ($this->filterMode) {
            'team' => $this->getTeamQuery($query, $prefix),
            'company' => $this->getCompanyQuery($query, $prefix),
            default => $this->getUserQuery($query, $prefix),
        };
    }

    private function getUserQuery(Builder $query, string $prefix): Builder
    {
        if (!$this->authUserId) {
            return $query->whereRaw('0 = 1');
        }

        return $query->where($prefix . 'created_by', $this->authUserId);
    }

    private function getTeamQuery(Builder $query, string $prefix): Builder
    {
        if (!$this->currentTeamId) {
            return $query->whereRaw('0 = 1');
        }

        return $query->where($prefix . 'team_id', $this->currentTeamId);
    }

    private function getCompanyQuery(Builder $query, string $prefix): Builder
    {
        if (!$this->companyId) {
            return $query->whereRaw('0 = 1');
        }

        return $query->where($prefix . 'company_id', $this->companyId);
    }
} 