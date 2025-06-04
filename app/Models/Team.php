<?php

namespace App\Models;

use App\Models\Alem\Company;
use App\Traits\Cache\WithRedisCache;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Team as JetstreamTeam;

class Team extends JetstreamTeam
{
    use HasFactory;
    use WithRedisCache;

    /**
     * The key used for caching this model
     *
     * @var string
     */
    protected $cacheKey = 'teams_cache';

    /**
     * Cache duration in seconds (-1 for forever)
     *
     * @var int
     */
    protected $cacheDuration = 86400; // 24 hours

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'user_id',
        'company_id',
        'personal_team',
    ];

    /**
     * The event map for the model.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'personal_team' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);

    }

    /**
     * Hauptmethode: Holt alle Teams für eine Company mit dreistufigem Cache
     *
     * Nutzt die neue generische getCachedCompanyData() Methode
     *
     * @param int $companyId Company ID
     * @return Collection
     */
    public static function getCompanyTeams(int $companyId)
    {
        return static::getCachedCompanyData($companyId, 'getQueryForCompany');
    }

    /**
     * Spezifische Query-Logik für Teams einer Company
     *
     * @param int $companyId Company ID
     * @return Collection
     */
    public static function getQueryForCompany(int $companyId)
    {
        return static::query()
            ->where('company_id', $companyId)
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();
    }

    protected static function booted(): void
    {
        static::creating(function (Team $team) {
            /** Automatische company_id Zuweisung */
            if (!$team->company_id && auth()->check()) {
                $team->company_id = auth()->user()->company_id;
            }
        });

        /**
         * Cache-Events werden jetzt automatisch vom WithRedisCache Trait gehandhabt
         * Keine manuellen Event-Hooks mehr nötig:
         * - static::saved() -> automatisch durch clearRelatedCaches()
         * - static::deleted() -> automatisch durch clearRelatedCaches()
         */
    }
}
