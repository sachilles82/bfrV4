<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Model\ModelStatus;
use App\Enums\User\UserType;
use App\Models\Address\State;
use App\Models\Alem\Company;
use App\Models\Alem\Department;
use App\Models\Alem\Employee\Employee;
use App\Scopes\TeamScope;
use App\Traits\BelongsToCompany;
use App\Traits\Cache\WithRedisCache;
use App\Traits\HasAddress;
use App\Traits\Model\ModelPermanentDeletion;
use App\Traits\Model\ModelStatusManagement;
use App\Traits\TraitForUserModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
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
    //    use BelongsToCompany
    //    use TraitForUserModel;
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
    use WithRedisCache;

    /**
     * The key used for caching this model
     *
     * @var string
     */
    protected $cacheKey = 'users_cache';

    /**
     * Cache duration in seconds
     *
     * @var int
     */
    protected $cacheDuration = 3600; // 1 hour


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

        //        static::addGlobalScope(new TeamScope);

        static::creating(function ($user) {
            //            $user->team_id = Auth::user()->currentTeam->id ?? null;
            //            $user->company_id = Auth::user()->company->id ?? null;
            //            $user->created_by = Auth::id();

            if (empty($user->slug)) {
                // Erstelle Slug aus Vor- und Nachname
                $user->slug = Str::slug($user->name.'-'.$user->last_name);
            }
        });

        static::updating(function ($user) {
            // Den Slug nur aktualisieren, wenn sich der Name oder Nachname geändert hat
            if ($user->isDirty('name') || $user->isDirty('last_name')) {
                // Erstelle Slug aus Vor- und Nachname
                $user->slug = Str::slug($user->name.'-'.$user->last_name);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    //    public function scopeActiveEmployees($query)
    //    {
    //        return $query->where('user_type', UserType::Employee)
    //            ->where('model_status', ModelStatus::ACTIVE);
    //    }
//
//    /**
//     * Get company-specific cache key
//     *
//     * @return string|null
//     */
//    public function getCompanyCacheKey(): ?string
//    {
//        if (property_exists($this, 'company_id') && $this->company_id) {
//            return "company_{$this->company_id}_user_cache";
//        }
//
//        return null;
//    }
    /**
     * Get managers for a specific company with caching
     */
    public static function getCompanyManagers(int $companyId)
    {
        return self::cacheCompanyResult($companyId, function() use ($companyId) {
            return self::select([
                'users.id',
                'users.name',
                'users.last_name',
                'users.profile_photo_path'
            ])
                ->join('model_has_roles', function ($join) {
                    $join->on('users.id', '=', 'model_has_roles.model_id')
                        ->where('model_has_roles.model_type', User::class);
                })
                ->join('roles', function ($join) {
                    $join->on('model_has_roles.role_id', '=', 'roles.id')
                        ->where('roles.is_manager', true);
                })
                ->where('users.company_id', $companyId)
                ->whereNull('users.deleted_at')
                ->orderBy('users.name')
                ->distinct()
                ->get();
        }, 'user');
    }


    /**
     * Definiert, welche Daten des Models an den Suchindex gesendet werden sollen.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id'          => $this->getKey(), // ID ist oft nützlich
            'name'        => $this->name,
            'last_name'   => $this->last_name,
            'email'       => $this->email,
            'phone_1'     => $this->phone_1,

            'joined_at_timestamp' => $this->joined_at?->timestamp,
            'created_at_timestamp' => $this->created_at?->timestamp,

            // Status direkt vom User-Model
            'model_status' => $this->model_status,
            'user_type'    => $this->user_type,

            // Füge weitere relevante Felder hinzu...
        ];
    }
}
