<?php

namespace App\Livewire\Address;

use App\Livewire\Address\Helper\ValidateCityForm;
use App\Models\Address\City;
use App\Models\Address\State;
use App\Traits\Table\WithPerPagePagination;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;

class CityForm extends Component
{
    use ValidateCityForm, WithPerPagePagination;

    public array $countries = [];

    public array $states = [];

    public ?int $selectedCountry = null;

    public ?int $selectedState = null;

    public ?int $cityId = null;

    public string $name = '';

    public bool $editing = false;

    public function mount(array $countries): void
    {
        $this->countries = $countries;
        $this->selectedCountry = $countries[0]['id'] ?? null;
        $this->loadStates();
    }

    protected function getTeamId(): int
    {
        return Auth::user()->currentTeam?->id ?? 0;
    }

    #[On('create-city')]
    public function loadStates(): void
    {
        if ($this->selectedCountry) {
            $teamId = $this->getTeamId();

            $this->states = State::select(['id', 'name', 'code', 'country_id'])
                ->where('country_id', $this->selectedCountry)
                ->where(function ($query) use ($teamId) {
                    $query->where('team_id', $teamId)
                        ->orWhere('created_by', 1);
                })
                ->orderBy('id')
                ->get()
                ->toArray();
        }
    }

    public function updatedSelectedCountry(): void
    {
        $this->selectedState = null;
        $this->states = [];
        $this->loadStates();
    }

    public function saveCity(): void
    {
        try {

            $this->validate();

            if ($this->editing && $this->cityId) {
                $city = City::where('created_by', Auth::id())
                    ->findOrFail($this->cityId);

                $city->update([
                    'name' => $this->name,
                    'state_id' => $this->selectedState,
                ]);

                Flux::toast(
                    text: __('City updated successfully.'),
                    heading: __('Success.'),
                    variant: 'success'
                );

            } else {
                City::create([
                    'name' => $this->name,
                    'state_id' => $this->selectedState,
                ]);

                Flux::toast(
                    text: __('City created successfully.'),
                    heading: __('Success.'),
                    variant: 'success'
                );

            }

            $this->dispatch('refresh-states-cities', ['modifiedState' => $this->selectedState]);

        } catch (\Throwable $e) {

            if ($e instanceof ValidationException) {
                throw $e;
            }

            Flux::toast(
                text: __('An error occurred while saving the City.'),
                heading: __('Error.'),
                variant: 'error'
            );

        }

        $this->finish();
    }

    public function editCity(int $id): void
    {
        try {
            $city = City::where('created_by', Auth::id())
                ->with('state')
                ->findOrFail($id);

            $this->cityId = $city->id;
            $this->name = $city->name;
            $this->selectedState = $city->state_id;
            $this->selectedCountry = $city->state->country_id;

            $this->loadStates();
            $this->editing = true;

        } catch (\Throwable $e) {

            Flux::toast(
                text: __('You cannot edit the city.'),
                heading: __('Error.'),
                variant: 'danger'
            );

        }
    }

    public function deleteCity(int $id): void
    {
        try {

            $city = City::where('created_by', Auth::id())
                ->findOrFail($id);

            $modifiedState = $city->state_id;
            $city->delete();
            $this->dispatch('refresh-states-cities', ['modifiedState' => $modifiedState]);

            Flux::toast(
                text: __('City deleted successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

            $this->finish();

        } catch (\Throwable $e) {

            Flux::toast(
                text: __('You cannot delete the city.'),
                heading: __('Error.'),
                variant: 'danger'
            );

        }
    }

    public function finish(): void
    {
        $this->modal('create-city')
            ->close();

        $this->reset([
            'cityId', 'name', 'selectedCountry', 'selectedState', 'editing',
        ]);

        $this->resetValidation();
    }

    public function render(): View
    {
        $query = City::where('created_by', Auth::id())
            ->with('state.country')
            ->orderBy('id');

        $cities = $this->applySimplePagination($query);

        return view('livewire.address.city-form', [
            'cities' => $cities,
        ]);
    }
}
