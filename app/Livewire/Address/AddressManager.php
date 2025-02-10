<?php

namespace App\Livewire\Address;

use App\Models\Address\Country;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class AddressManager extends Component
{
    public $addressable;

    public array $countries = [];

    public function mount($addressable): void
    {
        $this->addressable = $addressable;

        $countries = Cache::remember('all-countries', now()->addWeek(), function () {
            return Country::select(['id', 'name', 'code'])
                ->orderBy('id')
                ->get()
                ->toArray();
        });

        $this->countries = $countries;
    }

    public function render(): View
    {
        return view('livewire.address.address-manager');
    }
}
