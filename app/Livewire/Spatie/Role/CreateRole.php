<?php

namespace App\Livewire\Spatie\Role;

use App\Enums\Role\RoleHasAccessTo;
use App\Enums\Role\RoleVisibility;
use App\Livewire\Spatie\Role\Helper\ValidateRole;
use App\Models\Spatie\Role;
use Flux\Flux;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Component;

class CreateRole extends Component
{
    use AuthorizesRequests, ValidateRole;

    public $name;

    public $description;

    public $access;

    public $is_manager = false;

    public function save(): void
    {
        try {

            $this->authorize('create', Role::class);

            $this->validate();

            Role::create([
                'name' => $this->name,
                'description' => $this->description,
                'access' => $this->access,
                'visible' => RoleVisibility::Visible,
                'is_manager' => $this->is_manager,
            ]);
            $this->modal('role-add')->close();
            $this->dispatch('role-created');

            Flux::toast(
                text: __('Role created successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

            $this->reset(['name', 'description', 'access', 'is_manager']);

        } catch (ValidationException $ve) {
            Flux::toast(
                text: __('Validation error. Please check your inputs.'),
                heading: __('Error'),
                variant: 'danger'
            );
        } catch (AuthorizationException $ae) {
            Flux::toast(
                text: __('You are not authorized to create a role.'),
                heading: __('Unauthorized'),
                variant: 'danger'
            );
        } catch (\Exception $e) {
            Flux::toast(
                text: __('Error creating role: ').$e->getMessage(),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    public function render(): View
    {
        $accessOptions = collect(RoleHasAccessTo::cases())
            ->filter(fn ($case) => in_array($case, [
                RoleHasAccessTo::EmployeePanel, RoleHasAccessTo::PartnerPanel,
            ]))
            ->values();

        return view('livewire.spatie.role.create',
            compact('accessOptions')
        );
    }
}
