<?php

namespace App\Livewire\Address;

use App\Models\Address\City;
use App\Models\Address\Country;
use App\Models\Address\State;
use Illuminate\Support\Collection;
use Livewire\Component;

class Select extends Component
{
    public Collection $countries;
    public Collection $states;
    public Collection $cities;

    public ?int $selectedCountry = null;
//    public ?int $selectedState = null;
    public ?string $selectedState = null;
    public ?int $selectedCity = null;

    public ?int $country = null;
    public ?int $state = null;
    public ?int $city = null;
    public string $street_number = '';


    public function mount(): void
    {
        $this->countries = Country::all();
        $this->states = collect();
        $this->cities = collect();
    }

    public function updatedSelectedCountry(): void
    {
        $this->states = State::where('country_id', $this->selectedCountry)->get();
        $this->selectedState = null;
    }

    public function updatedSelectedState(): void
    {
        if (! is_null($this->selectedState)) {
            $this->cities = City::where('state_id', $this->selectedState)->get();
        }
    }

    public function render()
    {
        return view('livewire.address.select');
    }
}
