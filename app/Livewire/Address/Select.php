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

    /** Vom Parent übergebene Länder-Daten als Array. */
    public array $countries = [];

    // Dynamisch geladene Daten als Arrays
    public array $states = [];
    public array $cities = [];

    // Ausgewählte Werte
    public ?int $selectedCountry = null;
    public ?int $selectedState = null;
    public ?int $selectedCity = null;
    public string $street_number = '';


    public function mount(Model $addressable, array $countries): void
    {
        $this->addressable = $addressable;
        $this->countries   = $countries;
        $this->states      = [];
        $this->cities      = [];

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

    /**
     * Speichert die Adresse.
     */
    public function save(): void
    {
        // Autorisierung prüfen
        $this->authorize('update', $this->addressable);

        // Validierung anhand der in $rules definierten Regeln
        $this->validate();

        try {
            // Mapping der Werte zu den DB-Spalten
            $this->addressable->address()->updateOrCreate([], [
                'street_number' => $this->street_number,
                'country_id'    => $this->selectedCountry,
                'state_id'      => $this->selectedState,
                'city_id'       => $this->selectedCity,
            ]);

            // Erfolgsmeldung
            Flux::toast(
                text: __('Address updated successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

            $this->dispatch('address-updated');
        } catch (\Exception $e) {
            // Fehlerbehandlung: Fehler anzeigen und ggf. loggen
            Flux::toast(
                text: __('An error occurred while saving the address.'),
                heading: __('Error'),
                variant: 'error'
            );
            // Optional: \Log::error($e);
        }
    }

    /**
     * Lädt die States des aktuell ausgewählten Landes.
     */
    public function loadStates(): void
    {
        $teamId = Auth::user()->currentTeam?->id ?? 0;
        if ($this->selectedCountry) {
            $cacheKey = sprintf('state-country-%d-team-%d', $this->selectedCountry, $teamId);

            $this->states = Cache::remember($cacheKey, now()->addWeek(), function () use ($teamId) {
                return State::select(['id', 'name', 'code', 'country_id'])
                    ->where('country_id', $this->selectedCountry)
                    ->where(function ($query) use ($teamId) {
                        $query->where('team_id', $teamId)
                            ->orWhere('created_by', 1); // Hinweis: Passen, falls nötig, über Konfiguration an
                    })
                    ->orderBy('id')
                    ->get()
                    ->toArray();
            });
        }
    }

    /**
     * Lädt die Cities des aktuell ausgewählten Staates.
     */
    public function loadCities(): void
    {
        $teamId = Auth::user()->currentTeam?->id ?? 0;
        if ($this->selectedState) {
            $cacheKey = sprintf('cities-state-%d-team-%d', $this->selectedState, $teamId);

            $this->cities = Cache::remember($cacheKey, now()->addWeek(), function () use ($teamId) {
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

    /**
     * Wird automatisch aufgerufen, wenn sich der Wert von selectedCountry ändert.
     */
    public function updatedSelectedCountry(): void
    {
        $this->selectedState = null;
        $this->selectedCity  = null;
        $this->states        = [];
        $this->cities        = [];
        $this->loadStates();
    }

    /**
     * Wird automatisch aufgerufen, wenn sich der Wert von selectedState ändert.
     */
    public function updatedSelectedState(): void
    {
        $this->selectedCity = null;
        $this->loadCities();
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
