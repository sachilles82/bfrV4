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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Lazy;
use Livewire\Component;

//#[Lazy(isolate: true)]
class Details extends Component
{
    use AuthorizesRequests, ValidateAccountDetails;
    use AuthUserTeamCompanyId, WithDropDownRelations;
    use ModelStatusOptions, GenderOptions;

    // User identification
    public ?User $user = null;
    protected string $sharedDataKey;

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

    // Static method für Relations
    public static function requiredRelations(): array
    {
        return [
            'teams:id,name',
            'roles:id,name,is_manager',
            'department:id,name'
        ];
    }

    public function mount(string $sharedDataKey, int $authUserId, int $currentTeamId, int $companyId): void {
        $this->sharedDataKey = $sharedDataKey;
        $this->authUserId = $authUserId;
        $this->currentTeamId = $currentTeamId;
        $this->companyId = $companyId;

        // Lade User aus Cache
        $this->loadUserFromCache();

        // Lade nur Dropdown-Daten (nutzt deinen WithDropDownRelations Trait)
        $this->loadRelationsData(['teams', 'departments', 'roles']);
    }

    private function loadUserFromCache(): void
    {
        $this->user = Cache::get($this->sharedDataKey);

        if ($this->user) {
            // Populate properties vom gecachten User
            $this->gender = $this->user->gender;
            $this->name = $this->user->name;
            $this->last_name = $this->user->last_name;
            $this->email = $this->user->email;
            $this->phone_1 = $this->user->phone_1 ?? '';
            $this->model_status = $this->user->model_status;
            $this->department = $this->user->department_id;

            // Relations sind bereits geladen
            $this->selectedTeams = $this->user->teams->pluck('id')->toArray();
            $this->selectedRoles = $this->user->roles->pluck('id')->toArray();
        }
    }

    protected function shouldCheckModalState(): bool
    {
        return false;
    }

    protected function isModalOpen(): bool
    {
        return true;
    }

    public function updateEmployee(): void
    {
        $this->validate();

        try {
            DB::transaction(function () {
                User::where('id', $this->user->id)->update([
                    'name' => $this->name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'phone_1' => $this->phone_1,
                    'gender' => $this->gender,
                    'model_status' => $this->model_status,
                    'department_id' => $this->department,
                ]);

                $this->syncRelations();
            });

            // Cache invalidieren
            Cache::forget($this->sharedDataKey);

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

    private function syncRelations(): void
    {
        DB::transaction(function (): void {
            $wasManager = $this->user->hasManagerRole();

            $this->user->roles()->sync($this->selectedRoles);
            $this->user->teams()->sync($this->selectedTeams);

            if ($wasManager !== $this->user->hasManagerRole()) {
                User::clearManagerCache($this->user->company_id);
                $this->forceReloadCollection('supervisors');
            }
        });
    }

    public function render(): View
    {
        return view('livewire.alem.employee.profile.account.details');
    }
}
