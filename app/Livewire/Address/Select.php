<?php

namespace App\Livewire\Address;

use App\Livewire\Address\Helper\ValidateAddressable;
use App\Models\Address\City;
use App\Models\Address\State;
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

    /** Vom Parent übergebene Daten als Array.*/
    public array $countries = [];

    // Dynamisch geladene Daten als Arrays.
    public array $states = [];
    public array $cities = [];
    protected array $countriesData = [];

    // Ausgewählte Werte
    public ?int $selectedCountry = null;
    public ?string $selectedState = null;
    public ?int $selectedCity = null;

    public string $street_number = '';

    public function mount(Model $addressable, array $countries): void
    {
        $this->addressable   = $addressable;
        $this->countriesData = $countries;
        $this->states        = [];
        $this->cities        = [];

        // Falls bereits eine Adresse vorhanden ist, lade die gespeicherten Werte
        if ($address = $this->addressable->address) {
            $this->selectedCountry = $address->country_id ?? null;
            $this->selectedState   = $address->state_id ?? null;
            $this->selectedCity    = $address->city_id ?? null;
            $this->street_number   = $address->street_number ?? '';
        } else {
            // Falls keine Adresse existiert, setze das Land auf das erste Element
            $this->selectedCountry = $countries[0]['id'] ?? null;
        }

        // Lade States für das ausgewählte Land
        if ($this->selectedCountry) {
            $this->loadStates();

            // Falls bereits ein State ausgewählt wurde, auch die Cities laden
            if ($this->selectedState) {
                $this->loadCities();
            }
        }
    }


    public function save(): void
    {
        // Autorisierung prüfen
        $this->authorize('update', $this->addressable);

        // Validierung (nutzt jetzt die angepassten Regeln)
        $this->validate();

        // Mapping der Werte zu den DB-Spalten
        $this->addressable->address()->updateOrCreate([], [
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
    }


    /**
     * Lädt die States des aktuell ausgewählten Landes als Array.
     */
    protected function loadStates(): void
    {
        $cacheKey = 'states-country-' . $this->selectedCountry . '-user-' . Auth::id();

        $states = Cache::remember($cacheKey, now()->addWeek(), function () {
            return State::select(['id', 'name', 'code', 'country_id'])
                ->where('country_id', $this->selectedCountry)
                ->where(function ($query) {
                    $query->where('team_id', optional(Auth::user()->currentTeam)->id)
                        ->orWhere('created_by', 1);
                })
                ->orderBy('id')
                ->get()
                ->toArray(); // Rückgabe als Array
        });
        $this->states = is_array($states) ? $states : $states->toArray();
    }

    /**
     * Lädt die Cities des aktuell ausgewählten Staates als Array.
     */
    protected function loadCities(): void
    {
        if (!is_null($this->selectedState)) {
            $cacheKey = 'cities-state-' . $this->selectedState . '-user-' . Auth::id();

            $cities = Cache::remember($cacheKey, now()->addWeek(), function () {
                return City::select(['id', 'name', 'state_id'])
                    ->where('state_id', $this->selectedState)
                    ->where(function ($query) {
                        $query->where('team_id', optional(Auth::user()->currentTeam)->id)
                            ->orWhere('created_by', 1);
                    })
                    ->orderBy('id')
                    ->get()
                    ->toArray();
            });
            $this->cities = is_array($cities) ? $cities : $cities->toArray();
        } else {
            $this->cities = [];
        }
    }

    /**
     * Wird automatisch aufgerufen, wenn sich der Wert von $selectedCountry ändert.
     */
    public function updatedSelectedCountry(): void
    {
        $this->selectedState = null;
        $this->selectedCity  = null;

        $this->states = [];
        $this->cities = [];

        $this->loadStates();
    }

    /**
     * Wird automatisch aufgerufen, wenn sich der Wert von $selectedState ändert.
     */
    public function updatedSelectedState(): void
    {
        $this->selectedCity = null;
        $this->loadCities();
    }

    public function render(): View
    {
        return view('livewire.address.select', [
            'countries' => $this->countriesData,
            'states' => $this->states,
            'cities' => $this->cities,
        ]);
    }
}
