<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\User\Gender;
use App\Enums\User\UserType;
use App\Models\Address\State;
use App\Models\Alem\Company;
use App\Models\Alem\Department;
use App\Models\Alem\Employee;
use App\Models\Alem\QuickCrud\Profession;
use App\Models\Alem\QuickCrud\Stage;
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
        'email',
        'password',
        'phone_1',
        'phone_2',
        'url_slug',
        'company_id',
        'team_id',
        'created_by',
        'department_id',
        'joined_at',
        'user_type',
        'model_status',
        'gender',
        'email_verified_at',

        'birthdate',
        'status',

        'profession_id',
        'stage_id',
        'supervisor_id',
//        'invitation',
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
        'password' => 'hashed',
        'joined_at' => 'date',
        'birthdate' => 'date',
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'department_id' => 'integer',
        'profession_id' => 'integer',
        'stage_id' => 'integer',
        'supervisor_id' => 'integer',
        'gender' => Gender::class,
        'user_type' => UserType::class,
        'model_status' => ModelStatus::class,
        // status wird dynamisch gesetzt, daher hier nicht als Cast
    ];

    /**
     * Definiere zusätzliche Datumfelder.
     */
    protected $dates = [
        'deleted_at',
        'joined_at',
    ];

    /**
     * Dynamischer Status Accessor - gibt den korrekten Enum-Typ zurück
     * basierend auf dem user_type
     */
    public function getStatusAttribute($value): mixed
    {
        if (!$value) return null;

        return match($this->user_type) {
            UserType::Employee => EmployeeStatus::tryFrom($value),
            // UserType::Partner => PartnerStatus::tryFrom($value),    // Zukünftig
            // UserType::Customer => CustomerStatus::tryFrom($value),  // Zukünftig
            default => null
        };
    }

    /**
     * Dynamischer Status Mutator - konvertiert den Wert zum String für die DB
     * WICHTIG: Dies ist der fehlende Teil!
     */
    public function setStatusAttribute($value): void
    {
        if ($value === null) {
            $this->attributes['status'] = null;
            return;
        }

        // Wenn es bereits ein String ist (z.B. 'probation'), direkt speichern
        if (is_string($value)) {
            $this->attributes['status'] = $value;
            return;
        }

        // Wenn es ein Enum ist, den value extrahieren
        if ($value instanceof \BackedEnum) {
            $this->attributes['status'] = $value->value;
            return;
        }

        // Fallback für andere Fälle
        $this->attributes['status'] = (string) $value;
    }

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

    /**
     * Gibt den Vorgesetzten (Supervisor) als User zurück
     */
    public function supervisorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Gibt die Berufsbezeichnung/Position des Mitarbeiters zurück.
     */
    public function profession(): BelongsTo
    {
        return $this->belongsTo(Profession::class, 'profession_id');
    }

    /**
     * Gibt die Karrierestufe des Mitarbeiters zurück.
     */
    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class, 'stage_id');
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
            if (empty($user->url_slug)) {
                $user->url_slug = Str::slug($user->name) . '-' . rand(1000, 9999);
            }
        });

        static::updating(function ($user) {
            // Den Slug nur aktualisieren, wenn sich der Name oder Nachname geändert hat
            if ($user->isDirty('name') ) {
                $user->url_slug = Str::slug($user->name) . '-' . rand(1000, 9999);
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


    /** ********************* Employee Route Model Bindung */
    /**
     * Scope für dei EmployeeProfileController
     * Er braucht die ID und den SLUG für die Route Model Bindung
     */
    public function scopeUserEmployeeFields($query)
    {
        return $query->select([
            'id',
            'url_slug'
        ]);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKey(): mixed
    {
        return $this->url_slug;
    }

    /**
     * Get the route key name for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
    /** ********************* Employee Route Model Bindung */

}
