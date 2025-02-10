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

//        $this->countries = Cache::remember('countries-all', now()->addWeek(), function () {
//            return Country::select(['id', 'name', 'code'])
//                ->orderBy('id')
//                ->get()
//                ->toArray();
//        });

        // Lade alle Länder mit den benötigten Feldern
        $this->countries = Country::select('id', 'name', 'code')
            ->orderBy('id')
            ->get()
            ->toArray();
    }

    public function render(): View
    {
        return view('livewire.address.address-manager');
    }
}
