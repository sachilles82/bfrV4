<?php

namespace App\Traits\Model;

use App\Enums\Model\ModelStatus;

/**
 * Trait für die Model Status Optionen
 */
trait WithModelStatusOptions
{
    /**
     * Erforderliche Eigenschaften in der Komponente um den Model Status zu haben:
     * 1. public $model_status;
     * Diese Property muss in der Komponente initialisiert werden:
     *  public ?int $departmentId = null; // oder $employeeId, $reportId usw.
     * 2. in der render Methode der Komponente:
     *   'modelStatusOptions' => $this->modelStatusOptions,
     * 3. in der blade Datei:
     *
     * @foreach($modelStatusOptions as $statusOption)
     *
     * @endforeach
     */

    /**
     * Gets all available model status options with their labels, colors, and icons
     */
    public function getModelStatusOptionsProperty()
    {
        $statuses = [];

        foreach (ModelStatus::cases() as $status) {
            $statuses[] = [
                'value' => $status->value,
                'label' => $status->label(),
                'colors' => $status->colors(),
                'icon' => $status->icon(),
            ];
        }

        return $statuses;
    }
}
