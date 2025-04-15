<?php

namespace App\Services\Address;

use App\Models\Address\City;
use App\Models\Address\State;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class AddressCache
{
    // Lokaler Zwischenspeicher, der innerhalb einer Anfrage geteilt wird
    protected static array $localCache = [];

    // TTL in Sekunden (1 Woche)
    protected const TTL = 604800;

    /**
     * Liefert alle States für ein bestimmtes Land und Team.
     */
    public static function getStates(int $countryId, int $teamId): array
    {
        $cacheKey = sprintf('state-country-%d-team-%d', $countryId, $teamId);

        // Prüfe, ob bereits im lokalen Cache vorhanden
        if (isset(self::$localCache[$cacheKey])) {
            return self::$localCache[$cacheKey];
        }

        $states = Cache::remember($cacheKey, Carbon::now()->addSeconds(self::TTL), function () use ($countryId, $teamId) {
            return State::select(['id', 'name', 'code', 'country_id'])
                ->where('country_id', $countryId)
                ->where(function ($query) use ($teamId) {
                    $query->where('team_id', $teamId)
                        ->orWhere('created_by', 1);
                })
                ->orderBy('id')
                ->get()
                ->toArray();
        });

        self::$localCache[$cacheKey] = $states;

        return $states;
    }

    /**
     * Liefert alle Cities für einen bestimmten State und Team.
     */
    public static function getCities(int $stateId, int $teamId): array
    {
        $cacheKey = sprintf('cities-state-%d-team-%d', $stateId, $teamId);

        if (isset(self::$localCache[$cacheKey])) {
            return self::$localCache[$cacheKey];
        }

        $cities = Cache::remember($cacheKey, Carbon::now()->addSeconds(self::TTL), function () use ($stateId, $teamId) {
            return City::select(['id', 'name', 'state_id'])
                ->where('state_id', $stateId)
                ->where(function ($query) use ($teamId) {
                    $query->where('team_id', $teamId)
                        ->orWhere('created_by', 1);
                })
                ->orderBy('id')
                ->get()
                ->toArray();
        });

        self::$localCache[$cacheKey] = $cities;

        return $cities;
    }

    /**
     * Entfernt den Cache-Eintrag für States.
     */
    public static function forgetStates(int $countryId, int $teamId): void
    {
        $cacheKey = sprintf('state-country-%d-team-%d', $countryId, $teamId);
        Cache::forget($cacheKey);
        unset(self::$localCache[$cacheKey]);
    }

    /**
     * Entfernt den Cache-Eintrag für Cities.
     */
    public static function forgetCities(int $stateId, int $teamId): void
    {
        $cacheKey = sprintf('cities-state-%d-team-%d', $stateId, $teamId);
        Cache::forget($cacheKey);
        unset(self::$localCache[$cacheKey]);
    }
}
