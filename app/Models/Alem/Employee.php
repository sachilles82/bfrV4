<?php

namespace App\Models\Alem;

use App\Enums\Employee\CivilStatus;
use App\Enums\Employee\NoticePeriod;
use App\Enums\Employee\Probation;
use App\Enums\Employee\Religion;
use App\Enums\Employee\Residence;
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
        'employment_type',
        // joined_at wird im User Model gespeichert
        'probation_enum',
        'notice_at',
        'notice_enum',
        'leave_at',

        // Employment Data
        'ahv_number',
        'nationality',
        'hometown',
        'religion',
        'civil_status',
        'residence_permit',
    ];

    /**
     * Type-Casting für Attribute
     */
    protected $casts = [
        'leave_at' => 'date',
        'probation_at' => 'date',
        'probation_enum' => Probation::class,
        'notice_at' => 'date',
        'notice_enum' => NoticePeriod::class,
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

}
