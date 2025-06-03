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

//    /**
//     * Holt alle Professions einer Company mit Caching
//     * Wir in den Dropdowns verwendet
//     *
//     * @param int $companyId
//     * @return Collection
//     */
//    public static function getCompanyProfessions(int $companyId)
//    {
//        return self::cacheCompanyResult($companyId, function() use ($companyId) {
//            return self::where('company_id', $companyId)
//                ->select(['id', 'name'])
//                ->orderBy('name')
//                ->get();
//        });
//    }

// app/Models/Alem/QuickCrud/Profession.php
    public static function getCompanyProfessions(int $companyId): EloquentCollection
    {
        $staticCacheKey = 'company_professions_for_company_' . $companyId;
        // Log::debug("ProfessionModel: Attempting to getCompanyProfessions for company {$companyId}. Static cache key: {$staticCacheKey}");

        if (isset(self::$requestCacheForCompanyProfessions[$staticCacheKey])) {
            // Log::debug("ProfessionModel: Serving getCompanyProfessions for company {$companyId} from STATIC request cache.");
            return self::$requestCacheForCompanyProfessions[$staticCacheKey];
        }

        // Log::debug("ProfessionModel: STATIC request cache miss for getCompanyProfessions (company {$companyId}). Proceeding to Redis/DB.");

        $professions = self::cacheCompanyResult($companyId, function () use ($companyId) {
            // Log::info("ProfessionModel: Cache CALLBACK RUNNING for getCompanyProfessions (company {$companyId}) - DB query will occur.");
            return self::where('company_id', $companyId)
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();
        });

        self::$requestCacheForCompanyProfessions[$staticCacheKey] = $professions;
        // Log::debug("ProfessionModel: Stored getCompanyProfessions for company {$companyId} in STATIC request cache. Count: " . $professions->count());

        return $professions;
    }
}
