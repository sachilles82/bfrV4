<?php

namespace App\Models\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Employee\NoticePeriod;
use App\Enums\Employee\Probation;
use App\Models\Alem\Employee\Setting\Profession;
use App\Models\Alem\Employee\Setting\Stage;
use App\Models\User;
use App\Traits\BelongsToTeam;
use App\Traits\Employee\EmployeeStatusManagement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    use HasFactory;
    use EmployeeStatusManagement;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'uuid',
        'date_hired',
        'date_fired',
        'probation',
        'probation_enum',
        'notice_period',
        'notice_period_enum',
        'social_number',
        'personal_number',
        'profession',
        'stage',
        'employment_type',
        'supervisor',
        'employee_status',
    ];

    protected $casts = [
        'date_hired' => 'date',
        'date_fired' => 'date',
        'probation' => 'date',
        'probation_enum' => Probation::class,
        'notice_period' => 'date',
        'notice_period_enum' => NoticePeriod::class,
        'employee_status' => EmployeeStatus::class,
    ];

    /**
     * Gibt den Benutzer zurück, dem der Mitarbeiter zugeordnet ist.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Gibt die Berufsbezeichnung/Position des Mitarbeiters zurück.
     */
    public function professionRelation(): BelongsTo
    {
        return $this->belongsTo(Profession::class, 'profession');
    }

    /**
     * Gibt die Karrierestufe des Mitarbeiters zurück.
     */
    public function stageRelation(): BelongsTo
    {
        return $this->belongsTo(Stage::class, 'stage');
    }

    /**
     * Überprüft, ob der Mitarbeiter im Probezeitstatus ist.
     *
     * @return bool
     */
    public function isOnProbation(): bool
    {
        return $this->employee_status === EmployeeStatus::PROBATION;
    }

    /**
     * Überprüft, ob der Mitarbeiter im Onboarding-Status ist.
     *
     * @return bool
     */
    public function isOnboarding(): bool
    {
        return $this->employee_status === EmployeeStatus::ONBOARDING;
    }

    /**
     * Überprüft, ob der Mitarbeiter voll angestellt ist.
     *
     * @return bool
     */
    public function isEmployed(): bool
    {
        return $this->employee_status === EmployeeStatus::EMPLOYED;
    }

    /**
     * Überprüft, ob der Mitarbeiter im Urlaub ist.
     *
     * @return bool
     */
    public function isOnLeave(): bool
    {
        return $this->employee_status === EmployeeStatus::ONLEAVE;
    }

    /**
     * Überprüft, ob der Mitarbeiter die Firma verlassen hat.
     *
     * @return bool
     */
    public function hasLeft(): bool
    {
        return $this->employee_status === EmployeeStatus::LEAVE;
    }
}
