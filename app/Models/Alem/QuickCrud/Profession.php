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
     * Hauptmethode: Holt alle Professionen für eine Company mit dreistufigem Cache
     *
     * Diese Methode nutzt die generische getCachedCompanyData() aus dem WithRedisCache Trait
     * und delegiert die eigentliche Query-Logik an getQueryForCompany()
     *
     * @param int|null $companyId Company ID
     * @return Collection
     */
    public static function getCompanyProfessions(?int $companyId): Collection
    {
        return static::getCachedCompanyData($companyId, 'getQueryForCompany');
    }

    /**
     * Spezifische Query-Logik für Professionen einer Company
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
            ->select(['id', 'name']) // Nur benötigte Spalten selektieren
            ->orderBy('name')
            ->get();
    }
}
