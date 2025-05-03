<?php

namespace App\Models\Spatie;

use App\Enums\Role\RoleHasAccessTo;
use App\Enums\Role\RoleVisibility;
use App\Models\User;
use App\Traits\Cache\WithRedisCache;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use WithRedisCache;

    /**
     * The key used for caching this model
     *
     * @var string
     */
    protected $cacheKey = 'roles_cache';


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
     * Get employee panel roles for a specific company with caching
     */
    // In app/Models/Spatie/Role.php
    public static function getEmployeePanelRoles(int $companyId) // companyId kann hier 0 sein für globale
    {
        // Der dritte Parameter 'role' signalisiert dem Trait die spezielle Rollenlogik
        return self::cacheCompanyResult($companyId, function() use ($companyId) {
            // Log::info(" -> Fetching EmployeePanel roles for company {$companyId} from DB..."); // Optional
            return self::where(function ($query) use ($companyId) {
                // Wenn companyId > 0, suche firmenspezifische ODER globale
                if ($companyId > 0) {
                    $query->where('created_by', 1)
                        ->orWhere('company_id', $companyId);
                } else {
                    // Wenn companyId <= 0, suche nur globale
                    $query->where('created_by', 1);
                }
            })
                ->where('access', RoleHasAccessTo::EmployeePanel->value)
                ->where('visible', RoleVisibility::Visible->value)
                ->select(['id', 'name', 'is_manager'])
                ->get();
        }, 'role'); // <--- Wichtiger dritter Parameter
    }


    protected static function booted(): void
    {
        static::creating(function ($role) {
            $role->guard_name = 'web';

            if (! $role->created_by) {
                $role->created_by = auth()->id();
            }

            $user = auth()->user();

            if ($user) {
                if (! $role->company_id) {
                    $role->company_id = $user->company_id;
                }
                if (! $role->team_id) {
                    $role->team_id = $user->currentTeam?->id;
                }
            }
        });

        // Event-Hooks für Cache-Invalidierung
        static::saved(function($role) {
            self::flushCompanyCache($role->company_id);
            self::flushGlobalRoleCache();
        });

        static::deleted(function($role) {
            self::flushCompanyCache($role->company_id);
            self::flushGlobalRoleCache();
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
