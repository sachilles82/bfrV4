<?php

namespace App\Models\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Models\User;
use App\Traits\BelongsToTeam;
use App\Traits\Employee\EmployeeStatusManagement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    use HasFactory;
    use BelongsToTeam;
    use EmployeeStatusManagement;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'date_hired',
        'date_fired',
        'probation',
        'social_number',
        'personal_number',
        'profession',
        'company_id',
        'team_id',
        'created_by',
        'employee_status',
    ];

    protected $casts = [
        'date_hired' => 'date',
        'date_fired' => 'date',
        'probation' => 'date',
        'employee_status' => EmployeeStatus::class,
    ];

    /** Employee gehört zu einem User */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
