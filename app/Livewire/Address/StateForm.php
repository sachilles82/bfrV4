<?php

namespace App\Livewire\Address;

use App\Livewire\Address\Helper\ValidateStateForm;
use App\Models\Address\State;
use App\Traits\Table\WithPerPagePagination;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;

class StateForm extends Component
{
    use ValidateStateForm, WithPerPagePagination;

    public array $countries = [];

    #[Locked]
    public ?int $stateId = null;

    public ?string $name = null;

    public ?string $code = null; // Dummy-Wert, falls benötigt

    public ?int $selectedCountry = null;

    public bool $editing = false;

    public function mount(array $countries): void
    {
        $this->countries = $countries;
    }

    public function saveState(): void
    {
        try {
            $this->validate();
            $code = 'PUP'; // Dummy Code

            if ($this->editing && $this->stateId) {
                $state = State::where('created_by', Auth::id())
                    ->findOrFail($this->stateId);

                $this->authorize('update', $state);

                $state->update([
                    'name' => $this->name,
                    'code' => $code,
                    'country_id' => $this->selectedCountry,
                ]);

                Flux::toast(
                    text: __('State updated successfully.'),
                    heading: __('Success.'),
                    variant: 'success'
                );
            } else {

                $this->authorize('create', State::class);

                State::create([
                    'name' => $this->name,
                    'code' => $code,
                    'country_id' => $this->selectedCountry,
                ]);

                Flux::toast(
                    text: __('State created successfully.'),
                    heading: __('Success.'),
                    variant: 'success'
                );
            }

            $this->dispatch('states-updated', ['modifiedCountry' => $this->selectedCountry]);

        } catch (\Throwable $e) {
            if ($e instanceof ValidationException) {
                throw $e;
            }

            Flux::toast(
                text: __('An error occurred while saving the state.'),
                heading: __('Error.'),
                variant: 'danger'
            );
        }

        $this->finish();
    }

    public function editState(int $id): void
    {
        try {
            $state = State::where('created_by', Auth::id())
                ->findOrFail($id);

            $this->stateId = $state->id;
            $this->name = $state->name;
            $this->code = $state->code;
            $this->selectedCountry = $state->country_id;
            $this->editing = true;

        } catch (\Throwable $e) {

            Flux::toast(
                text: __('You cannot edit the state.'),
                heading: __('Error.'),
                variant: 'danger'
            );

        }
    }

    public function deleteState(int $id): void
    {
        try {
            $state = State::where('created_by', Auth::id())
                ->findOrFail($id);

            $this->authorize('delete', $state);

            $modifiedCountry = $state->country_id;

            $state->delete();
            $this->dispatch('states-updated', ['modifiedCountry' => $modifiedCountry]);

            Flux::toast(
                text: __('State deleted successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

            $this->finish();

        } catch (\Throwable $e) {

            Flux::toast(
                text: __('You cannot delete this state.'),
                heading: __('Error.'),
                variant: 'danger'
            );

        }
    }

    public function finish(): void
    {
        $this->modal('create-state')
            ->close();

        $this->reset([
            'stateId', 'name', 'selectedCountry', 'editing',
        ]);

        $this->resetValidation();
    }

    public function render(): View
    {
        $query = State::where('created_by', Auth::id())
            ->with('country:id,code,name')
            ->orderBy('id');

        $states = $this->applySimplePagination($query);

        return view('livewire.address.state-form', [
            'states' => $states,
        ]);
    }
}
