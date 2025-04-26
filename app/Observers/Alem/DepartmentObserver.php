<?php

namespace App\Observers\Alem;

use App\Models\Alem\Department;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DepartmentObserver
{
    private function getCacheKey(Department $department): ?string
    {
        if (!$department->company_id) {
            Log::warning("DepartmentObserver: Konnte Cache Key nicht generieren, company_id fehlt für Department ID {$department->id}.");
            return null;
        }
        // Stellt sicher, dass das Präfix von Laravel Cache verwendet wird
        return "departments_list_company_{$department->company_id}";
    }

    /**
     * Handle the Department "saved" event.
     * Wird nach created und updated aufgerufen.
     */
    public function saved(Department $department): void
    {
        $cacheKey = $this->getCacheKey($department);
        if ($cacheKey) {
            Cache::forget($cacheKey);
            Log::info("DepartmentObserver: Cache für Key '{$cacheKey}' wurde nach saved gelöscht."); // Optional
        }
    }

    /**
     * Handle the Department "deleted" event.
     */
    public function deleted(Department $department): void
    {
        $cacheKey = $this->getCacheKey($department);
        if ($cacheKey) {
            Cache::forget($cacheKey);
            Log::info("DepartmentObserver: Cache für Key '{$cacheKey}' wurde nach deleted gelöscht."); // Optional
        }
    }

    /**
     * Handle the Department "restored" event.
     */
    public function restored(Department $department): void
    {
        $cacheKey = $this->getCacheKey($department);
        if ($cacheKey) {
            Cache::forget($cacheKey);
            Log::info("DepartmentObserver: Cache für Key '{$cacheKey}' wurde nach restored gelöscht."); // Optional
        }
    }

    /**
     * Handle the Department "force deleted" event.
     */
    public function forceDeleted(Department $department): void
    {
        $cacheKey = $this->getCacheKey($department);
        if ($cacheKey) {
            Cache::forget($cacheKey);
            Log::info("DepartmentObserver: Cache für Key '{$cacheKey}' wurde nach forceDeleted gelöscht."); // Optional
        }
    }
}
