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

    /**
     * Hauptmethode: Holt alle Stages für eine Company mit dreistufigem Cache
     *
     * Diese Methode nutzt die generische getCachedCompanyData() aus dem WithRedisCache Trait
     * und delegiert die eigentliche Query-Logik an getQueryForCompany()
     *
     * @param int|null $companyId Company ID
     * @return Collection
     */
    public static function getCompanyStages(?int $companyId): Collection
    {
        return static::getCachedCompanyData($companyId, 'getQueryForCompany');
    }

    /**
     * Spezifische Query-Logik für Stages einer Company
     *
     * Diese Methode wird von getCachedCompanyData() aufgerufen wenn der Cache leer ist
     * Kann einfach angepasst werden ohne die Cache-Logik zu beeinflussen
     *
     * @param int $companyId Company ID
     * @return Collection
     */
    public static function getQueryForCompany(int $companyId): Collection
    {
        return static::query()
            ->where('company_id', $companyId)
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();
    }

}
