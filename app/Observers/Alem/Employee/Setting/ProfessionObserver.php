<?php

namespace App\Observers\Alem\Employee\Setting;

use App\Models\Profession;

class ProfessionObserver
{
    /**
     * Handle the Profession "created" event.
     */
    public function created(Profession $profession): void
    {
        //
    }

    /**
     * Handle the Profession "updated" event.
     */
    public function updated(Profession $profession): void
    {
        //
    }

    /**
     * Handle the Profession "deleted" event.
     */
    public function deleted(Profession $profession): void
    {
        //
    }

    /**
     * Handle the Profession "restored" event.
     */
    public function restored(Profession $profession): void
    {
        //
    }

    /**
     * Handle the Profession "force deleted" event.
     */
    public function forceDeleted(Profession $profession): void
    {
        //
    }
}
