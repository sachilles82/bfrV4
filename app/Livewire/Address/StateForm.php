<?php

namespace App\Livewire\Address;

use App\Livewire\Address\Helper\ValidateStateForm;
use App\Models\Address\State;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;
use App\Traits\Table\WithPerPagePagination;
use Spatie\ResponseCache\ResponseCache;

class StateForm extends Component
{
    use ValidateStateForm, WithPerPagePagination;

    public Collection $countries;

    public ?int $country_id = null;

    #[Locked]
    public ?int $stateId = null;
    public ?string $name = null;
    public ?string $code = null;

    public bool $editing = false;

    /** Wir nehmen die vom AddressManager übergebenen Props entgegen:*/
    public function mount($countries): void
    {
        $this->countries = $countries;
    }

    public function saveState(): void
    {
        $this->validate();

        $code = 'PUP'; // Dummy Code. Flagge United States of PUP.

        if ($this->editing && $this->stateId) {

            $state = State::where('created_by', auth()->id())
                ->findOrFail($this->stateId);

            $state->update([
                'name' => $this->name,
                'code' => $code,
                'country_id' => $this->country_id,
            ]);

            Flux::toast(
                text: __('State updated successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

        } else {

            State::create([
                'name' => $this->name,
                'code' => $code,
                'country_id' => $this->country_id,
            ]);

            Flux::toast(
                text: __('State created successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );
        }

        $this->finish();
    }

    public function editState(int $id): void
    {
        $state = State::where('created_by', auth()->id())->findOrFail($id);

        $this->stateId = $state->id;
        $this->name = $state->name;
        $this->code = $state->code;
        $this->country_id = $state->country_id;

        $this->editing = true;
    }

    public function deleteState(int $id): void
    {
        // Autorisierung
//        $this->authorize('delete');
        $state = State::where('created_by', auth()->id())->findOrFail($id);

        $state->delete();

        Flux::toast(
            text: __('State deleted successfully.'),
            heading: __('Success.'),
            variant: 'success'
        );

        $this->finish();
    }

    public function finish(): void
    {
        $this->modal('create-state')->close();
        app(ResponseCache::class)->clear();

        $this->dispatch('update-address');
        $this->reset([
            'stateId', 'name', 'country_id', 'editing'
        ]);
    }

    public function render(): View
    {
        $query = State::where('created_by', Auth::id())
            ->with('country:id,code')
            ->orderBy('id');

        $states = $this->applySimplePagination($query);

        return view('livewire.address.state-form', [
            'states' => $states,
        ]);
    }
}
