<?php

namespace App\Livewire\Address;

use App\Livewire\Address\Helper\LoadData;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AddressManager extends Component
{
    use LoadData;

    public $addressable;

    public $countries;
    public $states;
    public $cities;

    public function mount($addressable): void
    {
        $this->addressable = $addressable;

        $this->loadCountries();

        $this->loadStates();

        $this->loadCities();

    }

    public function render():View
    {
        return view('livewire.address.address-manager');
    }
}
