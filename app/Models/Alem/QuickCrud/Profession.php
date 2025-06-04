<?php

namespace App\Models\Alem\QuickCrud;

use App\Models\Alem\Employee;
use App\Traits\Cache\WithRedisCache;
use App\Traits\Model\ManageDataFilter;
use App\Traits\Model\ManagesContextAndOwnership;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Profession extends Model
{
    use ManageDataFilter, ManagesContextAndOwnership, WithRedisCache;
    use HasFactory;

    /**
     * Cache-Schlüssel für dieses Model
     *
     * @var string
     */
    protected $cacheKey = 'professions_cache';

    /**
     * Cache-Dauer in Sekunden (-1 for forever)
     *
     * @var int
     */
    protected $cacheDuration = 43200; // 12 hours

    /**
     * Mass assignable attributes
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'company_id',
        'team_id',
        'created_by',
    ];

    /**
     * Relation zu Mitarbeitern
     *
     * @return HasMany
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'profession_id');
    }

    /**
     * Holt alle Professions einer Company mit Caching
     * Wir in den Dropdowns verwendet
     *
     * @param int $companyId
     * @return Collection
     */
    /**
     * Request-level cache, um mehrfache Abfragen derselben Daten innerhalb eines Requests zu vermeiden.
     * Das Array wird pro Request neu initialisiert.
     */
    private static array $requestCache = [];

    /**
     * Die "booting" Methode des Models.
     * Registriert Eloquent-Events, um den Cache (Redis und Request-Level) automatisch zu leeren.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Diese Funktion wird nach dem Erstellen, Aktualisieren oder Löschen einer Profession aufgerufen.
        $flushAllCachesLogic = function (Profession $profession) {
            if ($profession->company_id) {
                // Log::info("Eloquent Event ausgelöst für Profession ID {$profession->id}. Leere Caches für Company ID {$profession->company_id}.");
                self::flushCompanyCache($profession->company_id); // Diese Methode leert BEIDE Caches
            }
        };

        static::created($flushAllCachesLogic);
        static::updated($flushAllCachesLogic);
        static::deleted($flushAllCachesLogic); // Bei SoftDeletes ist $profession->company_id noch verfügbar.
        // Bei HardDeletes ggf. 'deleting' Event verwenden, falls company_id dann nicht mehr sicher zugreifbar ist.
    }

    /**
     * Generiert den Cache-Schlüssel für den Redis-Cache (oder einen anderen persistenten Cache).
     */
    public static function getPersistentCacheKey(?int $companyId): string
    {
        return 'professions_for_company_' . $companyId;
    }

    /**
     * Generiert den Schlüssel für den Request-Level-Cache.
     */
    private static function getRequestCacheKey(?int $companyId): string
    {
        return 'request_professions_for_company_' . $companyId;
    }

    /**
     * Leert BEIDE Cache-Ebenen (Persistent und Request-Level) für eine bestimmte Company ID.
     * Diese Methode wird von den Eloquent-Events (boot-Methode) aufgerufen.
     */
    public static function flushCompanyCache(?int $companyId): void
    {
        if ($companyId) {
            // 1. Persistenten Cache (z.B. Redis) leeren
            Cache::forget(self::getPersistentCacheKey($companyId));
            // Log::info("Persistenter Cache (Redis) für Company ID {$companyId} geleert.");

            // 2. Request-Level-Cache für diese Company ID leeren
            $requestCacheKey = self::getRequestCacheKey($companyId);
            if (isset(self::$requestCache[$requestCacheKey])) {
                unset(self::$requestCache[$requestCacheKey]);
                // Log::info("Request-Cache für Company ID {$companyId} geleert.");
            }
        }
    }

    /**
     * Holt alle Professionen für eine bestimmte Firma.
     * Verwendet eine dreistufige Cache-Strategie:
     * 1. Request-Level Cache (schnellstes, pro Request)
     * 2. Persistenter Cache (z.B. Redis)
     * 3. Datenbankabfrage (Fallback)
     */
    public static function getCompanyProfessions(?int $companyId): Collection
    {
        if (!$companyId) {
            return collect();
        }

        $requestCacheKey = self::getRequestCacheKey($companyId);

        // 1. Prüfe den Request-Level Cache
        if (isset(self::$requestCache[$requestCacheKey])) {
            // Log::info("Treffer im Request-Cache für: " . $requestCacheKey);
            return self::$requestCache[$requestCacheKey];
        }
        // Log::info("Kein Treffer im Request-Cache für: " . $requestCacheKey . ". Prüfe persistenten Cache.");

        // 2. Wenn nicht im Request-Cache, prüfe den persistenten Cache (z.B. Redis)
        $persistentCacheKey = self::getPersistentCacheKey($companyId);

        $professions = Cache::rememberForever($persistentCacheKey, function () use ($companyId, $persistentCacheKey) {
            // Log::info("Kein Treffer im persistenten Cache für: " . $persistentCacheKey . ". Lade aus Datenbank für Company ID: " . $companyId);
            return self::query()
                ->where('company_id', $companyId)
                ->select(['id', 'name']) // Sicherstellen, dass nur benötigte Spalten selektiert werden
                ->orderBy('name')
                ->get();
        });

        // 3. Speichere das Ergebnis im Request-Level Cache für nachfolgende Aufrufe innerhalb dieses Requests
        // Log::info("Speichere Ergebnis im Request-Cache unter: " . $requestCacheKey);
        self::$requestCache[$requestCacheKey] = $professions;

        return $professions;
    }

    /**
     * Optional: Eine Methode, um den gesamten Request-Cache zu leeren.
     * Wird normalerweise nicht benötigt, wenn gezielt pro Schlüssel geleert wird.
     * Kann nützlich für Tests oder spezielle Szenarien sein (z.B. am Ende eines Octane/RoadRunner Requests).
     */
    public static function flushEntireRequestCache(): void
    {
        self::$requestCache = [];
        // Log::info("Der gesamte Request-Level-Cache wurde geleert.");
    }
}
