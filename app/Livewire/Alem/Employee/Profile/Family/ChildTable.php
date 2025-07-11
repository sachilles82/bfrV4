<?php

namespace App\Livewire\Alem\Employee\Profile\Family;

use App\Models\Alem\Child;
use App\Traits\Table\WithPerPagePagination;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
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

//    // In ChildTable.php
//    public int $refreshKey = 0;
//
//    #[On('child-created')]
//    public function refreshTable(): void
//    {
//        $this->refreshKey++;
//        $this->resetPage();
//    }

    /**
     * Löscht ein Kind
     */
    public function delete($childId): void
    {
        $child = Child::find($childId);

        if ($child && $child->user_id === $this->userId) {
            $child->delete();

            Flux::toast(
                text: __('Child removed successfully.'),
                heading: __('Success'),
                variant: 'success'
            );
        }
    }

    public function render(): View
    {
        $children = Child::query()
            ->select([
                'id',
                'name',
                'gender',
                'birthdate',
                'ahv_number',
                'valid_until',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->where('user_id', $this->userId)
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
