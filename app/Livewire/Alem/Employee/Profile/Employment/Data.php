<?php

namespace App\Livewire\Alem\Employee\Profile\Employment;


use App\Livewire\Alem\Employee\Helper\WithDropDownRelations;
use App\Livewire\Alem\Employee\Profile\Employment\Helper\HandleCatchError;
use App\Livewire\Alem\Employee\Profile\Employment\Helper\ValidateEmploymentData;
use App\Models\Alem\Employee;
use App\Models\User;
use App\Traits\Employee\EmployeeDataEnums;
use App\Traits\Employee\EmployeeStatusOptions;
use App\Traits\User\AuthUserTeamCompanyId;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Lazy]
class Data extends Component
{
    use AuthorizesRequests;
    use AuthUserTeamCompanyId;
    use ValidateEmploymentData, HandleCatchError;
    use EmployeeStatusOptions, EmployeeDataEnums;
    use WithDropDownRelations   ;

    #[Locked]
    public int $userId;

    public ?User $user = null;
    public ?Employee $employee = null;

    /** Form fields */
    public ?string $personal_number = null;
    public ?string $joined_at = null; // hier ist es angestellt seit Datum
    public ?string $prob_period = null;
    public ?string $probation_at = null;
    public ?string $notice_at = null;
    public ?string $notice_period = null;
    public ?string $leave_at = null;
    public ?string $status = null;

    /** Original Daten für Vergleiche */
    public array $originalData = [];

    public function mount(int $userId, int $authUserId, int $currentTeamId, int $companyId): void
    {
        $this->userId = $userId;
        $this->authUserId = $authUserId;
        $this->currentTeamId = $currentTeamId;
        $this->companyId = $companyId;

        $this->loadPersonalData();
    }

    /**
     * Employee als Computed Property
     * Wird automatisch gecached und nach Validation Error neu geladen
     */
    #[Computed]
    public function employee(): ?Employee
    {
        return Employee::with([
            'user:id,joined_at,status'
        ])
            ->where('user_id', $this->userId)
            ->select([
                'id',
                'user_id',
                'personal_number',
                'prob_period',
                'probation_at',
                'notice_period',
                'notice_at',
                'leave_at'
            ])
            ->first();
    }

    /**
     * User als Computed Property
     * Lädt User entweder über Employee Relation oder direkt
     */
    #[Computed]
    public function user(): User
    {
        // Wenn Employee existiert, nutze die Relation
        if ($this->employee && $this->employee->relationLoaded('user')) {
            return $this->employee->user;
        }

        // Sonst lade User direkt
        return User::select(['id', 'birthdate'])
            ->findOrFail($this->userId);
    }

    /**
     * Befülle die Form mit Personal Daten
     * Speichere Original-Daten aus der Datenbank für den Vergleich
     */
    private function loadPersonalData(): void
    {
        $user = $this->user;
        $employee = $this->employee;

        // Original-Daten speichern
        $this->originalData = [
            'joined_at' => $user?->joined_at?->format('Y-m-d'),
            'status' => $user?->status?->value,

            'personal_number' => $this->employee?->personal_number,
            'employment_type' => $this->employee?->employment_type,
            'prob_period' => $this->employee?->prob_period?->value,
            'probation_at' => $this->employee?->probation_at?->format('Y-m-d'),
            'notice_at' => $this->employee?->notice_at?->format('Y-m-d'),
            'notice_period' => $this->employee?->notice_period?->value,
            'leave_at' => $this->employee?->leave_at?->format('Y-m-d'),
        ];

        // Setze Form-Felder
        $this->joined_at = $this->user?->joined_at?->format('Y-m-d') ?? '';
        $this->status = $this->user?->status?->value;

        $this->personal_number = $this->originalData['status'] ?? '';
        $this->personal_number = $this->originalData['personal_number'] ?? '';
        $this->prob_period = $this->originalData['prob_period'];
        $this->probation_at = $this->originalData['probation_at'] ?? '';
        $this->notice_at = $this->originalData['notice_at'] ?? '';
        $this->notice_period = $this->originalData['notice_period'];
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
                if ($this->statusHasChanged()) {
                    $userUpdateData['status'] = $this->status;
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
                if ($this->probationEnumHasChanged()) {
                    $updateData['prob_period'] = $this->prob_period;
                }
                if ($this->probationAtHasChanged()) {
                    $updateData['probation_at'] = $this->probation_at ?: null;
                }
                if ($this->noticeAtHasChanged()) {
                    $updateData['notice_at'] = $this->notice_at ?: null;
                }
                if ($this->noticeEnumHasChanged()) {
                    $updateData['notice_period'] = $this->notice_period;
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
            'staus' => $this->status ?: $this->user?->status?->value,
            'personal_number' => $this->personal_number,
            'prob_period' => $this->prob_period,
            'probation_at' => $this->probation_at,
            'notice_at' => $this->notice_at,
            'notice_period' => $this->notice_period,
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
