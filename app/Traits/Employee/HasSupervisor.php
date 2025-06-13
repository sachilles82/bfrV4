<?php

namespace App\Traits\Employees;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Trait HasSupervisor
 *
 * Stellt Supervisor-Funktionalität für Employee Model bereit
 * Verwaltet hierarchische Beziehungen zwischen Mitarbeitern
 */
trait HasSupervisor
{
    /**
     * Boot-Methode für Model Events
     */
    public static function bootHasSupervisor()
    {
        // Validierung bei Supervisor-Zuweisung
        static::saving(function ($model) {
            if ($model->isDirty('supervisor_id') && $model->supervisor_id) {
                // Verhindere Selbst-Zuweisung
                if ($model->user_id === $model->supervisor_id) {
                    throw new \Exception('Ein Mitarbeiter kann nicht sein eigener Supervisor sein.');
                }

                // Validiere Hierarchie um Loops zu verhindern
                if (!static::validateSupervisorHierarchy($model->user_id, $model->supervisor_id)) {
                    throw new \Exception('Diese Supervisor-Zuweisung würde eine zirkuläre Hierarchie erstellen.');
                }
            }
        });
    }

    /**
     * Supervisor Relationship
     * Der direkte Vorgesetzte dieses Mitarbeiters
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id', 'id');
    }

    /**
     * Subordinates Relationship
     * Alle direkten Untergebenen dieses Mitarbeiters
     */
    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'supervisor_id', 'user_id');
    }

    /**
     * Prüft ob dieser Mitarbeiter ein Supervisor ist
     */
    public function isSupervisor(): bool
    {
        return $this->subordinates()->exists();
    }

    /**
     * Zählt die Anzahl der direkten Untergebenen
     */
    public function subordinatesCount(): int
    {
        return $this->subordinates()->count();
    }

    /**
     * Holt alle Untergebenen mit User-Informationen
     */
    public function getSubordinatesWithUsers()
    {
        return $this->subordinates()
            ->with(['user:id,name,last_name,email,profile_photo_path,model_status'])
            ->get();
    }

    /**
     * Holt die gesamte Hierarchie nach unten (alle Ebenen)
     */
    public function getAllSubordinates()
    {
        $allSubordinates = collect();
        $directSubordinates = $this->subordinates()->with('user')->get();

        foreach ($directSubordinates as $subordinate) {
            $allSubordinates->push($subordinate);
            $allSubordinates = $allSubordinates->merge($subordinate->getAllSubordinates());
        }

        return $allSubordinates;
    }

    /**
     * Holt die Supervisor-Kette nach oben
     */
    public function getSupervisorChain()
    {
        $chain = collect();
        $current = $this;

        while ($current->supervisor_id) {
            $supervisor = User::find($current->supervisor_id);
            if (!$supervisor) break;

            $chain->push($supervisor);
            $current = $supervisor->employee;

            if (!$current) break;
        }

        return $chain;
    }

    /**
     * Weist einen neuen Supervisor zu
     */
    public function assignSupervisor(?int $supervisorId): void
    {
        $this->supervisor_id = $supervisorId;
        $this->save();
    }

    /**
     * Entfernt den aktuellen Supervisor
     */
    public function removeSupervisor(): void
    {
        $this->supervisor_id = null;
        $this->save();
    }

    /**
     * Überträgt alle Untergebenen an einen anderen Supervisor
     */
    public function transferSubordinates(?int $newSupervisorId): void
    {
        $this->subordinates()->update([
            'supervisor_id' => $newSupervisorId
        ]);
    }

    /**
     * Prüft ob ein bestimmter User in der Supervisor-Kette ist
     */
    public function hasInSupervisorChain(int $userId): bool
    {
        return $this->getSupervisorChain()
            ->pluck('id')
            ->contains($userId);
    }

    /**
     * Validiert die Supervisor-Hierarchie um Loops zu verhindern
     */
    public static function validateSupervisorHierarchy(int $employeeUserId, int $supervisorUserId): bool
    {
        // Maximal 10 Ebenen prüfen um Endlosschleifen zu verhindern
        $maxLevels = 10;
        $currentLevel = 0;
        $currentSupervisorId = $supervisorUserId;

        while ($currentSupervisorId && $currentLevel < $maxLevels) {
            // Wenn wir auf den ursprünglichen Employee treffen, haben wir einen Loop
            if ($currentSupervisorId === $employeeUserId) {
                return false;
            }

            // Nächste Ebene
            $employee = Employee::where('user_id', $currentSupervisorId)->first();
            if (!$employee) {
                break; // Kein Employee-Eintrag, Hierarchie endet hier
            }

            $currentSupervisorId = $employee->supervisor_id;
            $currentLevel++;
        }

        return true;
    }

    /**
     * Scope: Mitarbeiter ohne Supervisor
     */
    public function scopeWithoutSupervisor($query)
    {
        return $query->whereNull('supervisor_id');
    }

    /**
     * Scope: Mitarbeiter mit Supervisor
     */
    public function scopeWithSupervisor($query)
    {
        return $query->whereNotNull('supervisor_id');
    }

    /**
     * Scope: Supervisoren (Mitarbeiter mit Untergebenen)
     */
    public function scopeSupervisors($query)
    {
        return $query->whereHas('subordinates');
    }
}
