<?php

namespace App\Livewire\Address\Helper;

use App\Models\Address\City;
use App\Models\Address\Country;
use App\Models\Address\State;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

trait LoadData
{

    /** loads all countries */
    protected function loadCountries(): void
    {
//        $this->countries = Country::select(['id', 'name', 'code'])
//            ->orderBy('id')
//            ->get();

        $this->countries = Cache::remember('all-countries', 10080, function () {
            return Country::select(['id', 'name', 'code'])
                ->orderBy('id')
                ->get();
        });
    }

    /** loads all states */
    protected function loadStates(): void
    {
//        $this->states = State::select(['id', 'name', 'code', 'country_id', 'created_by', 'team_id'])
//            ->where(function ($query) {
//                $query->where('team_id', Auth::user()->currentTeam->id);
//                $query->orWhere('created_by', 1);
//            })
//            ->with([
//                'country' => function ($q) {
//                    $q->select(['id', 'name', 'code']);
//                },
//            ])
//            ->orderBy('id')
//            ->get();

        $cacheKey = 'states-user-' . Auth::id();

        $this->states = Cache::remember($cacheKey, 10080, function () {
            return State::select(['id', 'name', 'code', 'country_id', 'created_by', 'team_id'])
                ->where(function ($query) {
                    $query->where('team_id', Auth::user()->currentTeam->id)
                        ->orWhere('created_by', 1);
                })
                ->with([
                    'country' => function ($q) {
                        $q->select(['id', 'name', 'code']);
                    },
                ])
                ->orderBy('id')
                ->get();
        });
    }

    /** loads all cities */
    protected function loadCities(): void
    {
//        $this->cities = City::select(['id', 'name', 'state_id','created_by', 'team_id'])
//            ->where(function ($query) {
//                $query->where('team_id', Auth::user()->currentTeam->id);
//                $query->orWhere('created_by', 1);
//            })
//            ->with([
//                'state.country' => function ($q) {
//                    $q->select(['id', 'name', 'code']);
//                }
//            ])
//            ->orderBy('id')
//            ->get();

        $cacheKey = 'cities-user-' . Auth::id();

        $this->cities = Cache::remember($cacheKey, 10080, function () {
            return City::select(['id', 'name', 'state_id', 'created_by', 'team_id'])
                ->where(function ($query) {
                    $query->where('team_id', Auth::user()->currentTeam->id)
                        ->orWhere('created_by', 1);
                })
                ->with([
                    'state.country' => function ($q) {
                        $q->select(['id', 'name', 'code']);
                    },
                ])
                ->orderBy('id')
                ->get();
        });
    }

}
