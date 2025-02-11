<?php

namespace App\Livewire\Spatie\Role\Permission;

use App\Enums\Role\Permission;
use App\Livewire\Spatie\Role\Helper\HasAppData;
use App\Models\Spatie\Role;
use Flux\Flux;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Livewire\Component;
use Spatie\Permission\PermissionRegistrar;

class Update extends Component
{
    use AuthorizesRequests, HasAppData;

    public int $roleId;
    public Role $role;
    public string $app;

    public string $name;
    public array $selectedIds = [];
    public array $idsOnPage = [];

    public array $allPermissions = [];

    public function mount(int $roleId, string $app): void
    {
        $this->roleId = $roleId;

        $userId = auth()->id();

        $this->role = Cache::remember("user.{$userId}.role.{$roleId}",
            now()->addMinutes(10),
            function () use ($roleId) {
                return Role::with(['permissions' => function ($query) {
                    $query->select('id', 'name', 'group', 'app_name');
                }])->findOrFail($roleId);
            });

        $this->authorize('view', $this->role);

        $this->app = $app;
        $this->name = $this->role->name;

        $permissionsForApp = Cache::remember("user.{$userId}.permissions_for_app.{$app}",
            now()->addMinutes(10),
            function () use ($app) {
                return collect(Permission::casesForApp($app))
                    ->toArray();
            });

        $this->allPermissions = $permissionsForApp;

        $this->idsOnPage = collect($permissionsForApp)
            ->pluck('value')
            ->toArray();

        $this->selectedIds = $this->role->permissions
            ->pluck('name')
            ->toArray();
    }

    public function updateRole(): void
    {
        $userId = auth()->id();
        try {

            $this->authorize('update', $this->role);

            $this->role->syncPermissions($this->selectedIds);

            app(PermissionRegistrar::class)
                ->forgetCachedPermissions();

            Cache::forget("user.{$userId}.role.{$this->roleId}");
            Cache::forget("user.{$userId}.permissions_for_app.{$this->app}");

            Flux::toast(
                text: __('Permission successfully updated.'),
                heading: __('Success.'),
                variant: 'success'
            );

        } catch (AuthorizationException $ae) {
            Flux::toast(
                text: __('You are not authorized to update this role.'),
                heading: __('Unauthorized'),
                variant: 'danger'
            );
        } catch (\Exception $e) {
            Flux::toast(
                text: __('An error occurred while updating the role. Please try again later.'),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    public function placeholder(): View
    {
        return view('livewire.placeholders.company.update');
    }

    public function render(): View
    {
        $permissionsByGroup = collect($this->allPermissions)
            ->groupBy(fn($perm) => $perm->group());

        $appData = $this->getAppData();

        return view('livewire.spatie.role.permission.update', [
            'permissionsByGroup' => $permissionsByGroup,
            'appData' => $appData,
        ]);
    }
}
