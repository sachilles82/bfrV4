<?php

namespace App\Models\Address;

use App\Traits\Cache\AdvancedCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Country extends Model
{
    use AdvancedCache;

    /**
     * Cache-Konfiguration für dieses Model
     * @var int
     * @var string
     */
    protected int $cacheDuration = -1; // Forever, da Countries sich selten ändern
    protected string $cachePrefix = 'countries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'currency',
        'phonecode',
    ];

    /**
     * Überschreibe die Cache-Kontexte, da Countries global sind
     */
    protected function getCacheContexts(): array
    {
        return []; // Countries sind global, keine company/team/user Kontexte
    }

    /**
     * Überschreibe Auto-Flush Kontexte
     */
    protected function getAutoFlushContexts(): array
    {
        return []; // Keine kontextbasierten Flushes für Countries
    }

    /**
     * Überschreibe die flush Methode für Countries
     */
    protected function flushRelevantCaches(): void
    {
        // Flush alle globalen Caches bei Create/Update/Delete
        static::flushAllCaches();
    }

    /**
     * Get all countries for dropdown (id, name, code)
     *
     * @return Collection
     */
    public static function getCountriesDropdown(): Collection
    {
        return static::getCachedGlobal(function () {
            return static::query()
                ->select(['id', 'name', 'code'])
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Manuell den Cache leeren (z.B. nach Import oder in Nova)
     *
     * Verwendung:
     * Country::flushAllCaches();
     */

    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }
}
