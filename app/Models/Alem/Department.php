<?php

namespace App\Models\Alem;

use App\Enums\Model\ModelStatus;
use App\Models\User;
use App\Traits\Cache\AdvancedCache;
use App\Traits\Model\ManageDataFilter;
use App\Traits\Model\ManagesContextAndOwnership;
use App\Traits\Model\ModelPermanentDeletion;
use App\Traits\Model\ModelStatusManagement;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory;
    use ModelPermanentDeletion;
    use ModelStatusManagement {
        ModelStatusManagement::restore insteadof SoftDeletes;
        // Alias für die originale SoftDeletes::restore()-Methode.
        SoftDeletes::restore as softRestore;
    }
    use SoftDeletes;
    use ManageDataFilter, ManagesContextAndOwnership, AdvancedCache;

    /**
     * Cache-Konfiguration für dieses Model
     * @var int
     * @var string
     */
    protected int $cacheDuration = 43200; // 12 Stunden, Cache-Dauer in Sekunden (-1 for forever)
    protected string $cachePrefix = 'departments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'model_status',
        'company_id',
        'team_id',
        'created_by',
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
     * Optional: Nur bestimmte Kontexte flushen
     * Wenn nicht definiert, werden alle geflusht (company, team, user)
     */
    protected function getAutoFlushContexts(): array
    {
        // Beispiele:
//        return ['company', 'team', 'user']; // Flusht alle drei (default)
        return ['team'];
    }

    /**
     * Holt Departments für das Team des aktuellen Benutzers
     * @param int|null $teamId Team ID
     * @return Collection
     */
    public static function getTeamDepartments(?int $teamId): Collection
    {
        return static::getCachedByTeam($teamId, function () use ($teamId) {
            return static::query()
                ->where('team_id', $teamId)
                ->where('model_status', ModelStatus::ACTIVE->value)
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();
        });
    }
}
