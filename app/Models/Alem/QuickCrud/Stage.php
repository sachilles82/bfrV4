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

class Stage extends Model
{
    use HasFactory;
    use ManageDataFilter, ManagesContextAndOwnership, WithAdvancedCache;

    /**
     * Cache-Konfiguration für dieses Model
     * @var int
     * @var string
     */
    protected int $cacheDuration = 43200; // 12 Stunden, Cache-Dauer in Sekunden (-1 for forever)
    protected string $cachePrefix = 'stages';

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


    /**
     * Hauptmethode: Holt alle Stages für eine Company mit dreistufigem Cache
     *
     * Diese Methode nutzt die generische getCachedCompanyData() aus dem WithRedisCache Trait
     * und delegiert die eigentliche Query-Logik an getQueryForCompany()
     *
     * @param int|null $companyId Company ID
     * @return \Illuminate\Support\Collection
     */

    public static function getCompanyStages(?int $companyId): Collection
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

        // Auch Request-Cache leeren
        static::flushRequestCache();
    }

}
