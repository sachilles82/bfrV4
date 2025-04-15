<?php

namespace App\Livewire\Address;

use App\Livewire\Address\Helper\ValidateAddressable;
use App\Services\Address\AddressCache;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Livewire\Component;

class AddressForm extends Component
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
        $cacheKey = sprintf('addressable-%s', $addressable->getKey());

        $this->addressable = Cache::rememberForever($cacheKey, function () use ($addressable) {
            return $addressable->loadMissing('address');
        });

        $this->countries = $countries;
        $this->states = [];
        $this->cities = [];

        if ($address = $this->addressable->address) {
            $this->selectedCountry = $address->country_id ?? null;
            $this->selectedState = $address->state_id ?? null;
            $this->selectedCity = $address->city_id ?? null;
            $this->street_number = $address->street_number ?? '';
        } else {
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
        $teamId = Auth::user()->currentTeam?->id ?? 0;

        if ($this->selectedCountry) {
            $this->states = AddressCache::getStates($this->selectedCountry, $teamId);
        }
    }

    public function loadCities(): void
    {
        $teamId = Auth::user()->currentTeam?->id ?? 0;
        if ($this->selectedState) {
            $this->cities = AddressCache::getCities($this->selectedState, $teamId);
        } else {
            $this->cities = [];
        }
    }

    public function updatedSelectedCountry(): void
    {
        $this->selectedState = null;
        $this->selectedCity = null;
        $this->states = [];
        $this->cities = [];
        $this->loadStates();
    }

    public function updatedSelectedState(): void
    {
        $this->selectedCity = null;
        $this->loadCities();
    }

    public function updateAddress(): void
    {
        //        $this->authorize('update', $this->addressable);

        $this->validate();

        try {
            $this->addressable->address()->updateOrCreate([], [
                'street_number' => $this->street_number,
                'country_id' => $this->selectedCountry,
                'state_id' => $this->selectedState,
                'city_id' => $this->selectedCity,
            ]);

            $cacheKey = sprintf('addressable-%s', $this->addressable->getKey());
            Cache::forget($cacheKey);
            $this->addressable = Cache::rememberForever($cacheKey, function () {
                return $this->addressable->fresh('address');
            });

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

    #[On(['refresh-states-all', 'states-updated'])]
    public function loadStatesFromDatabase($eventData = null): void
    {
        $teamId = Auth::user()->currentTeam?->id ?? 0;

        if (is_array($eventData) && isset($eventData['modifiedCountry'])) {
            $modifiedCountry = $eventData['modifiedCountry'];
            AddressCache::forgetStates($modifiedCountry, $teamId);
        }

        $this->loadStates();
    }

    #[On('refresh-states-cities')]
    public function loadCitiesFromDatabase($eventData = null): void
    {
        $teamId = Auth::user()->currentTeam?->id ?? 0;

        if (is_array($eventData) && isset($eventData['modifiedState'])) {
            $modifiedState = $eventData['modifiedState'];
            AddressCache::forgetCities($modifiedState, $teamId);
        }

        $this->loadCities();
    }

    public function render(): View
    {
        return view('livewire.address.address-form', [
            'countries' => $this->countries,
            'states' => $this->states,
            'cities' => $this->cities,
        ]);
    }
}
