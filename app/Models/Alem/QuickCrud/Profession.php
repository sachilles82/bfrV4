<?php

namespace App\Models\Alem\QuickCrud;

use App\Models\Alem\Employee;
use App\Traits\Cache\WithAdvancedCache;
use App\Traits\Model\ManageDataFilter;
use App\Traits\Model\ManagesContextAndOwnership;
use Illuminate\Support\Collection;
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
     * Boot-Methode um sicherzustellen, dass Events gefeuert werden
     */
    protected static function boot()
    {
        parent::boot();

        // Zusätzliche Sicherheit: Manuell Cache leeren bei Events
        static::created(function ($model) {
            static::flushCompanyCache($model->company_id);
        });

        static::updated(function ($model) {
            static::flushCompanyCache($model->company_id);
        });

        static::deleted(function ($model) {
            static::flushCompanyCache($model->company_id);
        });
    }

    public static function getCompanyProfessions(?int $companyId): Collection
    {
        \Log::info('🔵 [Profession] getCompanyProfessions aufgerufen', [
            'company_id' => $companyId,
            'caller' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'] ?? 'unknown',
            'timestamp' => now()->toTimeString()
        ]);

        return static::getCachedByCompany($companyId, function() use ($companyId) {
            \Log::warning('⚠️ [Profession] DATENBANK-ABFRAGE wird ausgeführt!', [
                'company_id' => $companyId,
                'info' => 'Cache war leer - Lade aus DB',
                'timestamp' => now()->toTimeString()
            ]);

            $result = static::query()
                ->where('company_id', $companyId)
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();

            \Log::info('✅ [Profession] Datenbank-Abfrage abgeschlossen', [
                'company_id' => $companyId,
                'anzahl_records' => $result->count(),
                'timestamp' => now()->toTimeString()
            ]);

            return $result;
        });
    }

    /**
     * Leert den Company-Cache manuell
     */
    public static function flushCompanyCache(?int $companyId): void
    {
        if (!$companyId) return;

        $instance = new static;
        $instance->flushCacheContext('company', $companyId);

        // Auch Request-Cache leeren
        static::flushRequestCache();
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
