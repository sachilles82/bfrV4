<?php

namespace App\Livewire\Address;

use App\Livewire\Address\Helper\ValidateCityForm;
use App\Models\Address\City;
use App\Models\Address\State;
use App\Traits\Table\WithPerPagePagination;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Spatie\ResponseCache\ResponseCache;

class CityForm extends Component
{
    use ValidateCityForm, WithPerPagePagination;

    /** Vom AddressManager übergebene Länder und States (als Collections oder Arrays) */
    public $countries;
    public $states;

    // Formulareingaben
    public ?int $country_id = null;
    public ?int $state_id = null;

    #[Locked]
    public ?int $cityId = null;
    public ?string $name = null;

    public bool $editing = false;

    /**
     * Beim Mount erhältst du die bereits gecachten Countries und States vom Parent.
     */
    public function mount($countries, $states): void
    {
        $this->countries = $countries;
        $this->states = $states;
    }

    /**
     * Wenn der Country-Wechsel erfolgt (wire:model="country_id"), wird diese Methode automatisch aufgerufen.
     * Hier wird die Liste der States (nur für das gewählte Land) neu geladen.
     */
    public function updatedCountryId($value): void
    {
        $teamId = Auth::user()->currentTeam?->id ?? 0;

        // Lade alle States, die zu dem ausgewählten Country gehören.
        // Hier werden nur die relevanten Spalten ausgewählt und nach id sortiert.
        $this->states = State::select(['id', 'name', 'code', 'country_id'])
            ->where('country_id', $value)
            ->where(function ($query) use ($teamId) {
                $query->where('team_id', $teamId)
                    ->orWhere('created_by', 1);
            })
            ->orderBy('id')
            ->get();

        // Da sich das States-Array ändert, setzen wir state_id zurück.
        $this->state_id = null;
    }

    /**
     * Speichert eine neue oder aktualisierte City.
     */
    public function saveCity(): void
    {
        $this->validate();

        if ($this->editing && $this->cityId) {
            $city = City::where('created_by', Auth::id())->findOrFail($this->cityId);
            $city->update([
                'name'     => $this->name,
                'state_id' => $this->state_id,
            ]);
            Flux::toast(
                text: __('City updated successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );
        } else {
            City::create([
                'name'       => $this->name,
                'state_id'   => $this->state_id,
                'created_by' => Auth::id(),
            ]);
            Flux::toast(
                text: __('City created successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );
        }

        $this->finish();
    }

    public function editCity(int $id): void
    {
        $city = City::where('created_by', Auth::id())->findOrFail($id);

        $this->cityId = $city->id;
        $this->name = $city->name;
        $this->state_id = $city->state_id;
        $this->country_id = $city->state?->country_id;

        $this->editing = true;
    }

    public function deleteCity(int $id): void
    {
        $city = City::where('created_by', Auth::id())->findOrFail($id);
        $city->delete();

        Flux::toast(
            text: __('City deleted successfully.'),
            heading: __('Success.'),
            variant: 'success'
        );

        $this->finish();
    }

    public function finish(): void
    {
        $this->modal('create-city')->close();
        app(ResponseCache::class)->clear();
        $this->dispatch('update-address');
        $this->reset([
            'cityId', 'name', 'country_id', 'state_id', 'editing'
        ]);
    }

    public function render(): View
    {
        $query = City::where('created_by', Auth::id())
            ->with([
                'state.country' => function ($q) {
                    $q->select(['id', 'name', 'code']);
                }
            ])
            ->orderBy('id');

        $cities = $this->applySimplePagination($query);

        return view('livewire.address.city-form', [
            'cities' => $cities,
        ]);
    }
}
