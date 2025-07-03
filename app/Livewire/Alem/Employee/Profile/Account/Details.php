<?php

namespace App\Livewire\Alem\Employee\Profile\Account;

use App\Livewire\Alem\Employee\Helper\Secure\HandleCatchError;
use App\Livewire\Alem\Employee\Profile\Account\Helper\ValidateAccountDetails;
use App\Models\User;
use App\Traits\Enum\GenderOptions;
use App\Traits\Model\ModelStatusOptions;
use App\Traits\User\AuthUserTeamCompanyId;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Lazy]
class Details extends Component
{
    use AuthorizesRequests;
    use AuthUserTeamCompanyId;
    use ValidateAccountDetails, HandleCatchError;
    use ModelStatusOptions, GenderOptions;

    #[Locked]
    public int $userId;

    public ?User $user = null; // der geladene User

    /** User form fields */
    public ?string $gender = null;
    public ?string $name = null;
    public ?string $email = null;
    public ?string $phone_1 = null;
    public ?string $phone_2 = null;
    public ?string $model_status = null;

    /** Original Daten des Users aus der Datenbank für Vergleiche */
    public array $originalData = [];

    public function mount(int $userId, int $authUserId, int $currentTeamId, int $companyId): void
    {
        $this->userId = $userId;
        $this->authUserId = $authUserId;
        $this->currentTeamId = $currentTeamId;
        $this->companyId = $companyId;

        // Lade userId mit allen benötigten Relations
        $this->user = User::select([
                'id', 'name', 'email', 'gender', 'model_status', 'user_type',
                'phone_1', 'phone_2'
            ])
            ->findOrFail($this->userId);

        $this->loadEmployeeData();

////        // Lade Relation für Dropdown-Daten
//        $this->loadRelationsData([
//            'teams', 'departments', 'roles', 'supervisors'
//        ]);
    }

    /**
     * Befülle die Form mit User Employee Daten
     * Speichere Original-Daten aus der Datenbank für den Vergleich
     */
    private function loadEmployeeData(): void
    {
        if (!$this->user) return;

        // WICHTIG: Speichere Original-Daten in EINEM public Array
        $this->originalData = [
            'gender' => $this->user->gender?->value,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'phone_1' => $this->user->phone_1,
            'phone_2' => $this->user->phone_2,
            'model_status' => $this->user->model_status?->value,
        ];

        $this->gender = $this->user->gender?->value;
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->phone_1 = $this->user->phone_1;
        $this->phone_2 = $this->user->phone_2;

        // Setze selected Arrays
        $this->model_status = $this->user->model_status?->value;
    }


    /**
     * Aktualisiert die Benutzer- und Mitarbeiterdaten in der Datenbank.
     * Validiert nur die geänderten Felder für bessere Performance
     */
    public function updateEmployee(): void
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
                // Erstelle Update-Array nur mit geänderten Feldern
                $updateData = [];

                if ($this->genderHasChanged()) {
                    $updateData['gender'] = $this->gender;
                }
                if ($this->nameHasChanged()) {
                    $updateData['name'] = $this->name;
                }
                if ($this->emailHasChanged()) {
                    $updateData['email'] = $this->email;
                }
                if ($this->phoneHasChanged()) {
                    $updateData['phone_1'] = $this->phone_1;
                }
                if ($this->phone2HasChanged()) {
                    $updateData['phone_2'] = $this->phone_2;
                }
                if ($this->modelStatusHasChanged()) {
                    $updateData['model_status'] = $this->model_status;
                }

                // Update nur wenn Felder geändert wurden
                if (!empty($updateData)) {
                    $this->user->update($updateData);
                }

            });

            // Aktualisiere Original-Daten nach erfolgreichem Update
            $this->updateOriginalDataAfterSave();

            $this->dispatch('employee-updated');

            Flux::toast(
                text: __('Employee Account Details updated successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

        } catch (\Throwable $e) {
            $this->handleEditingError($e);
        }
    }

    /**
     * Prüft, ob Änderungen an den Feldern vorgenommen wurden
     */
    private function updateOriginalDataAfterSave(): void
    {
        // Update nur die originalData, ohne die Form-Felder zu überschreiben
        $this->originalData = [
            'gender' => $this->gender,
            'name' => $this->name,
            'email' => $this->email,
            'phone_1' => $this->phone_1,
            'phone_2' => $this->phone_2,
            'model_status' => $this->model_status,
        ];
    }

    public function placeholder(): string
    {
        return view('livewire.placeholders.employee.details');
    }

    public function render(): View
    {
        return view('livewire.alem.employee.profile.account.details', [
            'user' => $this->user ?? null
        ]);
    }
}
