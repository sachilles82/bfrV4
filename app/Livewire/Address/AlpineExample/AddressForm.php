<?php

namespace App\Livewire\Address\AlpineExample;

use App\Livewire\Address\Helper\ValidateAddressForm;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Livewire\Component;
use Spatie\ResponseCache\ResponseCache;

class AddressForm extends Component
{
    use ValidateAddressForm;

    public Model $addressable;

    public Collection $countries;

    public Collection $states;

    public Collection $cities;

    public ?int $country_id = null;

    public ?int $state_id = null;

    public ?int $city_id = null;

    public string $street_number = '';

    public function mount(Model $addressable, $countries, $states, $cities): void
    {
        $this->addressable = $addressable;
        $this->countries = $countries;
        $this->states = $states;
        $this->cities = $cities;

        if ($address = $this->addressable->address) {
            $this->country_id = $address->country_id ?? null;
            $this->state_id = $address->state_id ?? null;
            $this->city_id = $address->city_id ?? null;
            $this->street_number = $address->street_number ?? '';
        }
    }

    public function save(): void
    {
        // Autorisierung
        $this->authorize('update', $this->addressable);

        $this->validate();

        $this->addressable->address()->updateOrCreate([], [
            'street_number' => $this->street_number,
            'country_id' => $this->country_id,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
        ]);

        Flux::toast(
            text: __('Address updated successfully.'),
            heading: __('Success.'),
            variant: 'success'
        );

        app(ResponseCache::class)->clear();
        $this->dispatch('address-updated');
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
