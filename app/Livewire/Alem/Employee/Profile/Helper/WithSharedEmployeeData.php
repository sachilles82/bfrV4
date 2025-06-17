<?php

namespace App\Livewire\Alem\Employee\Profile\Helper;

use Illuminate\Support\Facades\Cache;

trait WithSharedEmployeeData
{
    protected ?string $sharedDataKey = null;
    protected ?object $sharedData = null;

    public function mountWithSharedEmployeeData(string $sharedDataKey): void
    {
        $this->sharedDataKey = $sharedDataKey;
        $this->loadSharedData();
    }

    protected function loadSharedData(): void
    {
        if (!$this->sharedDataKey) {
            return;
        }

        $this->sharedData = Cache::get($this->sharedDataKey);

        if ($this->sharedData) {
            $this->hydrateFromSharedData();
        }
    }

    abstract protected function hydrateFromSharedData(): void;
}
