<?php

namespace App\Livewire\Alem\Employee\Profile\Family;

use App\Models\Alem\Child;
use App\Traits\Table\WithPerPagePagination;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Lazy]
class ChildTable extends Component
{
    use AuthorizesRequests;
    use WithPerPagePagination;

    #[Locked]
    public ?int $userId = null;

    public function mount(int $userId): void
    {
        $this->userId = $userId;
    }

    public function delete($childId): void
    {
        try {
            $child = Child::find($childId);

            //Authorize the action

            if ($child && $child->user_id === $this->userId) {
                $childName = $child->name;

                $child->delete();

                $this->resetPage();

                Flux::toast(
                    text: __('Child :name removed successfully.', ['name' => $childName]),
                    heading: __('Success'),
                    variant: 'success'
                );
            }
        } catch (\Throwable $e) {
            Flux::toast(
                text: __('Error deleting child.'),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }



    public function render(): View
    {
        $children = Child::query()
            ->where('user_id', $this->userId)
            ->select([
                'id',
                'name',
                'gender',
                'birthdate',
                'ahv_number',
                'valid_until',
                'user_id',
            ])
            ->latest()
            ->simplePaginate($this->perPage);

        return view('livewire.alem.employee.profile.family.child-table', [
            'children' => $children
        ]);
    }

    public function placeholder(): View
    {
        return view('livewire.placeholders.employee.family.child-table');
    }
}
