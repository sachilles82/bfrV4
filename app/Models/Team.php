<?php

namespace App\Models;

use App\Models\Alem\Company;
use App\Traits\Cache\WithRedisCache;
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
     * Get teams for a specific company with caching
     */
    public static function getCompanyTeams(int $companyId)
    {
        return self::cacheCompanyResult($companyId, function() use ($companyId) {
            return self::where('company_id', $companyId)
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();
        });
    }

    protected static function booted(): void
    {
        static::creating(function (Team $team) {
            // Wenn keine company_id explizit gesetzt wurde und ein Benutzer eingeloggt ist
            if (!$team->company_id && auth()->check()) {
                // Setze die company_id des authentifizierten Benutzers
                $team->company_id = auth()->user()->company_id;
            }
        });
    }
}
