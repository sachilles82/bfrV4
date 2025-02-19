<?php

namespace App\Livewire\Alem\Employee\Profile;

use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Component;

class Holiday extends Component
{
    public $dummyReports = [];

    public function mount()
    {
        $this->dummyReports = [
            [
                'id'          => 1,
                'title'       => 'Sales Report',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                'date'        => Carbon::now()->subDays(5)->toDateString(),
            ],
            [
                'id'          => 2,
                'title'       => 'Inventory Report',
                'description' => 'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'date'        => Carbon::now()->subDays(3)->toDateString(),
            ],
            [
                'id'          => 3,
                'title'       => 'Employee Performance Report',
                'description' => 'Ut enim ad minim veniam, quis nostrud exercitation ullamco.',
                'date'        => Carbon::now()->subDays(1)->toDateString(),
            ],
        ];
    }
    public function render(): View
    {
        return view('livewire.alem.employee.profile.holiday');
    }
}
