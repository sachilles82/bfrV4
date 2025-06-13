<?php

namespace App\Models\Spatie;

use App\Enums\Role\RoleHasAccessTo;
use App\Enums\Role\RoleVisibility;
use App\Traits\Cache\AdvancedCache;
use App\Traits\Model\ManageDataFilter;
use App\Traits\Model\ManagesContextAndOwnership;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use ManageDataFilter, ManagesContextAndOwnership, AdvancedCache;

    /**
     * Cache-Konfiguration für dieses Model
     * @var int
     * @var string
     */
    protected int $cacheDuration = 43200; // 12 Stunden, Cache-Dauer in Sekunden (-1 for forever)
    protected string $cachePrefix = 'roles';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'guard_name',
        'description',
        'access',
        'visible',
        'is_manager',
        'company_id',
        'team_id',
        'created_by',
    ];

    protected $casts = [
        'access' => RoleHasAccessTo::class,
        'visible' => RoleVisibility::class,
        'is_manager' => 'boolean',
    ];

    protected $attributes = [
        'guard_name' => 'web',
    ];

    /**
     * Optional: Nur bestimmte Kontexte flushen
     * Wenn nicht definiert, werden alle geflusht (company, team, user)
     */
    protected function getAutoFlushContexts(): array
    {
        return ['company'];
    }

    /**
     * Get employee panel roles for a specific company with caching
     *
     * @param int $companyId Company ID (kann 0 sein für globale Rollen)
     * @return Collection
     */
    public static function getEmployeePanelRoles(?int $companyId): Collection
    {
        // Für globale Rollen (companyId = 0 oder null)
        if (!$companyId) {
            return static::getCached('global', 'employee_panel', function() {
                return static::query()
                    ->where('company_id', 0)
                    ->orWhereNull('company_id')
                    ->where('access', RoleHasAccessTo::EmployeePanel)
                    ->where('visible', RoleVisibility::Visible)
                    ->select(['id', 'name', 'is_manager'])
                    ->orderBy('name')
                    ->get();
            });
        }

        // Für Company-spezifische Rollen
        return static::getCachedByCompany($companyId, function() use ($companyId) {
            return static::query()
                ->where(function($query) use ($companyId) {
                    $query->where('company_id', $companyId)
                        ->orWhere('company_id', 0)
                        ->orWhereNull('company_id');
                })
                ->where('access', RoleHasAccessTo::EmployeePanel)
                ->where('visible', RoleVisibility::Visible)
                ->select(['id', 'name', 'is_manager'])
                ->orderBy('name')
                ->get();
        });
    }


    protected static function booted(): void
    {
        static::creating(function ($role) {
            $role->guard_name = 'web';
        });

    }
}
