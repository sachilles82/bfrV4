<?php

namespace App\Models\Alem\QuickCrud;

use App\Models\Alem\Employee;
use App\Traits\BelongsToCompany;
use App\Traits\Cache\WithRedisCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profession extends Model
{
    use BelongsToCompany, HasFactory, WithRedisCache;

    /**
     * The key used for caching this model
     *
     * @var string
     */
    protected $cacheKey = 'professions_cache';

    /**
     * Cache duration in seconds (-1 for forever)
     *
     * @var int
     */
    protected $cacheDuration = 43200; // 12 hours

    protected $fillable = [
        'name',
        'company_id',
        'team_id',
        'created_by',
    ];

    /**
     * Gibt die Mitarbeiter die zu dieser Berufsbezeichnung/ Position zugeorndet sind.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'profession_id');
    }

    /**
     * Get professions for a specific company with caching
     */
    public static function getCompanyProfessions(int $companyId)
    {
        return self::cacheCompanyResult($companyId, function() use ($companyId) {
            return self::where('company_id', $companyId)
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();
        });
    }
}
