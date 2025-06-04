<?php

namespace App\Models\Alem\QuickCrud;

use App\Models\Alem\Employee;
use App\Traits\Cache\WithAdvancedCache;
use App\Traits\Model\ManageDataFilter;
use App\Traits\Model\ManagesContextAndOwnership;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profession extends Model
{
    use HasFactory;
    use ManageDataFilter, ManagesContextAndOwnership, WithAdvancedCache;

    /**
     * Cache-Konfiguration für dieses Model
     * @var int
     * @var string
     */
    protected int $cacheDuration = 43200; // 12 Stunden, Cache-Dauer in Sekunden (-1 for forever)
    protected string $cachePrefix = 'professions';

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
     * Holt alle Professions einer Company mit Cache
     * Nutzt den neuen generischen Cache-Trait
     */
    public static function getCompanyProfessions(?int $companyId): Collection
    {
        return static::getCachedByCompany($companyId, function() use ($companyId) {
            return static::query()
                ->where('company_id', $companyId)
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Leert den Company-Cache manuell
     * Wird von anderen Components aufgerufen nach Create/Update/Delete
     */
    public static function flushCompanyCache(?int $companyId): void
    {
        if (!$companyId) return;

        $instance = new static;
        $instance->flushCacheContext('company', $companyId);
    }

//    /**
//     * Holt Professions für ein spezifisches Team
//     */
//    public static function getTeamProfessions(?int $teamId): Collection
//    {
//        return static::getCachedByTeam($teamId, function() use ($teamId) {
//            return static::query()
//                ->where('team_id', $teamId)
//                ->select(['id', 'name'])
//                ->orderBy('name')
//                ->get();
//        });
//    }
//
//    /**
//     * Holt Professions eines bestimmten Users
//     */
//    public static function getUserProfessions(?int $userId): Collection
//    {
//        return static::getCachedByUser($userId, function() use ($userId) {
//            return static::query()
//                ->where('created_by', $userId)
//                ->select(['id', 'name'])
//                ->orderBy('name')
//                ->get();
//        });
//    }
}
