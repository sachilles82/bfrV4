<?php

namespace App\Livewire\Address;

use App\Livewire\Address\Helper\ValidateAddressable;
use App\Models\Address\City;
use App\Models\Address\State;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Select extends Component
{
    use ValidateAddressable;

    public Model $addressable;
    public array $countries = [];
    public array $states = [];
    public array $cities = [];

    public ?int $selectedCountry = null;
    public ?int $selectedState = null;
    public ?int $selectedCity = null;
    public string $street_number = '';

    public function mount(Model $addressable, array $countries): void
    {
        // Stelle sicher, dass die Adresse bereits eager geladen ist
        $this->addressable = $addressable->loadMissing('address');
        $this->countries = $countries;
        $this->states = [];
        $this->cities = [];

        if ($address = $this->addressable->address) {
            $this->selectedCountry = $address->country_id ?? null;
            $this->selectedState   = $address->state_id ?? null;
            $this->selectedCity    = $address->city_id ?? null;
            $this->street_number   = $address->street_number ?? '';
        } else {
            // Fallback: Wähle das erste Land aus
            $this->selectedCountry = $countries[0]['id'] ?? null;
        }

        if ($this->selectedCountry) {
            $this->loadStates();
            if ($this->selectedState) {
                $this->loadCities();
            }
        }
    }

    public function loadStates(): void
    {
        // Lade States für das ausgewählte Land mit selektiven Feldern
        $this->states = State::select('id', 'name', 'code', 'country_id')
            ->where('country_id', $this->selectedCountry)
            ->orderBy('id')
            ->get()
            ->toArray();
    }

    public function loadCities(): void
    {
        if ($this->selectedState) {
            $this->cities = City::select('id', 'name', 'state_id')
                ->where('state_id', $this->selectedState)
                ->orderBy('id')
                ->get()
                ->toArray();
        } else {
            $this->cities = [];
        }
    }

    public function updatedSelectedCountry(): void
    {
        $this->selectedState = null;
        $this->selectedCity  = null;
        $this->states        = [];
        $this->cities        = [];
        $this->loadStates();
    }

    public function updatedSelectedState(): void
    {
        $this->selectedCity = null;
        $this->loadCities();
    }

    public function save(): void
    {
        $this->authorize('update', $this->addressable);
        $this->validate();

        try {
            $address = $this->addressable->address()->updateOrCreate([], [
                'street_number' => $this->street_number,
                'country_id'    => $this->selectedCountry,
                'state_id'      => $this->selectedState,
                'city_id'       => $this->selectedCity,
            ]);

            Flux::toast(
                text: __('Address updated successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

            $this->dispatch('address-updated');
        } catch (\Exception $e) {
            Flux::toast(
                text: __('An error occurred while saving the address.'),
                heading: __('Error'),
                variant: 'error'
            );
        }
    }

    public function render(): View
    {
        return view('livewire.address.select', [
            'countries' => $this->countries,
            'states'    => $this->states,
            'cities'    => $this->cities,
        ]);
    }
}
