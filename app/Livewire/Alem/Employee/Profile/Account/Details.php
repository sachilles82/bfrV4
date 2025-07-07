<?php

namespace App\Livewire\Alem\Employee\Profile\Account;

use App\Livewire\Alem\Employee\Profile\Account\Helper\HandleCatchError;
use App\Livewire\Alem\Employee\Profile\Account\Helper\ValidateAccountDetails;
use App\Models\User;
use App\Traits\Enum\GenderOptions;
use App\Traits\Model\ModelStatusOptions;
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
class Details extends Component
{
    use AuthorizesRequests;
    use AuthUserTeamCompanyId;
    use ValidateAccountDetails, HandleCatchError;
    use ModelStatusOptions, GenderOptions;

    #[Locked]
    public int $userId;

    /** Form fields */
    public ?string $gender = null;
    public ?string $name = null;
    public ?string $email = null;
    public ?string $phone_1 = null;
    public ?string $phone_2 = null;
    public ?string $model_status = null;

    /** Original Daten für Vergleiche */
    public array $originalData = [];

    public function mount(int $userId, int $authUserId, int $currentTeamId, int $companyId): void
    {
        $this->userId = $userId;
        $this->authUserId = $authUserId;
        $this->currentTeamId = $currentTeamId;
        $this->companyId = $companyId;

        $this->loadAccountDetails();
    }

    /**
     * User als Computed Property
     * Wird automatisch gecached und nach Validation Error neu geladen
     */
    #[Computed]
    public function user(): User
    {
        return User::select([
            'id', 'name', 'email', 'gender', 'model_status', 'user_type',
            'phone_1', 'phone_2'
        ])
            ->findOrFail($this->userId);
    }

    /**
     * Befülle die Form mit Account Details
     * Speichere Original-Daten aus der Datenbank für den Vergleich
     */
    private function loadAccountDetails(): void
    {
        $user = $this->user;

        // Original-Daten speichern - KEINE empty strings, nur null
        $this->originalData = [
            'gender' => $user->gender?->value,
            'name' => $user->name,
            'email' => $user->email,
            'phone_1' => $user->phone_1,
            'phone_2' => $user->phone_2,
            'model_status' => $user->model_status?->value,
        ];

        // Setze Form-Felder NUR mit den originalData
        $this->gender = $this->originalData['gender'];
        $this->name = $this->originalData['name'];
        $this->email = $this->originalData['email'];
        $this->phone_1 = $this->originalData['phone_1'];
        $this->phone_2 = $this->originalData['phone_2'];
        $this->model_status = $this->originalData['model_status'];
    }

    /**
     * Aktualisiert die Account Details in der Datenbank.
     * Validiert nur die geänderten Felder für bessere Performance
     */
    public function updateAccountDetails(): void
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
                    $updateData['name'] = $this->sanitizeName($this->name);
                }
                if ($this->emailHasChanged()) {
                    $updateData['email'] = $this->email;
                }
                if ($this->phoneHasChanged()) {
                    $updateData['phone_1'] = $this->sanitizePhoneNumber($this->phone_1) ?: null;
                }
                if ($this->phone2HasChanged()) {
                    $updateData['phone_2'] = $this->sanitizePhoneNumber($this->phone_2) ?: null;
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

            // WICHTIG: Clear Computed Property Cache nach Update
            unset($this->user);

            $this->dispatch('account-details-updated');

            Flux::toast(
                text: __('Account details updated successfully.'),
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
        return view('livewire.placeholders.employee.account-details');
    }

    public function render(): View
    {
        return view('livewire.alem.employee.profile.account.details');
    }
}
