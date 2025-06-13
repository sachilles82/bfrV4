<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Model\ModelStatus;
use App\Enums\User\Gender;
use App\Models\Address\State;
use App\Models\Alem\Company;
use App\Models\Alem\Department;
use App\Models\Alem\Employee;
use App\Models\Spatie\Role;
use App\Traits\Cache\AdvancedCache;
use App\Traits\HasAddress;
use App\Traits\Model\ModelPermanentDeletion;
use App\Traits\Model\ModelStatusManagement;
use App\Traits\Users\HasManagerRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasAddress;
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasRoles;
    use HasTeams;
    use ModelPermanentDeletion;
    use ModelStatusManagement{
        ModelStatusManagement::restore insteadof SoftDeletes;
        // Alias für die originale SoftDeletes::restore()-Methode.
        SoftDeletes::restore as softRestore;
    }
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;
    use Searchable;
    use AdvancedCache;
    use HasManagerRole;

    /**
     * Cache-Konfiguration für dieses Model
     * @var int
     * @var string
     */
    protected int $cacheDuration = 43200; // 12 Stunden
    protected string $cachePrefix = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'phone_1',
        'phone_2',
        'slug',
        'company_id',
        'team_id',
        'created_by',
        'department_id',
        'joined_at',
        'user_type',
        'model_status',
        'gender',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'joined_at' => 'date',
        'model_status' => ModelStatus::class,
        'gender' => Gender::class,
        'department_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Definiere zusätzliche Datumfelder.
     */
    protected $dates = [
        'deleted_at',
        'joined_at',
    ];

    /**
     * Berechnet die Betriebszugehörigkeit in Jahren
     */
    public function getYearsOfServiceAttribute()
    {
        if (! $this->joined_at) {
            return 0;
        }

        return $this->joined_at->diffInYears(now());
    }

    /* User & States Relation */
    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }

    /* Der User kann ein Employee sein */
    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    /* Der User gehört zu einem Department (belongs to) */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKey(): mixed
    {
        // Hier kombinieren wir den Slug für URLs
        return $this->slug;
    }

    /**
     * Get the route key name for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::created(function ($user) {
            // Nach dem Erstellen/Ändern eines Benutzers den Permission-Cache zurücksetzen
            app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
        });

        static::updated(function ($user) {
            // Wenn Berechtigungsrelevante Felder geändert wurden, Cache zurücksetzen
            if ($user->isDirty('model_status') || $user->isDirty('user_type')) {
                app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
            }
        });

        static::creating(function ($user) {
            if (empty($user->slug)) {
                // Erstelle Slug aus Vor- und Nachname
                $user->slug = Str::slug($user->name . '-' . $user->last_name);
            }
        });

        static::updating(function ($user) {
            // Den Slug nur aktualisieren, wenn sich der Name oder Nachname geändert hat
            if ($user->isDirty('name') || $user->isDirty('last_name')) {
                // Erstelle Slug aus Vor- und Nachname
                $user->slug = Str::slug($user->name . '-' . $user->last_name);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Optional: Nur bestimmte Kontexte flushen
     * Wenn nicht definiert, werden alle geflusht (company, team, user)
     */
    protected function getAutoFlushContexts(): array
    {
        // User-Änderungen können Company und Team betreffen
        return ['company', 'team'];
    }

