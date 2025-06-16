<?php

namespace App\Livewire\Alem\Employee\Profile\Account;

use App\Enums\Model\ModelStatus;
use App\Enums\User\Gender;
use App\Livewire\Alem\Employee\Helper\WithDropDownRelations;
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
use Livewire\Component;

#[Lazy(isolate: true)]
class Details extends Component
{
    use AuthorizesRequests, ValidateAccountDetails;
    use AuthUserTeamCompanyId, WithDropDownRelations;
    use ModelStatusOptions, GenderOptions;

    // User identification
    public ?User $user = null;

    // User form fields
    public ?Gender $gender = null;
    public ?string $name = null;
    public ?string $last_name = null;
    public ?string $email = null;
    public ?string $phone_1 = null;
    public ?ModelStatus $model_status = null;
    public ?int $department = null;
    public array $selectedTeams = [];
    public array $selectedRoles = [];

    // Flag ob Daten geladen wurden
    private bool $dataLoaded = false;

    /**
     * Mount erhält den minimalen User und die Auth-Properties
     */
    public function mount(User $user, int $authUserId, int $currentTeamId, int $companyId): void
    {
        $this->user = $user;
        $this->authUserId = $authUserId;
        $this->currentTeamId = $currentTeamId;
        $this->companyId = $companyId;

        // Sofort Employee-Daten und Relations laden
        $this->loadEmployeeData();
        $this->loadRelationsData(['teams', 'departments', 'roles']);
    }

    /**
     * Lädt alle benötigten Daten für diese Komponente
     */
    private function loadEmployeeData(): void
    {
        if ($this->dataLoaded) {
            return;
        }

        // Lade den vollständigen User mit Relations - genau wie im EditEmployee
        $this->user = User::with([
            'teams:id,name',
            'roles:id,name,is_manager',
            'department:id,name'
        ])->findOrFail($this->user->id);

        // Befülle die Komponenten-Properties
        $this->gender = $this->user->gender;
        $this->name = $this->user->name;
        $this->last_name = $this->user->last_name;
        $this->email = $this->user->email;
        $this->phone_1 = $this->user->phone_1 ?? '';
        $this->model_status = $this->user->model_status;
        $this->department = $this->user->department_id;

        // Teams und Rollen - genau wie im EditEmployee
        $this->selectedTeams = $this->user->teams->pluck('id')->toArray();
        $this->selectedRoles = $this->user->roles->pluck('id')->toArray();

        $this->dataLoaded = true;
    }

    /**
     * Override der shouldCheckModalState für den WithDropDownRelations Trait
     */
    protected function shouldCheckModalState(): bool
    {
        return false; // Kein Modal in diesem Component
    }

    protected function isModalOpen(): bool
    {
        return true; // Immer "offen" da kein Modal
    }


    /**
     * Aktualisiert die User-Daten
     */
    public function updateEmployee(): void
    {
        $this->validate();

        try {
            DB::transaction(function () {
                // Update der User-Daten - verwende die gleiche Logik wie EditEmployee
                User::where('id', $this->user->id)->update([
                    'name' => $this->name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'phone_1' => $this->phone_1,
                    'gender' => $this->gender,
                    'model_status' => $this->model_status,
                    'department_id' => $this->department,
                ]);

                // Sync Relations
                $this->syncRelations();
            });

            $this->dispatch('employee-updated');

            Flux::toast(
                text: __('Employee Profile updated successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

        } catch (\Throwable $e) {
            Flux::toast(
                text: __('Error updating employee profile: ') . $e->getMessage(),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    /**
     * Synchronisiert Rollen und Teams des Users - identisch zu EditEmployee
     */
    private function syncRelations(): void
    {
        DB::transaction(function (): void {
            // Cache Manager-Status vor Änderung
            $wasManager = $this->user->hasManagerRole();

            // Batch-Synchronisation
            $this->user->roles()->sync($this->selectedRoles);
            $this->user->teams()->sync($this->selectedTeams);

            // Handle Manager-Status-Änderung
            if ($wasManager !== $this->user->hasManagerRole()) {
                User::clearManagerCache($this->user->company_id);
                $this->forceReloadCollection('supervisors');
            }
        });
    }

    /**
     * Debug-Methode um die geladenen Rollen zu prüfen
     */
    public function debugRoles()
    {
        dd([
            'authUserId' => $this->authUserId,
            'currentTeamId' => $this->currentTeamId,
            'companyId' => $this->companyId,
            'user_roles' => $this->user->roles->toArray(),
            'selected_roles' => $this->selectedRoles,
            'available_roles' => $this->roles,
            'loaded_collections' => $this->loadedCollections ?? []
        ]);
    }

    public function render(): View
    {
        // Keine weitere Logik hier - alles bereits in mount() geladen
        return view('livewire.alem.employee.profile.account.details');
    }
}
