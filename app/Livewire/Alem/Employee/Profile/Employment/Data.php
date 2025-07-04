<?php

namespace App\Livewire\Alem\Employee\Profile\Employment;

use App\Livewire\Alem\Employee\Helper\WithDropDownRelations;
use App\Livewire\Alem\Employee\Profile\Employment\Helper\ValidateEmploymentData;
use App\Livewire\Alem\Employee\Profile\Personal\Helper\HandleCatchError;
use App\Models\Alem\Employee;
use App\Models\User;
use App\Traits\Employee\EmployeeStatusOptions;
use App\Traits\Employee\NoticePeriodOptions;
use App\Traits\Employee\ProbationOptions;
use App\Traits\User\AuthUserTeamCompanyId;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Lazy]
class Data extends Component
{
    use AuthorizesRequests;
    use AuthUserTeamCompanyId;
    use ValidateEmploymentData, HandleCatchError;
    use WithDropDownRelations;
    use EmployeeStatusOptions, ProbationOptions, NoticePeriodOptions;

    #[Locked]
    public int $userId;

    public ?User $user = null;
    public ?Employee $employee = null;

    /** Form fields */
    public ?string $joined_at = null;// was soll das sein? Angestellungsdatum?
    public ?string $personal_number = null;
    public ?string $employment_type = null;
    public ?int $profession = null;
    public ?int $stage = null;
    public ?string $probation_enum = null;
    public ?string $probation_at = null;
    public ?string $notice_at = null;
    public ?string $notice_enum = null;
    public ?string $leave_at = null;

    /** Original Daten für Vergleiche */
    public array $originalData = [];

    public function mount(int $userId, int $authUserId, int $currentTeamId, int $companyId): void
    {
        $this->userId = $userId;
        $this->authUserId = $authUserId;
        $this->currentTeamId = $currentTeamId;
        $this->companyId = $companyId;

//        // Lade User mit joined_at
        $this->user = User::select([
            'id',
            'joined_at',
        ])
            ->findOrFail($this->userId);

//        $this->employee = Employee::with(['user:id,joined_at'])
//            ->where('user_id', $this->userId)
//            ->select([
//                'id',
//                'user_id',
//                'ahv_number',
//                'nationality',
//                'hometown',
//                'religion',
//                'residence_permit'
//            ])
//            ->first();

        $this->employee = Employee::with(['user:id'])
            ->where('user_id', $this->userId)
            ->select([
                'id',
                'user_id',
                'ahv_number',
                'nationality',
                'hometown',
                'religion',
                'residence_permit'
            ])
            ->first();

//        // Lade Employee Model mit allen benötigten Feldern
//        $this->employee = Employee::where('user_id', $this->userId)
//            ->select([
//                'id',
//                'user_id',
//                'personal_number',
//                'employment_type',
//                'probation_enum',
//                'probation_at',
//                'notice_at',
//                'notice_enum',
//                'leave_at'
//            ])
//            ->first();

        $this->loadPersonalData();

        // Lade Dropdown-Daten
        $this->loadRelationsData([
            'professions', 'stages', 'supervisors'
        ]);
    }

    /**
     * Befülle die Form mit Personal Daten
     * Speichere Original-Daten aus der Datenbank für den Vergleich
     */
    private function loadPersonalData(): void
    {
        // Original-Daten speichern
        $this->originalData = [
            'joined_at' => $this->user?->joined_at?->format('Y-m-d'),
            'personal_number' => $this->employee?->personal_number,
            'employment_type' => $this->employee?->employment_type,
            'probation_enum' => $this->employee?->probation_enum?->value,
            'probation_at' => $this->employee?->probation_at?->format('Y-m-d'),
            'notice_at' => $this->employee?->notice_at?->format('Y-m-d'),
            'notice_enum' => $this->employee?->notice_enum?->value,
            'leave_at' => $this->employee?->leave_at?->format('Y-m-d'),
        ];

        // Setze Form-Felder
        $this->joined_at =  $this->user?->joined_at?->format('Y-m-d') ?? '';
        $this->personal_number = $this->originalData['personal_number'] ?? '';
        $this->employment_type = $this->originalData['employment_type'] ?? '';
        $this->probation_enum = $this->originalData['probation_enum'];
        $this->probation_at = $this->originalData['probation_at'] ?? '';
        $this->notice_at = $this->originalData['notice_at'] ?? '';
        $this->notice_enum = $this->originalData['notice_enum'];
        $this->leave_at = $this->originalData['leave_at'] ?? '';
    }

