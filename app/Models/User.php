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
use App\Traits\User\UserWithManagerRole;
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
    use AdvancedCache, UserWithManagerRole;

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

    // In App\Models\User - füge diese Methode hinzu:

    /**
     * Scope to select only employee-related fields for user queries.
     * This is useful for performance optimization when fetching user data
     */
    public function scopeUserEmployeeFields($query)
    {
        return $query->select([
            'id',
            'gender',
            'name',
            'last_name',
            'email',
            'phone_1',
            'model_status',
            'department_id',
            'slug'
        ]);
    }

}
