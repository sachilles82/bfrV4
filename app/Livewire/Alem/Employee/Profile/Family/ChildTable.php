<?php

namespace App\Livewire\Alem\Employee\Profile\Family;

use App\Models\Alem\Child;
use App\Traits\Table\WithPerPagePagination;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
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

    // In ChildTable.php
    public int $refreshKey = 0;

    #[On('child-created')]
    #[On('child-updated')]
    #[On('child-deleted')]
    public function refreshTable(): void
    {
        $this->refreshKey++;
        $this->resetPage();
    }

    public function delete($childId): void
    {
        try {
            $child = Child::find($childId);

            if ($child && $child->user_id === $this->userId) {
                $childName = $child->name;
                $child->delete();

                sleep(1); // Optional: wie im Tutorial für visuelles Feedback

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
