<?php

namespace App\Models\Alem;

use App\Enums\Model\ModelStatus;
use App\Models\User;
use App\Traits\BelongsToTeam;
use App\Traits\Cache\WithRedisCache;
use App\Traits\Model\ModelPermanentDeletion;
use App\Traits\Model\ModelStatusManagement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory;
    use BelongsToTeam;
    use ModelPermanentDeletion;
    use ModelStatusManagement {
        ModelStatusManagement::restore insteadof SoftDeletes;
        // Alias für die originale SoftDeletes::restore()-Methode.
        SoftDeletes::restore as softRestore;
    }
    use SoftDeletes;
    use WithRedisCache;

    /**
     * The key used for caching this model
     *
     * @var string
     */
    protected $cacheKey = 'departments_cache';

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
        'description',
        'company_id',
        'team_id',
        'created_by',
        'model_status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'model_status' => ModelStatus::class,
        ];
    }

    /**
     * Ein Department hat viele User (one-to-many)
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class)
            ->select(['id', 'name', 'last_name', 'department_id', 'email', 'profile_photo_path']);
    }

    public function scopeActive($query)
    {
        return $query->where('model_status', ModelStatus::ACTIVE);
    }

    /**
     * Get departments for a specific company with caching
     */
    public static function getDepartmentsForTeam(int $teamId)
    {
        return self::cacheTeamResult($teamId, function () use ($teamId) {
            return self::where('team_id', $teamId)
                ->where('model_status', ModelStatus::ACTIVE->value)
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();
        });
    }
}
