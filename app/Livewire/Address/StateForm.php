<?php

namespace App\Livewire\Address;

use App\Livewire\Address\Helper\ValidateStateForm;
use App\Traits\Table\WithPerPagePagination;
use Flux\Flux;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Address\State;
use Illuminate\Contracts\View\View;

class StateForm extends Component
{
    use ValidateStateForm, WithPerPagePagination;

    public array $countries = [];

    public ?int $stateId = null;
    public ?string $name = null;
    public ?string $code = null; // Falls benötigt (hier verwenden wir z.B. einen Dummy-Wert)
    public ?int $selectedCountry = null;  // Die aktuell ausgewählte Country-ID
    public bool $editing = false;

    /**
     * Der Parameter $countries wird hier vom Parent übergeben.
     */
    public function mount(array $countries): void
    {
        $this->countries = $countries;
    }

    /**
     * Speichert oder aktualisiert einen State.
     */


    public function saveState(): void
    {
        try {
            $this->validate();

            $code = 'PUP'; // Dummy Code

            if ($this->editing && $this->stateId) {
                $state = State::where('created_by', Auth::id())
                    ->findOrFail($this->stateId);

                $state->update([
                    'name'       => $this->name,
                    'code'       => $code,
                    'country_id' => $this->selectedCountry,
                ]);

                Flux::toast(
                    text: __('State updated successfully.'),
                    heading: __('Success.'),
                    variant: 'success'
                );
            } else {
                State::create([
                    'name'       => $this->name,
                    'code'       => $code,
                    'country_id' => $this->selectedCountry,
                ]);

                Flux::toast(
                    text: __('State created successfully.'),
                    heading: __('Success.'),
                    variant: 'success'
                );
            }

            $this->dispatch('refresh-states-all', ['modifiedCountry' => $this->selectedCountry]);

        } catch (\Throwable $e) {
            // Wenn es sich um eine ValidationException handelt, diese erneut werfen
            if ($e instanceof ValidationException) {
                throw $e;
            }

            // Andernfalls generische Fehlermeldung anzeigen
            Flux::toast(
                text: __('An error occurred while saving the state.'),
                heading: __('Error.'),
                variant: 'danger'
            );
        }
        $this->finish();
    }



    /**
     * Lädt einen bestehenden State zur Bearbeitung.
     */
    public function editState(int $id): void
    {
        try {
            $state = State::where('created_by', Auth::id())
                ->findOrFail($id);

//            $this->authorize('update-state', $state);

            $this->stateId    = $state->id;
            $this->name       = $state->name;
            $this->code       = $state->code;
            $this->selectedCountry = $state->country_id;

            $this->editing = true;

        } catch (\Throwable $e) {
            Flux::toast(
                text: __('You can not editing the state.'),
                heading: __('Error.'),
                variant: 'danger'
            );
        }
    }


    /**
     * Löscht einen State.
     */
    public function deleteState(int $id): void
    {
        try {
            $state = State::where('created_by', Auth::id())
                ->findOrFail($id);

            // Hole den Country-Wert direkt aus dem gelöschten State
            $modifiedCountry = $state->country_id;

            $state->delete();

            $this->dispatch('refresh-states-all', ['modifiedCountry' => $modifiedCountry]);

            Flux::toast(
                text: __('State deleted successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );
            $this->finish();

        } catch (\Throwable $e) {
            Flux::toast(
                text: __('You can not delete this state.'),
                heading: __('Error.'),
                variant: 'danger'
            );
        }
    }


    /**
     * Setzt das Formular zurück.
     */
    public function finish(): void
    {
        $this->modal('create-state')->close();

        $this->reset([
            'stateId', 'name', 'selectedCountry', 'editing'
        ]);
    }

    /**
     * Rendert die Komponente.
     */
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
