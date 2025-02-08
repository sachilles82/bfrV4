<?php

namespace App\Livewire\Address;

use App\Livewire\Address\Helper\ValidateCityForm;
use App\Models\Address\City;
use App\Traits\Table\WithPerPagePagination;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Spatie\ResponseCache\ResponseCache;

class CityForm extends Component
{
    use ValidateCityForm, WithPerPagePagination;

    public Collection $countries;
    public Collection $states;

    public ?int $country_id = null;
    public ?int $state_id = null;

    #[Locked]
    public ?int $cityId = null;
    public ?string $name = null;

    public bool $editing = false;

    /** Wir nehmen die vom AddressManager übergebenen Props entgegen:*/
    public function mount($countries, $states): void
    {
        $this->countries = $countries;
        $this->states = $states;
    }

    public function saveCity(): void
    {
        $this->validate();

        if ($this->editing && $this->cityId) {

            $city = City::where('created_by', auth()->id())
                ->findOrFail($this->cityId);

            $city->update([
                'name' => $this->name,
                'state_id' => $this->state_id,
            ]);

            Flux::toast(
                text: __('City updated successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

        } else {

            City::create([
                'name' => $this->name,
                'state_id' => $this->state_id,
                'created_by' => auth()->id(),
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
        $city = City::where('created_by', auth()->id())->findOrFail($id);

        $this->cityId = $city->id;
        $this->name = $city->name;
        $this->state_id = $city->state_id;
        $this->country_id = $city->state?->country_id;

        $this->editing = true;
    }

    public function deleteCity(int $id): void
    {
        // Autorisierung
//        $this->authorize('delete');
        $city = City::where('created_by', auth()->id())->findOrFail($id);

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
