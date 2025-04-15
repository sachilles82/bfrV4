<?php

namespace App\Traits;

use App\Models\Address\Address;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasAddress
{
    /**
     * Das ist eine MorphOne Realation.
     * Jedes Model hat nur eine Adresse.
     */
    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }
}
