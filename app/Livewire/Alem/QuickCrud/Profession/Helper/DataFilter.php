<?php

namespace App\Livewire\Alem\QuickCrud\Profession\Helper;

use App\Models\Alem\QuickCrud\Profession;
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
     *
     * @return Builder
     */
    private function getFilteredQuery(): Builder
    {
        return match ($this->filterMode) {
            'team' => Profession::teamData(),
            'company' => Profession::companyData(),
            default => Profession::userData(),
        };
    }

}
