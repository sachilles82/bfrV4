<?php

namespace App\Livewire\Alem\Employee\Profile\Family;

use App\Models\Alem\Child;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ShowChild extends Component
{
    #[Locked]
    public ?int $userId = null;

    public function mount(int $userId): void
    {
        $this->userId = $userId;
    }

    public function delete($childId)
    {
        $child = Child::find($childId);

        // Authorization...

        $child->delete();

//        sleep(1);
    }

    public function render()
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
            ->get(); // <-- HIER! Du musst get() aufrufen!

        return view('livewire.alem.employee.profile.family.show-child', [
            'children' => $children
        ]);
    }
}
