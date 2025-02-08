<?php

namespace App\Livewire\Setting;

use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Theme extends Component
{
    public $theme;

    public function mount()
    {
        $this->theme = Auth::user()->theme ?? 'default';
    }

    public function updatedTheme($value)
    {
        // Persist to DB
        $user = Auth::user();
        $user->theme = $value;
        $user->save();

        // Dispatch an event so JS can immediately update local storage
        $this->dispatch('themeChanged', [
            'theme' => $value,
        ]);

        Flux::toast(
            text: __('You have a new theme!'),
            heading: __('Changes saved.'),
            variant: 'success'
        );
    }

    public function render()
    {
        return view('livewire.setting.theme');
    }
}


