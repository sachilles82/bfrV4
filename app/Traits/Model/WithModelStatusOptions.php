<?php

namespace App\Traits\Model;

use App\Enums\Model\ModelStatus;

/**
 * Trait für die Model Status Optionen
 */
trait WithModelStatusOptions
{


    /**
     * Erforderliche Eigenschaften in der Komponente:
     * public $model_status;
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
