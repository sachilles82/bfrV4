<?php

namespace App\Models\Alem\QuickCrud;

use App\Models\Alem\Employee;
use App\Traits\Cache\WithRedisCache;
use App\Traits\Model\DataFilter;
use App\Traits\Model\ManagesContextAndOwnership;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stage extends Model
{
    use DataFilter, ManagesContextAndOwnership, WithRedisCache;
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
     * Holt alle Stages einer Company mit Caching
     * Wir in den Dropdowns verwendet
     *
     * @param int $companyId
     * @return Collection
     */
    public static function getCompanyStages(int $companyId): Collection
    {
        return self::cacheCompanyResult($companyId, function() use ($companyId) {
            return self::where('company_id', $companyId)
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();
        });
    }
}
