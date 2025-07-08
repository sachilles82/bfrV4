<?php

namespace App\Models\Alem;

use App\Enums\Employee\CivilStatus;
use App\Enums\Employee\NoticePeriod;
use App\Enums\Employee\Probation;
use App\Enums\Employee\Religion;
use App\Enums\Employee\Residence;
use App\Models\Address\Country;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        // Personal Data
        'personal_number',

        'prob_period',
        'probation_at',
        'notice_at',
        'notice_period',
        'leave_at',

        // Employment Data
        'ahv_number',
        'residence_permit',
        // Brithdate ist im User Model
        'country_id',
        'hometown',
        'religion',
        // Employee Status ist im User Model


        // Zivilstand des Employees
        'civil_status',
        'name_partner',
        'single_parent',
        'birthdate_partner',
        'ahv_partner',
        'marriage_at',
    ];

    /**
     * Type-Casting für Attribute
     */
    protected $casts = [
        'leave_at' => 'date',
        'marriage_at' => 'date',
        'probation_at' => 'date',
        'prob_period' => Probation::class,
        'notice_at' => 'date',
        'notice_period' => NoticePeriod::class,
        'religion' => Religion::class,
        'civil_status' => CivilStatus::class,
        'residence_permit' => Residence::class,
    ];

    /**
     * Boot-Method mit Global Scopes
     */
    protected static function booted()
    {
        //
    }

    /**
     * Berechnet die Betriebszugehörigkeit in Jahren - nutzt User joined_at
     */
    public function getYearsOfServiceAttribute()
    {
        if ($this->user && $this->user->joined_at) {
            return $this->user->joined_at->diffInYears(now());
        }

        return 0;
    }

    /**
     * Factory-Methode mit Standardrelationen
     */
    public static function withDefaultRelations()
    {
        return static::with(['user']);
    }



    /**
     * Gibt den Benutzer zurück, dem der Mitarbeiter zugeordnet ist.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Country Relation
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

}
