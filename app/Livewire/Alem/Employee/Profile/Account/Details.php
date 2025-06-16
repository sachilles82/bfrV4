<?php

namespace App\Livewire\Alem\Employee\Profile\Account;

use App\Enums\Model\ModelStatus;
use App\Enums\User\Gender;
use App\Livewire\Alem\Employee\Helper\WithDropDownRelations;
use App\Models\User;
use App\Traits\Enum\GenderOptions;
use App\Traits\Model\ModelStatusOptions;
use App\Traits\User\AuthUserTeamCompanyId;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy(isolate: true)]
class Details extends Component
{
    use AuthorizesRequests;
    use WithDropDownRelations;
    use ModelStatusOptions, GenderOptions;

    // Der minimale User aus dem Controller
    public User $user;

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
     * Mount erhält nur den minimalen User
     */
    public function mount(User $user): void
    {
        $this->user = $user;

        // Initialisiere Auth Properties für den Trait
//        $this->initializeAuthProperties();
    }

    /**
     * Lädt alle benötigten Daten für diese Komponente
     */
    private function loadEmployeeData(): void
    {
        if ($this->dataLoaded) {
            return;
        }

        $cacheKey = "employee_account_details_{$this->user->id}";

        $userData = Cache::remember($cacheKey, now()->addMinutes(15), function () {
            return User::with([
                'teams:id,name',
                'roles:id,name,is_manager',
                'department:id,name',
                'employee:id,user_id'
            ])
                ->select([
                    'id', 'name', 'last_name', 'email', 'phone_1', 'gender',
                    'department_id', 'model_status', 'company_id'
                ])
                ->find($this->user->id);
        });

        if (!$userData) {
            abort(404, 'Mitarbeiter nicht gefunden.');
        }

        // Setze die Company ID für den Trait
        $this->companyId = $userData->company_id;

        // Befülle die Komponenten-Properties
        $this->gender = $userData->gender;
        $this->name = $userData->name;
        $this->last_name = $userData->last_name;
        $this->email = $userData->email;
        $this->phone_1 = $userData->phone_1 ?? '';
        $this->model_status = $userData->model_status;
        $this->department = $userData->department_id;

        // Teams und Rollen
        $this->selectedTeams = $userData->teams->pluck('id')->toArray();
        $this->selectedRoles = $userData->roles->pluck('id')->toArray();

        // Lade Dropdown-Daten mit dem Trait
        $this->loadDropdownRelationsData();

        $this->dataLoaded = true;
    }

    /**
     * Überschreibt die initializeDropdownRelations Methode vom Trait
     */
    protected function loadDropdownRelationsData(): void
    {
        // Lade nur die benötigten Collections
        $this->loadRelationsData(['teams', 'departments', 'roles']);
    }

    /**
     * Aktualisiert die User-Daten
     */
    public function updateEmployee(): void
    {
        $this->validate();

        try {
            DB::transaction(function () {
                // Lade den vollständigen User für das Update
                $fullUser = User::find($this->user->id);

                // Update der User-Daten
                $fullUser->update([
                    'gender' => $this->gender,
                    'name' => $this->name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'phone_1' => $this->phone_1,
                    'model_status' => $this->model_status,
                    'department_id' => $this->department,
                ]);

                // Sync Relations
                $this->syncRelations($fullUser);
            });

            // Cache invalidieren
            $this->invalidateCaches();

            Flux::toast(
                text: __('User updated successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

            $this->dispatch('employee-updated');

        } catch (\Exception $e) {
            Flux::toast(
                text: __('Error updating user: ') . $e->getMessage(),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    /**
     * Synchronisiert Rollen und Teams des Users
     */
    private function syncRelations(User $user): void
    {
        // Cache Manager-Status vor Änderung
        $wasManager = $user->hasManagerRole();

        // Teams synchronisieren
        $user->teams()->sync($this->selectedTeams);

        // Rollen synchronisieren
        $user->roles()->sync($this->selectedRoles);

        // Handle Manager-Status-Änderung
        if ($wasManager !== $user->hasManagerRole()) {
            User::clearManagerCache($user->company_id);
            $this->forceReloadCollection('supervisors');
        }

        // Team wechseln wenn nötig
        if (!in_array($user->currentTeam?->id, $this->selectedTeams) && !empty($this->selectedTeams)) {
            $teamToSwitch = $user->teams()->find($this->selectedTeams[0]);
            if ($teamToSwitch) {
                $user->switchTeam($teamToSwitch);
            }
        }
    }

    /**
     * Invalidiert alle relevanten Caches
     */
    private function invalidateCaches(): void
    {
        Cache::forget("employee_account_details_{$this->user->id}");
        Cache::forget("employee_profile_{$this->user->slug}_account-details");

        // Invalidiere auch die Dropdown-Caches
        $this->resetDropdownRelationsData();
    }

    /**
     * Placeholder während des Ladens
     */
//    public function placeholder(): View
//    {
//        return view('livewire.placeholders.form-skeleton');
//    }

    public function render(): View
    {
        // Lade Daten beim ersten Render
        $this->loadEmployeeData();

        return view('livewire.alem.employee.profile.account.details');
    }
}
