<?php

namespace App\Livewire\Alem\QuickCrud\Profession\Helper;

use App\Models\Alem\QuickCrud\Profession;
use Illuminate\Database\Eloquent\Builder;

trait DataFilter
{
    public string $filterMode = 'user';

    public function setFilterMode(string $mode): void
    {
        if (in_array($mode, ['user', 'team', 'company'])) {
            $this->filterMode = $mode;
            $this->resetPage();
        }
    }

    /**
     * Gibt die gefilterte Query zurück basierend auf dem aktuellen Modus
     * Nutzt die übergebenen Properties statt Auth::user()
     */
    private function getFilteredQuery(): Builder
    {
        return match ($this->filterMode) {
            'team' => $this->getTeamQuery(),
            'company' => $this->getCompanyQuery(),
            default => $this->getUserQuery(),
        };
    }

    private function getUserQuery(): Builder
    {
        if (!$this->authUserId) {
            return Profession::whereRaw('0 = 1');
        }

        return Profession::where('created_by', $this->authUserId);
    }

    private function getTeamQuery(): Builder
    {
        if (!$this->currentTeamId) {
            return Profession::whereRaw('0 = 1');
        }

        return Profession::where('team_id', $this->currentTeamId);
    }

    private function getCompanyQuery(): Builder
    {
        if (!$this->companyId) {
            return Profession::whereRaw('0 = 1');
        }

        return Profession::where('company_id', $this->companyId);
    }
}
