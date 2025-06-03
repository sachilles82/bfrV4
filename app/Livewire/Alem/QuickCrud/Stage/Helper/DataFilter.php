<?php

namespace App\Livewire\Alem\QuickCrud\Stage\Helper;

use App\Models\Alem\QuickCrud\Stage;
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
     * Setzt den Filter-Modus
     *
     * @param string $mode
     * @return void
     */
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
            return Stage::whereRaw('0 = 1');
        }

        return Stage::where('created_by', $this->authUserId);
    }

    private function getTeamQuery(): Builder
    {
        if (!$this->currentTeamId) {
            return Stage::whereRaw('0 = 1');
        }

        return Stage::where('team_id', $this->currentTeamId);
    }

    private function getCompanyQuery(): Builder
    {
        if (!$this->companyId) {
            return Stage::whereRaw('0 = 1');
        }

        return Stage::where('company_id', $this->companyId);
    }
}
