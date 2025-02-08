<?php

namespace App\Livewire\Spatie\Role\Helper;

trait HasAppData
{
    /**
     * Liefert das passende Bild, den Titel und die Version für die aktuelle $this->app
     */
    private function getAppData(): array
    {
        return match($this->app) {
            'baseApp' => [
                'logo' => 'https://tailwindui.com/plus/img/logos/48x48/savvycal.svg',
                'title' => 'BaseApp, Inc',
                'version' => 'v.1.028',
            ],
            'crmApp' => [
                'logo' => 'https://tailwindui.com/plus/img/logos/48x48/reform.svg',
                'title' => 'CRMApp, Inc',
                'version' => 'v.1.055',
            ],
            'holidayApp' => [
                'logo' => 'https://tailwindui.com/plus/img/logos/48x48/tuple.svg',
                'title' => 'HolidayApp, Inc',
                'version' => 'v.2.000',
            ],
            'projectApp' => [
                'logo' => 'https://tailwindui.com/plus/img/logos/48x48/tuple.svg',
                'title' => 'ProjectApp, Inc',
                'version' => 'v.1.999',
            ],
            'settingApp' => [
                'logo' => 'https://tailwindui.com/plus/img/logos/48x48/tuple.svg',
                'title' => 'SettingApp, Inc',
                'version' => 'v.1.999',
            ],
            default => [
                'logo' => 'https://tailwindui.com/plus/img/logos/48x48/transistor.svg',
                'title' => 'DefaultApp, Inc',
                'version' => 'v.0.999',
            ],
        };
    }
}
