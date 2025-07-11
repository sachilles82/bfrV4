<?php
//
//namespace App\Livewire\Alem\Employee\Profile\Family;
//
//use App\Enums\User\Gender;
//use App\Livewire\Alem\Employee\Profile\Family\Helper\HandleCatchError;
//use App\Livewire\Alem\Employee\Profile\Family\Helper\ValidateChild;
//use App\Models\Alem\Child;
//use App\Traits\Enum\GenderOptions;
//use Flux\Flux;
//use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
//use Illuminate\Support\Facades\DB;
//use Illuminate\View\View;
//use Livewire\Attributes\Lazy;
//use Livewire\Attributes\Locked;
//use Livewire\Attributes\On;
//use Livewire\Component;
//
//#[Lazy(isolate: false)]
//class CreateChild extends Component
//{
//    use AuthorizesRequests;
//    use ValidateChild, HandleCatchError;
//    use GenderOptions;
//
//    // Parent User ID - wird beim Event übergeben
//    #[Locked]
//    public ?int $userId = null;
//
//    // Child Form Fields
//    public ?string $name = null;
//    public ?string $gender = null;
//    public ?string $birthdate = null;
//    public ?string $ahv_number = null;
//    public ?string $valid_until = null;
//
//    #[On('create-child-modal')]
//    public function openCreateChildModal(int $userId): void
//    {
//        // Setze userId beim ersten Aufruf
//        if ($this->userId === null) {
//            $this->userId = $userId;
//        }
//
//        // $this->authorize('create', [Child::class, $this->userId]);
//
//        $this->resetFormInputs();
//
//        // Setze Standardwerte
//        $this->gender = Gender::Male->value;
//
//        // Öffne Flux Modal
//        $this->modal('create-child')->show();
//    }
//
//    /**
//     * Speichert ein neues Kind
//     */
//    public function saveChild(): void
//    {
//        $this->validate();
//
//        try {
//            DB::transaction(function () {
//                // Berechne valid_until wenn birthdate gesetzt ist
//                if ($this->birthdate && !$this->valid_until) {
//                    $birthdate = \Carbon\Carbon::parse($this->birthdate);
//                    $this->valid_until = $birthdate->copy()->addYears(18)->format('Y-m-d');
//                }
//
//                Child::create([
//                    'user_id' => $this->userId,
//                    'name' => $this->name,
//                    'gender' => $this->gender,
//                    'birthdate' => $this->birthdate,
//                    'ahv_number' => $this->ahv_number,
//                    'valid_until' => $this->valid_until,
//                ]);
//            });
//
//            $this->closeCreateChildModal();
//            $this->dispatch('child-created');
//
//            Flux::toast(
//                text: __('Child added successfully.'),
//                heading: __('Success'),
//                variant: 'success'
//            );
//
//        } catch (\Throwable $e) {
//            $this->handleSavingError($e);
//        }
//    }
//
//    /**
//     * Schließt das Modal und bereinigt die Daten
//     */
//    public function closeCreateChildModal(): void
//    {
//        $this->modal('create-child')->close();
//
//        $this->js("
//            setTimeout(() => {
//                \$wire.resetFormInputs();
//            }, 300);
//        ");
//    }
//
//    public function resetFormInputs(): void
//    {
//        $this->resetErrorBag();
//        $this->reset([
//            'name', 'gender', 'birthdate',
//            'ahv_number', 'valid_until'
//        ]);
//    }
//
//    public function render(): View
//    {
//        return view('livewire.alem.employee.profile.family.create-child');
//    }
//}


namespace App\Livewire\Alem\Employee\Profile\Family;

use App\Enums\User\Gender;
use App\Livewire\Alem\Employee\Profile\Family\Helper\HandleCatchError;
use App\Livewire\Alem\Employee\Profile\Family\Helper\ValidateChild;
use App\Models\Alem\Child;
use App\Traits\Enum\GenderOptions;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class CreateChild extends Component
{
    use AuthorizesRequests;
    use ValidateChild, HandleCatchError;
    use GenderOptions;

    // Parent User ID
    #[Locked]
    public int $userId;

    // Modal State
    public bool $show = false;

    // Child Form Fields
    public ?string $name = null;
    public ?string $gender = null;
    public ?string $birthdate = null;
    public ?string $ahv_number = null;
    public ?string $valid_until = null;

    public function mount(int $userId): void
    {
        $this->userId = $userId;
        // Setze Default Gender
        $this->gender = Gender::Male->value;
    }

    /**
     * Öffnet das Create Modal
     */
    public function openModal(): void
    {
        $this->resetFormInputs();
        $this->gender = Gender::Male->value;
        $this->show = true;
    }

    /**
     * Speichert ein neues Kind
     */
    public function add(): void
    {
        $this->validate();

        try {
            DB::transaction(function () {
                // Berechne valid_until wenn birthdate gesetzt ist
                if ($this->birthdate && !$this->valid_until) {
                    $birthdate = \Carbon\Carbon::parse($this->birthdate);
                    $this->valid_until = $birthdate->copy()->addYears(18)->format('Y-m-d');
                }

                Child::create([
                    'user_id' => $this->userId,
                    'name' => $this->name,
                    'gender' => $this->gender,
                    'birthdate' => $this->birthdate,
                    'ahv_number' => $this->ahv_number,
                    'valid_until' => $this->valid_until,
                ]);
            });

            $this->reset('show');
            $this->dispatch('added');

            Flux::toast(
                text: __('Child added successfully.'),
                heading: __('Success'),
                variant: 'success'
            );

        } catch (\Throwable $e) {
            $this->handleSavingError($e);
        }
    }

    public function resetFormInputs(): void
    {
        $this->resetErrorBag();
        $this->reset([
            'name', 'gender', 'birthdate',
            'ahv_number', 'valid_until'
        ]);
    }

    public function render(): View
    {
        return view('livewire.alem.employee.profile.family.create-child');
    }
}
