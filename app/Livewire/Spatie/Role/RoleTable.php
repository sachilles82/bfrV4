<?php

namespace App\Livewire\Spatie\Role;

use App\Enums\Role\RoleHasAccessTo;
use App\Enums\Role\RoleVisibility;
use App\Livewire\Spatie\Role\Helper\Searchable;
use App\Livewire\Spatie\Role\Helper\ValidateRole;
use App\Models\Spatie\Role;
use App\Traits\Table\WithPerPagePagination;
use Flux\Flux;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class RoleTable extends Component
{
    use Searchable, ValidateRole, WithPerPagePagination;

    public $roleId;

    public $name;

    public $description;

    public $access;

    public $is_manager;

    public function mount(): void
    {
        $this->authorize('viewAny', Role::class);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function loadRole($id): void
    {
        try {
            $role = Role::findOrFail($id);

            $this->authorize('update', $role);

            $this->roleId = $role->id;
            $this->name = $role->name;
            $this->description = $role->description;
            $this->access = $role->access->value;
            $this->is_manager = $role->is_manager;
        } catch (AuthorizationException $ae) {
            // Bei Berechtigungsproblemen
            Flux::toast(
                text: __('You are not authorized to edit this role.'),
                heading: __('Unauthorized'),
                variant: 'danger'
            );
        } catch (\Exception $e) {

            Flux::toast(
                text: __('Error loading role: '),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    public function showEditModal($id): void
    {
        $this->reset(['roleId', 'name', 'description']);
        $this->resetErrorBag();

        $this->loadRole($id);

        $this->modal('role-edit')->show();
    }

    public function update(): void
    {
        try {
            $this->validate();
            $role = Role::findOrFail($this->roleId);

            $this->authorize('update', $role);

            $role->update([
                'name' => $this->name,
                'description' => $this->description,
                'access' => RoleHasAccessTo::from($this->access),
                'is_manager' => $this->is_manager,
            ]);

            Flux::toast(
                text: __('Role updated successfully.'),
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
        } finally {
            $this->modal('role-edit')->close();
        }
    }

    public function delete($id): void
    {
        try {
            $role = Role::findOrFail($id);

            $this->authorize('delete', $role);

            $role->delete();

            Flux::toast(
                text: __('Role deleted successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );
        } catch (AuthorizationException $ae) {
            Flux::toast(
                text: __('You are not authorized to delete this role.'),
                heading: __('Unauthorized'),
                variant: 'danger'
            );
        } catch (\Exception $e) {
            Flux::toast(
                text: __('Error deleting role: '),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    #[On('role-created')]
    public function resetFilters(): void
    {
        $this->resetPage();
        $this->reset('search');
    }

    public function render(): View
    {
        $user = auth()->user();

        $query = Role::query()
            ->select('id', 'name', 'description', 'access', 'created_by', 'is_manager')
            ->with(['creator:id,name'])
            ->withCount('permissions')
            ->where(function ($q) use ($user) {
                $q->where('company_id', $user->company_id)
                    ->orWhere(function ($or) {
                        $or->where('created_by', 1)
                            ->where('visible', RoleVisibility::Visible);
                    });
            });

        $this->applySearch($query);
        $query->latest('id');

        $roles = $this->applyPagination($query);

        $accessOptions = collect(RoleHasAccessTo::cases())
            ->filter(fn ($case) => in_array($case, [
                RoleHasAccessTo::EmployeePanel,
                RoleHasAccessTo::PartnerPanel,
            ]))
            ->values();

        return view('livewire.spatie.role.table',
            compact('roles', 'accessOptions')
        );
    }
}
