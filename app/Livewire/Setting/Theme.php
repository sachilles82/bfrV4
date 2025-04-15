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
        // Validate theme to prevent invalid values
        $allowedThemes = ['default', 'orange', 'green', 'blue', 'red', 'lime', 'pink'];

        if (! in_array($value, $allowedThemes)) {
            $value = 'default';
        }

        // Cache the theme value to prevent Livewire from resetting it
        $this->theme = $value;

        // Persist to DB
        $user = Auth::user();
        $user->theme = $value;
        $user->save();

        // Dispatch an event so JS can immediately update local storage
        // This prevents needing a page refresh
        $this->dispatch('themeChanged', [
            'theme' => $value,
        ]);

        Flux::toast(
            text: __('Theme updated successfully!'),
            heading: __('Changes saved.'),
            variant: 'success'
        );
    }

    public function render()
    {
        return view('livewire.setting.theme');
    }
}
