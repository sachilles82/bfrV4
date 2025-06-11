<?php

namespace App\Models\Alem\QuickCrud;

use App\Models\Alem\Employee;
use App\Traits\Cache\AdvancedCache;
use App\Traits\Model\ManageDataFilter;
use App\Traits\Model\ManagesContextAndOwnership;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stage extends Model
{
    use HasFactory;
    use ManageDataFilter, ManagesContextAndOwnership, AdvancedCache;

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
     * Optional: Nur bestimmte Kontexte flushen
     * Wenn nicht definiert, werden alle geflusht (company, team, user)
     */
    protected function getAutoFlushContexts(): array
    {
        return ['company'];
    }


    /**
     * @param int|null $companyId Company ID
     * @return Collection
     */
    public static function getCompanyStages(?int $companyId): Collection
    {
        return static::getCachedByCompany($companyId, function () use ($companyId) {
            return static::query()
                ->where('company_id', $companyId)
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();
        });
    }
}
