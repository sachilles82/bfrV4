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

class Stage extends Model
{
    use ManageDataFilter, ManagesContextAndOwnership;
    use WithRedisCache;
    use HasFactory;

    /**
     * Cache-Schlüssel für dieses Model
     *
     * @var string
     */
    protected string $cacheKey = 'stages_cache';

    /**
     * Cache-Dauer in Sekunden
     *
     * @var int
     */
    protected int $cacheDuration = 43200; // 12 hours

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
        return $this->hasMany(Employee::class, 'stage_id');
    }

//    /**
//     * Holt alle Stages einer Company mit Caching
//     * Wir in den Dropdowns verwendet
//     *
//     * @param int $companyId
//     * @return Collection
//     */
//    public static function getCompanyStages(int $companyId): Collection
//    {
//        return self::cacheCompanyResult($companyId, function() use ($companyId) {
//            return self::where('company_id', $companyId)
//                ->select(['id', 'name'])
//                ->orderBy('name')
//                ->get();
//        });
//    }

    /**
     * Request-level cache, um mehrfache Abfragen derselben Daten innerhalb eines Requests zu vermeiden.
     */
    private static array $requestCache = [];

    /**
     * Die "booting" Methode des Models.
     * Registriert Eloquent-Events, um den Cache (Persistent und Request-Level) automatisch zu leeren.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Diese Funktion wird nach dem Erstellen, Aktualisieren oder Löschen eines Stages aufgerufen.
        $flushAllCachesLogic = function (Stage $stage) {
            if ($stage->company_id) {
                // Log::info("Eloquent Event ausgelöst für Stage ID {$stage->id}. Leere Caches für Company ID {$stage->company_id}.");
                self::flushCompanyCache($stage->company_id); // Diese Methode leert BEIDE Caches
            }
            // Hier könntest du auch team_id oder user_id basierte Caches leeren, falls vorhanden
        };

        static::created($flushAllCachesLogic);
        static::updated($flushAllCachesLogic);
        static::deleted($flushAllCachesLogic);
    }

    /**
     * Generiert den Cache-Schlüssel für den persistenten Cache (z.B. Redis).
     */
    public static function getPersistentCacheKey(?int $companyId): string
    {
        // Ein eindeutiger Präfix für Stages, um Kollisionen mit Profession-Cache zu vermeiden
        return 'stages_for_company_' . $companyId;
    }

    /**
     * Generiert den Schlüssel für den Request-Level-Cache.
     */
    private static function getRequestCacheKey(?int $companyId): string
    {
        return 'request_stages_for_company_' . $companyId;
    }

    /**
     * Leert BEIDE Cache-Ebenen (Persistent und Request-Level) für eine bestimmte Company ID.
     * Wird von den Eloquent-Events (boot-Methode) aufgerufen.
     */
    public static function flushCompanyCache(?int $companyId): void
    {
        if ($companyId) {
            // 1. Persistenten Cache (z.B. Redis) leeren
            Cache::forget(self::getPersistentCacheKey($companyId));
            // Log::info("Persistenter Stage-Cache (Redis) für Company ID {$companyId} geleert.");

            // 2. Request-Level-Cache für diese Company ID leeren
            $requestCacheKey = self::getRequestCacheKey($companyId);
            if (isset(self::$requestCache[$requestCacheKey])) {
                unset(self::$requestCache[$requestCacheKey]);
                // Log::info("Request-Stage-Cache für Company ID {$companyId} geleert.");
            }
        }
    }

    /**
     * Holt alle Stages einer Company mit Caching (Request-Level und Persistent).
     * Wird in den Dropdowns verwendet.
     *
     * @param int|null $companyId // Mache es nullable für Konsistenz und Sicherheit
     * @return Collection
     */
    public static function getCompanyStages(?int $companyId): Collection
    {
        if (!$companyId) {
            // Log::warning("getCompanyStages aufgerufen ohne companyId.");
            return collect(); // Keine Stages ohne Company ID
        }

        $requestCacheKey = self::getRequestCacheKey($companyId);

        // 1. Prüfe den Request-Level Cache
        if (isset(self::$requestCache[$requestCacheKey])) {
            // Log::info("Treffer im Request-Stage-Cache für: " . $requestCacheKey);
            return self::$requestCache[$requestCacheKey];
        }
        // Log::info("Kein Treffer im Request-Stage-Cache für: " . $requestCacheKey);

        // 2. Wenn nicht im Request-Cache, prüfe den persistenten Cache (z.B. Redis)
        $persistentCacheKey = self::getPersistentCacheKey($companyId);

        // Die Logik von self::cacheCompanyResult wird hier durch Cache::rememberForever ersetzt/implementiert
        $stages = Cache::rememberForever($persistentCacheKey, function () use ($companyId, $persistentCacheKey) {
            // Log::info("Kein Treffer im persistenten Stage-Cache für: " . $persistentCacheKey . ". Lade aus Datenbank für Company ID: " . $companyId);
            return self::query() // Nutze self::query() für den Zugriff auf das aktuelle Model
            ->where('company_id', $companyId)
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();
        });

        // 3. Speichere das Ergebnis im Request-Level Cache für nachfolgende Aufrufe innerhalb dieses Requests
        // Log::info("Speichere Stage-Ergebnis im Request-Cache unter: " . $requestCacheKey);
        self::$requestCache[$requestCacheKey] = $stages;

        return $stages;
    }

    /**
     * Optional: Eine Methode, um den gesamten Request-Cache zu leeren.
     * Kann nützlich für Tests oder spezielle Szenarien sein.
     */
    public static function flushEntireRequestCache(): void
    {
        self::$requestCache = [];
        // Log::info("Der gesamte Request-Stage-Cache wurde geleert.");
    }
}
