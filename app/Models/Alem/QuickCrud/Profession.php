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

class Profession extends Model
{
    use HasFactory;
    use ManageDataFilter, ManagesContextAndOwnership, AdvancedCache;

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
     * Optional: Nur bestimmte Kontexte flushen
     * Wenn nicht definiert, werden alle geflusht (company, team, user)
     */
    protected function getAutoFlushContexts(): array
    {
        // Beispiele:
//        return ['company', 'team', 'user']; // Flusht alle drei (default)
        return ['company'];
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
    public static function getCompanyProfessions(?int $companyId): Collection
    {
        return static::getCachedByCompany($companyId, function () use ($companyId) {
            return static::query()
                ->where('company_id', $companyId)
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Manuell den Cache leeren. zb. Beim Importieren von Daten
     *
     * Manuell Company Cache leeren
     * Profession::flushCompanyCache($this->companyId);
     *
     * Manuell Team Cache leeren
     * Profession::flushTeamCache($this->teamId);
     *
     * Manuell User Cache leeren
     * Profession::flushUserCache($this->userId);
     */

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
