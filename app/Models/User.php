<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Model\ModelStatus;
use App\Enums\User\UserType;
use App\Models\Address\State;
use App\Models\Alem\Department;
use App\Models\Alem\Employee\Employee;
use App\Scopes\TeamScope;
use App\Traits\BelongsToCompany;
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
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;
//    use BelongsToCompany
//    use TraitForUserModel;
    use HasAddress;
    use SoftDeletes;
    use HasRoles;
    use ModelPermanentDeletion;
    use ModelStatusManagement{
        ModelStatusManagement::restore insteadof SoftDeletes;
        // Alias für die originale SoftDeletes::restore()-Methode.
        SoftDeletes::restore as softRestore;
    }

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
        'department_id',
        'joined_at',
        'user_type',
        'model_status',
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
        if (!$this->joined_at) {
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
//        static::addGlobalScope(new TeamScope);

        static::creating(function ($user) {
//            $user->team_id = Auth::user()->currentTeam->id ?? null;
//            $user->company_id = Auth::user()->company->id ?? null;
//            $user->created_by = Auth::id();

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

    public function scopeActiveEmployees($query)
    {
        return $query->where('user_type', UserType::Employee)
            ->where('model_status', ModelStatus::ACTIVE)
            ->whereHas('employee');
    }
}