//    /**
//     * 1. Hilfsmethode: Hat User eine Manager-Rolle?
//     */
//    public function hasManagerRole(): bool
//    {
//        return $this->roles()->where('is_manager', true)->exists();
//    }
//
//    /**
//     * 2. Leert den Manager-Cache für eine Company mit AdvancedCache
//     * Nutzt die korrekten AdvancedCache-Methoden für konsistentes Cache-Management
//     */
//    public static function clearManagerCache(int $companyId): void
//    {
//        $instance = new static;
//        $config = $instance->getCacheConfig();
//
//        // Generiere den korrekten Cache-Key mit suffix
//        $persistentKey = "{$config['prefix']}:company:{$companyId}:managers";
//        $requestKey = "request_{$config['prefix']}_company_{$companyId}_managers";
//
//        // Leere persistent cache
//        Cache::forget($persistentKey);
//
//        // Leere request cache
//        if (isset(self::$requestCache[$requestKey])) {
//            unset(self::$requestCache[$requestKey]);
//        }
//
//        Log::info("Manager cache cleared for company {$companyId}", [
//            'persistent_key' => $persistentKey,
//            'request_key' => $requestKey
//        ]);
//    }
//
//    /**
//     * 4. Override assignRole - Prüft ob Manager-Rolle zugewiesen wird
//     * Cache wird NUR geleert wenn User zum Manager wird
//     */
//    public function assignRole(...$roles)
//    {
//        // Prüfe ob User bereits Manager ist
//        $wasManager = $this->hasManagerRole();
//
//        // Prüfe ob eine der neuen Rollen eine Manager-Rolle ist
//        $assigningManagerRole = collect($roles)
//            ->map(function($role) {
//                if (is_string($role)) {
//                    return Role::where('name', $role)->first();
//                } elseif (is_numeric($role)) {
//                    return Role::find($role);
//                }
//                return $role;
//            })
//            ->filter()
//            ->contains(fn($role) => $role && $role->is_manager);
//
//        // Führe die Rollenzuweisung durch
//        $result = parent::assignRole(...$roles);
//
//        // Cache leeren wenn User zum Manager wird
//        if (!$wasManager && $assigningManagerRole && $this->company_id) {
//            static::clearManagerCache($this->company_id);
//
//            Log::info("User {$this->id} became a manager via assignRole", [
//                'company_id' => $this->company_id,
//                'roles' => collect($roles)->pluck('name', 'id')->toArray()
//            ]);
//        }
//
//        return $result;
//    }
//
//    /**
//     * 5. Override removeRole - Prüft ob Manager-Rolle entfernt wird
//     * Cache wird NUR geleert wenn User Manager-Status verliert
//     */
//    public function removeRole($role)
//    {
//        // Konvertiere Rolle zu Model-Objekt
//        $roleModel = is_string($role)
//            ? Role::where('name', $role)->first()
//            : (is_numeric($role) ? Role::find($role) : $role);
//
//        // Prüfe ob es eine Manager-Rolle ist
//        $isRemovingManagerRole = $roleModel && $roleModel->is_manager;
//
//        // Prüfe ob User noch andere Manager-Rollen hat
//        $otherManagerRoles = $this->roles()
//            ->where('is_manager', true)
//            ->where('id', '!=', $roleModel->id ?? 0)
//            ->exists();
//
//        // Führe die Rollenentfernung durch
//        $result = parent::removeRole($role);
//
//        // Cache leeren wenn User keinen Manager-Status mehr hat
//        if ($isRemovingManagerRole && !$otherManagerRoles && $this->company_id) {
//            static::clearManagerCache($this->company_id);
//
//            Log::info("User {$this->id} is no longer a manager", [
//                'company_id' => $this->company_id,
//                'removed_role' => $roleModel ? $roleModel->name : 'unknown'
//            ]);
//        }
//
//        return $result;
//    }
//
//    /**
//     * Get managers for a specific company with caching
//     * Nutzt AdvancedCache mit suffix für spezifischen Cache-Key
//     */
//    public static function getCompanyManagers(int $companyId): Collection
//    {
//        \Debugbar::startMeasure('manager-cache', 'Loading Company Managers');
//
//        $cacheKey = "users:company:{$companyId}:managers";
//
//        // Check Cache Status
//        if (\Cache::has($cacheKey)) {
//            \Debugbar::info("CACHE HIT - Managers for company {$companyId}");
//        } else {
//            \Debugbar::warning("CACHE MISS - Loading managers from DB for company {$companyId}");
//        }
//
//        $result = static::getCachedByCompany($companyId, function() use ($companyId) {
//            \Log::debug("Loading managers from database for company {$companyId}");
//            \Debugbar::addMessage("DB Query for managers", 'queries');
//
//            return self::select([
//                'users.id',
//                'users.name',
//                'users.last_name',
//                'users.profile_photo_path'
//            ])
//                ->join('model_has_roles', function ($join) {
//                    $join->on('users.id', '=', 'model_has_roles.model_id')
//                        ->where('model_has_roles.model_type', User::class);
//                })
//                ->join('roles', function ($join) {
//                    $join->on('model_has_roles.role_id', '=', 'roles.id')
//                        ->where('roles.is_manager', true);
//                })
//                ->where('users.company_id', $companyId)
//                ->whereNull('users.deleted_at')
//                ->orderBy('users.name')
//                ->distinct()
//                ->get();
//        }, ['suffix' => 'managers']);
//
//        \Debugbar::stopMeasure('manager-cache');
//        \Debugbar::info("Found {$result->count()} managers");
//
//        return $result;
//    }
}
