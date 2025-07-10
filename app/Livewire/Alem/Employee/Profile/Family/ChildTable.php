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

    // Eigenschaften für vorgeladene Daten
    #[Locked]
    public ?int $userId = null;
    public int $authUserId;
    public int $currentTeamId;
    public int $companyId;

    public function mount(int $authUserId, int $currentTeamId, int $companyId): void
    {
        $this->authUserId = $authUserId;
        $this->currentTeamId = $currentTeamId;
        $this->companyId = $companyId;
    }

    /**
     * Hört auf die Events 'child-created', 'child-updated', 'child-deleted' und aktualisiert die Tabelle
     */
    #[On(['child-created', 'child-updated', 'child-deleted'])]
    public function refreshTable(): void
    {
        $this->resetPage();
    }

    /**
     * Lifecycle-Hook: Wird aufgerufen, wenn sich eine öffentliche Eigenschaft ändert.
     */
    public function updated($property): void
    {
        // Falls später Filter hinzugefügt werden
        if ($property === 'perPage') {
            $this->resetPage();
        }
    }

    /**
     * Löscht ein Kind (Soft Delete)
     */
    public function deleteChild(int $childId): void
    {
        // $this->authorize('delete', [Child::class, $childId]);

        try {
            $child = Child::findOrFail($childId);

            // Sicherheitsprüfung - nur über Parent User
            if ($child->user_id !== $this->userId) {
                abort(403);
            }

            $child->delete();

            $this->dispatch('child-deleted');

            Flux::toast(
                text: __('Child removed successfully.'),
                heading: __('Success'),
                variant: 'success'
            );

        } catch (\Exception $e) {
            Flux::toast(
                text: __('Error deleting child.'),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        // $this->authorize('viewAny', [Child::class, $this->userId]);

        $query = Child::query();

        $query->select([
            'children.id',
            'children.name',
            'children.gender',
            'children.birthdate',
            'children.ahv_number',
            'children.valid_until',
        ]);

        // Where Bedingung für den spezifischen User
        $query->where('children.user_id', $this->userId);

        // Sortierung - neueste zuerst
        $query->latest('children.created_at');

        // Pagination
        $children = $query->simplePaginate($this->perPage);

        return view('livewire.alem.employee.profile.family.child-table', [
            'children' => $children
        ]);
    }

    /**
     * Placeholder während des Lazy Loading
     */
    public function placeholder(): View
    {
        return view('livewire.placeholders.employee.family.child-table');
    }
}
