<?php

namespace App\Livewire\Address;

use App\Models\Address\City;
use App\Models\Address\State;
use App\Livewire\Address\Helper\ValidateAddressable;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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

    protected const CACHE_TTL = 604800; // 1 Woche in Sekunden

    /**
     * Der Parameter $countries wird hier übergeben, um die Länderliste zu erhalten.
     */
    public function mount(Model $addressable, array $countries): void
    {
        $cacheKey = sprintf('addressable-%s', $addressable->getKey());
        // Das addressable Model wird dauerhaft gecached, da sich diese Daten selten ändern.
        $this->addressable = Cache::rememberForever($cacheKey, function () use ($addressable) {
            return $addressable->loadMissing('address');
        });

        $this->countries = $countries;
        $this->states = [];
        $this->cities = [];

        if ($address = $this->addressable->address) {
            $this->selectedCountry = $address->country_id ?? null;
            $this->selectedState   = $address->state_id ?? null;
            $this->selectedCity    = $address->city_id ?? null;
            $this->street_number   = $address->street_number ?? '';
        } else {
            // Fallback: Wähle das erste Land, falls keine Adresse vorhanden ist.
            $this->selectedCountry = $countries[0]['id'] ?? null;
        }

        if ($this->selectedCountry) {
            $this->loadStates();
            if ($this->selectedState) {
                $this->loadCities();
            }
        }
    }

    /**
     * Eine Hilfsmethode, die einen Cache-Eintrag mit einer definierten TTL erstellt.
     */
    protected function cacheQuery(string $cacheKey, \Closure $callback)
    {
        return Cache::remember($cacheKey, now()->addSeconds(self::CACHE_TTL), $callback);
    }

    public function loadStates(): void
    {
        $teamId = Auth::user()->currentTeam?->id ?? 0;
        if ($this->selectedCountry) {
            $cacheKey = sprintf('state-country-%d-team-%d', $this->selectedCountry, $teamId);
            $this->states = $this->cacheQuery($cacheKey, function () use ($teamId) {
                return State::select(['id', 'name', 'code', 'country_id'])
                    ->where('country_id', $this->selectedCountry)
                    ->where(function ($query) use ($teamId) {
                        $query->where('team_id', $teamId)
                            ->orWhere('created_by', 1);
                    })
                    ->orderBy('id')
                    ->get()
                    ->toArray();
            });
        }
    }

    public function loadCities(): void
    {
        $teamId = Auth::user()->currentTeam?->id ?? 0;
        if ($this->selectedState) {
            $cacheKey = sprintf('cities-state-%d-team-%d', $this->selectedState, $teamId);
            $this->cities = $this->cacheQuery($cacheKey, function () use ($teamId) {
                return City::select(['id', 'name', 'state_id'])
                    ->where('state_id', $this->selectedState)
                    ->where(function ($query) use ($teamId) {
                        $query->where('team_id', $teamId)
                            ->orWhere('created_by', 1);
                    })
                    ->orderBy('id')
                    ->get()
                    ->toArray();
            });
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

    public function updateAddress(): void
    {
        $this->authorize('update', $this->addressable);
        $this->validate();

        try {
            $this->addressable->address()->updateOrCreate([], [
                'street_number' => $this->street_number,
                'country_id'    => $this->selectedCountry,
                'state_id'      => $this->selectedState,
                'city_id'       => $this->selectedCity,
            ]);

            // Den Cache invalidieren
            $cacheKey = sprintf('addressable-%s', $this->addressable->getKey());
            Cache::forget($cacheKey);

            // Setze die Property neu, sodass der Cache-Aufruf in mount() (oder einer Methode) wieder ausgeführt wird.
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

    public function render(): View
    {
        return view('livewire.address.select', [
            'countries' => $this->countries,
            'states' => $this->states,
            'cities' => $this->cities,
        ]);
    }
}