    /**
     * Aktualisiert die Personal Daten in der Datenbank.
     * Validiert nur die geänderten Felder für bessere Performance
     */
    public function updatePersonalData(): void
    {
        // Prüfe ob überhaupt Änderungen vorliegen
        if (!$this->hasAnyChanges()) {
            Flux::toast(
                text: __('No changes detected.'),
                heading: __('No Update'),
                variant: 'warning'
            );
            return;
        }

        // WICHTIG: Validiere NUR die geänderten Felder
        $this->validateOnlyChanged();

        try {
            DB::transaction(function () {
                // Update User Felder wenn geändert
                $userUpdateData = [];

                if ($this->joinedAtHasChanged()) {
                    $userUpdateData['joined_at'] = $this->joined_at;
                }
                if ($this->professionHasChanged()) {
                    $userUpdateData['profession_id'] = $this->profession;
                }
                if ($this->stageHasChanged()) {
                    $userUpdateData['stage_id'] = $this->stage;
                }
                // Update User nur wenn Felder geändert wurden
                if (!empty($userUpdateData)) {
                    $this->user->update($userUpdateData);
                }

                // Erstelle Update-Array nur mit geänderten Employee Feldern
                $updateData = [];

                if ($this->personalNumberHasChanged()) {
                    $updateData['personal_number'] = $this->personal_number;
                }
                if ($this->employmentTypeHasChanged()) {
                    $updateData['employment_type'] = $this->employment_type;
                }
                if ($this->probationEnumHasChanged()) {
                    $updateData['probation_enum'] = $this->probation_enum;
                }
                if ($this->probationAtHasChanged()) {
                    $updateData['probation_at'] = $this->probation_at ?: null;
                }
                if ($this->noticeAtHasChanged()) {
                    $updateData['notice_at'] = $this->notice_at ?: null;
                }
                if ($this->noticeEnumHasChanged()) {
                    $updateData['notice_enum'] = $this->notice_enum;
                }
                if ($this->leaveAtHasChanged()) {
                    $updateData['leave_at'] = $this->leave_at ?: null;
                }

                if ($this->employee) {
                    // Update nur wenn Felder geändert wurden
                    if (!empty($updateData)) {
                        $this->employee->update($updateData);
                    }
                } else {
                    // Erstelle neuen Employee Record
                    $this->employee = Employee::create([
                        'user_id' => $this->userId,
                        ...$updateData
                    ]);
                }
            });

            // Aktualisiere Original-Daten nach erfolgreichem Update
            $this->updateOriginalDataAfterSave();

            Flux::toast(
                text: __('Personal data updated successfully.'),
                heading: __('Success'),
                variant: 'success'
            );

        } catch (\Throwable $e) {
            $this->handleEditingError($e);
        }
    }

    /**
     * Aktualisiert die Original-Daten nach erfolgreichem Speichern
     */
    private function updateOriginalDataAfterSave(): void
    {
        $this->originalData = [
            'joined_at' => $this->joined_at,
            'personal_number' => $this->personal_number,
            'employment_type' => $this->employment_type,
            'profession_id' => $this->profession,
            'stage_id' => $this->stage,
            'probation_enum' => $this->probation_enum,
            'probation_at' => $this->probation_at,
            'notice_at' => $this->notice_at,
            'notice_enum' => $this->notice_enum,
            'leave_at' => $this->leave_at,
        ];
    }

    public function placeholder(): string
    {
        return view('livewire.placeholders.employee.personal-data');
    }

    public function render(): View
    {
        return view('livewire.alem.employee.profile.employment.data');
    }
}
