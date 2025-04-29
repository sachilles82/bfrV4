<?php

namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CompanyCacheService
{
//    /**
//     * Zeit bis der Cache ungültig wird (1 Woche)
//     */
//    public static int $cacheDuration = 604800; // 7 Tage in Sekunden
//
//    /**
//     * Lädt Daten aus dem Cache oder mit dem Callback und speichert sie für eine Woche
//     */
//    public static function remember(string $type, int $companyId, callable $callback)
//    {
//        $cacheKey = self::getCacheKey($type, $companyId);
//
//        // Prüfen, ob Daten im Cache vorhanden sind
//        $cacheExists = Cache::has($cacheKey);
////        if ($cacheExists) {
////            Log::info("CACHE HIT: Daten für {$type} (Firma {$companyId}) aus Cache geladen");
////        } else {
////            Log::info("CACHE MISS: Daten für {$type} (Firma {$companyId}) aus Datenbank geladen");
////        }
//
//        return Cache::remember($cacheKey, now()->addSeconds(self::$cacheDuration), $callback);
//    }
//
//    /**
//     * Invalidiert den Cache für einen bestimmten Datentyp in einer Firma
//     */
//    public static function invalidate(string $type, int $companyId): void
//    {
//        $cacheKey = self::getCacheKey($type, $companyId);
////        Log::info("CACHE INVALIDIERT: {$cacheKey}");
//        Cache::forget($cacheKey);
//    }
//
//    /**
//     * Invalidiert alle Caches für eine Firma
//     */
//    public static function invalidateAll(int $companyId): void
//    {
//        // Liste der bekannten Typen, die potenziell im Cache sein könnten
//        $knownTypes = ['teams', 'departments', 'supervisors', 'roles', 'professions', 'stages', 'employees'];
//
////        Log::info("CACHE ALLE INVALIDIERT: Firma {$companyId}");
//        foreach ($knownTypes as $type) {
//            self::invalidate($type, $companyId);
//        }
//    }
//
//    /**
//     * Erzeugt einen einheitlichen Cache-Key
//     */
//    private static function getCacheKey(string $type, int $companyId): string
//    {
//        return "company_{$companyId}_{$type}_list";
//    }
}
