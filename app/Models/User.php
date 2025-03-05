<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\User\EmployeeAccountStatus;
use App\Models\Address\State;
use App\Models\Alem\Employee\Employee;
use App\Traits\HasAddress;
use App\Traits\TraitForUserModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Prunable;
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
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use TraitForUserModel;
    use HasAddress;
    use SoftDeletes;
    use Prunable;
    use HasRoles;

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
        'company_id',
        'user_type',
        'gender',
        'created_by',
        'account_status',
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
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'account_status' => EmployeeAccountStatus::class,
        ];
    }

    /**
     * Definiere zusätzliche Datumfelder.
     */
    protected $dates = [
        'deleted_at',
    ];

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

    /**
     * Verwende den "username" als Schlüssel für das Route Model Binding.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::creating(function ($user) {
            if (empty($user->slug)) {
                $user->slug = Str::slug($user->name);
            }
        });

        static::updating(function ($user) {
            // Optional: Den slug auch aktualisieren, wenn sich der Name ändert.
            $user->slug = Str::slug($user->name);
        });
    }

    /**
     * Legt fest, welche Users aus dem Bin (Trash) entfernt werden sollen.
     */
    public function prunable()
    {
        return static::onlyTrashed()
            ->where('deleted_at', '<=', now()->subDays(7));
    }

    /**
     * Scope für aktive (nicht archivierte und nicht gelöschte) Users.
     */
    public function scopeActive($query)
    {
        return $query->where('account_status', EmployeeAccountStatus::ACTIVE->value)
            ->whereNull('deleted_at');
    }

    /**
     * Scope für nicht aktivierte Users.
     */
    public function scopeNotActivated($query)
    {
        return $query->where('account_status', EmployeeAccountStatus::NOT_ACTIVATED->value)
            ->whereNull('deleted_at');
    }

    /**
     * Scope für archivierte Users (aber noch nicht im Bin).
     */
    public function scopeArchived($query)
    {
        return $query->where('account_status', EmployeeAccountStatus::ARCHIVED->value)
            ->whereNull('deleted_at');
    }

    /**
     * Scope für alle nicht-gelöschten Benutzer (active, not_activated, und archived)
     */
    public function scopeNotTrashed($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Override the SoftDeletes trait's delete method to update account_status
     */
    public function delete()
    {
        $this->update(['account_status' => EmployeeAccountStatus::TRASHED->value]);
        return parent::delete();
    }

    /**
     * Override the SoftDeletes trait's restore method to restore previous status
     */
    public function restore()
    {
        $result = parent::restore();
        // If account_status is trashed, change it back to active
        if ($this->account_status === EmployeeAccountStatus::TRASHED) {
            $this->update(['account_status' => EmployeeAccountStatus::ACTIVE->value]);
        }
        return $result;
    }
}
