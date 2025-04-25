<?php

namespace App\Models\Alem\Employee;

use App\Enums\Employee\CivilStatus;
use App\Enums\Employee\EmployeeStatus;
use App\Enums\Employee\NoticePeriod;
use App\Enums\Employee\Probation;
use App\Enums\Employee\Religion;
use App\Enums\Employee\Residence;
use App\Models\Alem\Employee\Setting\Profession;
use App\Models\Alem\Employee\Setting\Stage;
use App\Models\User;
use App\Traits\Employee\EmployeeStatusManagement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    use EmployeeStatusManagement;
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
        'profession_id',
        'stage_id',
        'employment_type',
        'supervisor_id',
        // joined_at wird im User Model gespeichert
        'probation_enum',
        'notice_at',
        'notice_enum',
        'leave_at',
        'employee_status',

        // Employment Data
        'ahv_number',
        'nationality',
        'hometown',
        'birthdate',
        'religion',
        'civil_status',
        'residence_permit',
    ];

    /**
     * Appends für häufig benötigte berechnete Attribute
     */
    protected $appends = ['full_status'];

    /**
     * Type-Casting für Attribute
     */
    protected $casts = [
        'leave_at' => 'date',
        'probation_at' => 'date',
        'probation_enum' => Probation::class,
        'notice_at' => 'date',
        'notice_enum' => NoticePeriod::class,
        'employee_status' => EmployeeStatus::class,
        'birthdate' => 'date',
        'religion' => Religion::class,
        'civil_status' => CivilStatus::class,
        'residence_permit' => Residence::class,
    ];

    /**
     * Boot-Methode mit Global Scopes
     */
    protected static function booted()
    {
        // Global Scope für aktive Mitarbeiter (optional, nur bei Bedarf verwenden)
        // static::addGlobalScope('notLeft', function ($builder) {
        //     $builder->where('employee_status', '!=', EmployeeStatus::LEAVE->value);
        // });
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
        return static::with(['user', 'profession', 'stage', 'supervisorUser']);
    }

    /**
     * Scope für aktive Mitarbeiter
     */
    public function scopeActive($query)
    {
        return $query->whereIn('employee_status', [
            EmployeeStatus::EMPLOYED->value,
            EmployeeStatus::PROBATION->value,
            EmployeeStatus::ONBOARDING->value,
        ]);
    }

    /**
     * Scope für Mitarbeiter mit bestimmtem Status
     */
    public function scopeWithStatus($query, EmployeeStatus $status)
    {
        return $query->where('employee_status', $status->value);
    }

    /**
     * Liefert den formatierten Status
     */
    public function getFullStatusAttribute()
    {
        if (! $this->employee_status) {
            return '';
        }

        return "{$this->employee_status->value}: {$this->employee_status->label()}";
    }

    /**
     * Gibt den Benutzer zurück, dem der Mitarbeiter zugeordnet ist.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    /**
     * Überprüft, ob der Mitarbeiter im Probezeitstatus ist.
     */
    public function isOnProbation(): bool
    {
        return $this->employee_status === EmployeeStatus::PROBATION;
    }

    /**
     * Überprüft, ob der Mitarbeiter im Onboarding-Status ist.
     */
    public function isOnboarding(): bool
    {
        return $this->employee_status === EmployeeStatus::ONBOARDING;
    }

    /**
     * Überprüft, ob der Mitarbeiter voll angestellt ist.
     */
    public function isEmployed(): bool
    {
        return $this->employee_status === EmployeeStatus::EMPLOYED;
    }

    /**
     * Überprüft, ob der Mitarbeiter im Urlaub ist.
     */
    public function isOnLeave(): bool
    {
        return $this->employee_status === EmployeeStatus::ONLEAVE;
    }

    /**
     * Überprüft, ob der Mitarbeiter die Firma verlassen hat.
     */
    public function hasLeft(): bool
    {
        return $this->employee_status === EmployeeStatus::LEAVE;
    }
}
