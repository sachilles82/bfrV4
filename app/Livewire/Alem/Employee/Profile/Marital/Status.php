<?php

namespace App\Livewire\Alem\Employee\Profile\Marital;

use App\Livewire\Alem\Employee\Profile\Marital\Helper\HandleCatchError;
use App\Livewire\Alem\Employee\Profile\Marital\Helper\ValidateMaritalData;
use App\Models\Alem\Employee;
use App\Models\User;
use App\Traits\Employee\EmployeeDataEnums;
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
class Status extends Component
{
    use AuthorizesRequests;
    use AuthUserTeamCompanyId;
    use ValidateMaritalData, HandleCatchError;
    use EmployeeDataEnums;

    #[Locked]
    public int $userId;

    /** Form fields */
    public ?string $name_partner = null;
    public ?string $ahv_partner = null;
    public ?string $birthdate_partner = null;
    public ?string $civil_status = null;
    public ?string $marriage_at = null;
    public ?string $single_parent = null;

    /** Original Daten für Vergleiche */
    public array $originalData = [];

    public function mount(int $userId, int $authUserId, int $currentTeamId, int $companyId): void
    {
        $this->userId = $userId;
        $this->authUserId = $authUserId;
        $this->currentTeamId = $currentTeamId;
        $this->companyId = $companyId;

        $this->loadEmployeeMaritalData();
    }

    /**
     * Employee als Computed Property
     * Wird automatisch gecached und nach Validation Error neu geladen
     */
    #[Computed]
    public function employee(): ?Employee
    {
        return Employee::with([
            'user:id'
        ])
            ->where('user_id', $this->userId)
            ->select([
                'id',
                'user_id',
                'civil_status',
                'name_partner',
                'single_parent',
                'birthdate_partner',
                'ahv_partner',
                'marriage_at',
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
        return User::select('id')
            ->findOrFail($this->userId);
    }

    /**
     * Befülle die Form mit Personal Daten
     * Speichere Original-Daten aus der Datenbank für den Vergleich
     */
    private function loadEmployeeMaritalData(): void
    {
        $employee = $this->employee;

        // Original-Daten speichern - KEINE empty strings, nur null
        $this->originalData = [
            'name_partner' => $employee->name_partner,
            'ahv_partner' => $employee->ahv_partner,
            'birthdate_partner' => $employee->birthdate_partner,
            'civil_status' => $employee->civil_status,
            'marriage_at' => $employee->marriage_at?->format('Y-m-d'),
            'single_parent' => $employee->single_parent,
        ];

        // Form-Felder befüllen
        $this->name_partner = $this->originalData['name_partner'];
        $this->ahv_partner = $this->originalData['ahv_partner'];
        $this->birthdate_partner = $this->originalData['birthdate_partner'];
        $this->civil_status = $this->originalData['civil_status'];
        $this->marriage_at = $this->originalData['marriage_at'];
        $this->single_parent = $this->originalData['single_parent'];
    }

    /**
     * Aktualisiert die Personal Daten in der Datenbank.
     * Validiert nur die geänderten Felder für bessere Performance
     */
    public function updateMaritalData(): void
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
                // Erstelle Update-Array nur mit geänderten Employee Feldern
                $updateData = [];

                if ($this->civilStatusHasChanged()) {
                    $updateData['civil_status'] = $this->civil_status;
                }
                if ($this->namePartnerHasChanged()) {
                    $updateData['name_partner'] = $this->name_partner;
                }
                if ($this->singleParentHasChanged()) {
                    $updateData['single_parent'] = $this->single_parent;
                }
                if ($this->birthdatePartnerHasChanged()) {
                    $updateData['birthdate_partner'] = $this->birthdate_partner;
                }
                if ($this->ahvPartnerHasChanged()) {
                    $updateData['ahv_partner'] = $this->ahv_partner;
                }
                if ($this->marriageAtHasChanged()) {
                    $updateData['marriage_at'] = $this->marriage_at ?: null;
                }

                if ($this->employee) {
                    // Update nur wenn Felder geändert wurden
                    if (!empty($updateData)) {
                        $this->employee->update($updateData);
                    }
                } else {
                    // Erstelle neuen Employee Record nur wenn es Employee-Daten gibt
                    if (!empty($updateData)) {
                        Employee::create([
                            'user_id' => $this->userId,
                            ...$updateData
                        ]);

                        // WICHTIG: Clear Computed Property Cache nach Create
                        unset($this->employee);
                    }
                }
            });

            // Aktualisiere Original-Daten nach erfolgreichem Update
            $this->updateOriginalDataAfterSave();

            $this->dispatch('personal-data-updated');

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
            'name_partner' => $this->name_partner,
            'ahv_partner' => $this->ahv_partner,
            'birthdate_partner' => $this->birthdate_partner,
            'civil_status' => $this->civil_status,
            'marriage_at' => $this->marriage_at,
            'single_parent' => $this->single_parent,
        ];
    }


    public function render(): View
    {
        return view('livewire.alem.employee.profile.marital.status');
    }
}
