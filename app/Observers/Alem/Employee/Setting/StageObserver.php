<?php

namespace App\Observers\Alem\Employee\Setting;

use App\Models\Stage;

class StageObserver
{
    /**
     * Handle the Stage "created" event.
     */
    public function created(Stage $stage): void
    {
        //
    }

    /**
     * Handle the Stage "updated" event.
     */
    public function updated(Stage $stage): void
    {
        //
    }

    /**
     * Handle the Stage "deleted" event.
     */
    public function deleted(Stage $stage): void
    {
        //
    }

    /**
     * Handle the Stage "restored" event.
     */
    public function restored(Stage $stage): void
    {
        //
    }

    /**
     * Handle the Stage "force deleted" event.
     */
    public function forceDeleted(Stage $stage): void
    {
        //
    }
}
