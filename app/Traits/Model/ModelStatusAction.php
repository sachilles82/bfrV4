<?php

namespace App\Traits\Model;
use App\Enums\Model\ModelStatus;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Url;

/**
 * Trait für die Handhabung von Model-Status-Aktionen in Livewire-Komponenten
 *
 * Dieses Trait stellt Methoden bereit, um den Status von Modellen zu ändern und zu filtern.
 * Es kann in verschiedenen Komponenten für beliebige Modelle verwendet werden, die das
 * ModelStatusManagement Trait implementieren.
 */
trait ModelStatusAction
{
    /**
     * Diese Eigenschaften werden in jeder Komponente gebraucht. CheckAll Box:
     * public $selectedIds = [];
     * public $idsOnPage = [];
     * public $statusFilter = 'active';
     */

    #[Url]
    public $statusFilter = 'active';
    public $selectedIds = [];
    public $idsOnPage = [];

    /**
     * Gibt die Modellklasse zurück, die in der Komponente verwendet wird.
     * Muss in der Komponente implementiert werden.
     *
     * @return string Die vollständige Klassenname des Modells (z.B. \App\Models\User::class)
     */
    abstract protected function getModelClass(): string;

    /**
     * Gibt den Anzeigenamen für das Modell zurück (für Benachrichtigungen)
     * Kann in der Komponente überschrieben werden.
     *
     * @return string Der Anzeigename des Modells (z.B. 'Account', 'User', 'Product', etc.)
     */
    protected function getModelDisplayName(): string
    {
        return 'Item';
    }

    /**
     * Status-Filter setzen
     *
     * @param string $status Der neue Status ('active', 'archived', 'trashed')
     */
    public function setStatusFilter(string $status): void
    {
        $this->statusFilter = $status;
        $this->selectedIds = [];
        $this->idsOnPage = [];
        $this->dispatch('update-table');
    }

    /**
     * Status-Filter auf die Abfrage anwenden
     *
     * @param Builder $query Die zu filternde Query
     * @return Builder Die gefilterte Query
     */
    protected function applyStatusFilter(Builder $query): Builder
    {
        switch ($this->statusFilter) {
            case 'archived':
                $query->whereNull('deleted_at')
                    ->where('model_status', ModelStatus::ARCHIVED);
                break;
            case 'trashed':
                $query->onlyTrashed();
                break;
            case 'active':
            default:
                $query->whereNull('deleted_at')
                    ->where('model_status', ModelStatus::ACTIVE);
        }
        return $query;
    }

    /**
     * Hilfsmethode, um eine Instanz des Modells zu erhalten
     *
     * @return Model Eine neue Instanz des Modells
     */
    protected function getModelInstance(): Model
    {
        $class = $this->getModelClass();
        return new $class;
    }

    /**
     * Hilfsmethode für den Modell-Query-Builder
     *
     * @param bool $withTrashed Ob gelöschte Einträge einbezogen werden sollen
     * @return Builder Der Query-Builder für das Modell
     */
    protected function getModelQuery(bool $withTrashed = false): Builder
    {
        $class = $this->getModelClass();
        return $withTrashed ? $class::withTrashed() : $class::query();
    }

    // Einzelne Model-Statusänderungen

    /**
     * Model aktivieren
     *
     * @param int|string $modelId Die Modell-ID
     */
    public function activate($modelId): void
    {
        $model = $this->getModelQuery(true)->find($modelId);
        if ($model && $model->model_status !== ModelStatus::ACTIVE) {
            $this->setModelActive($model);
            $this->showToast(__(':name activated.', ['name' => $this->getModelDisplayName()]));
            $this->dispatchStatusEvents();
        }
    }

    /**
     * Model archivieren
     *
     * @param int|string $modelId Die Modell-ID
     */
    public function archive($modelId): void
    {
        $model = $this->getModelQuery()->find($modelId);
        if ($model && $model->model_status !== ModelStatus::ARCHIVED) {
            $this->setModelArchived($model);
            $this->showToast(__(':name moved to archive.', ['name' => $this->getModelDisplayName()]));
            $this->dispatchStatusEvents();
        }
    }

    /**
     * Model in den Papierkorb verschieben
     *
     * @param int|string $modelId Die Modell-ID
     */
    public function delete($modelId): void
    {
        $model = $this->getModelQuery()->find($modelId);
        if ($model) {
            $this->trashModel($model);
            $this->showToast(__(':name moved to trash.', ['name' => $this->getModelDisplayName()]));
            $this->dispatchStatusEvents();
        }
    }

    /**
     * Wiederherstellung als Active.
     * Wenn das Model im Papierkorb ist, wird es mit restoreToActive() wiederhergestellt.
     * Falls der Status archived ist, wird setModelActive() aufgerufen.
     *
     * @param int|string $modelId Die Modell-ID
     */
    public function restore($modelId): void
    {
        $model = $this->getModelQuery(true)->find($modelId);
        if (!$model) return;

        if ($model->trashed()) {
            $model->restoreToActive();
            $this->showToast(__(':name restored to active.', ['name' => $this->getModelDisplayName()]));
        } elseif ($model->model_status === ModelStatus::ARCHIVED) {
            $this->setModelActive($model);
            $this->showToast(__(':name set to active.', ['name' => $this->getModelDisplayName()]));
        }

        $this->resetSelections();
        $this->dispatchStatusEvents();
    }

    /**
     * Model im archivierten Zustand wiederherstellen
     *
     * @param int|string $modelId Die Modell-ID
     */
    public function restoreToArchive($modelId): void
    {
        $model = $this->getModelQuery(true)->find($modelId);
        if (!$model) return;

        if ($model->trashed()) {
            $model->restoreToArchive();
            $this->showToast(__(':name restored to archive.', ['name' => $this->getModelDisplayName()]));
        } elseif ($model->model_status !== ModelStatus::ARCHIVED) {
            $this->setModelArchived($model);
            $this->showToast(__(':name moved to archive.', ['name' => $this->getModelDisplayName()]));
        }
        $this->dispatchStatusEvents();
    }

    /**
     * Model dauerhaft löschen
     *
     * @param int|string $modelId Die Modell-ID
     */
    public function forceDelete($modelId): void
    {
        $model = $this->getModelQuery(true)->find($modelId);
        if ($model) {
            $model->forceDelete();
            $this->showToast(
                __(':name permanently deleted.', ['name' => $this->getModelDisplayName()]),
                'Success',
                'danger'
            );
            $this->dispatchStatusEvents();
        }
    }

    /**
     * Papierkorb leeren
     */
    public function emptyTrash(): void
    {
        $count = $this->getModelQuery(true)->onlyTrashed()->count();

        if ($count > 0) {
            $this->getModelQuery(true)->onlyTrashed()->forceDelete();
            $this->showToast(
                __(':count :name permanently deleted.', [
                    'count' => $count,
                    'name' => $count === 1
                        ? $this->getModelDisplayName()
                        : $this->getModelDisplayNamePlural()
                ]),
                'Success',
                'danger'
            );
            $this->dispatchStatusEvents();
        } else {
            $this->showToast(__('Trash bin is already empty.'));
        }
    }

    /**
     * Gibt den Plural des Modell-Anzeigenamens zurück
     * Kann in der Komponente überschrieben werden.
     *
     * @return string Der pluralisierte Anzeigename des Modells
     */
    protected function getModelDisplayNamePlural(): string
    {
        return $this->getModelDisplayName() . 's';
    }

    // Bulk-Aktionen

    /**
     * Bulk-Status-Update für mehrere Modelle
     *
     * @param string $action Die durchzuführende Aktion ('active', 'archived', 'trashed', 'restore_to_archive')
     */
    public function bulkUpdateStatus(string $action): void
    {
        if (empty($this->selectedIds)) {
            $this->showToast(__('No :name selected.', ['name' => $this->getModelDisplayNamePlural()]), 'Info', 'info');
            return;
        }

        $models = $this->getModelQuery(true)->whereIn('id', $this->selectedIds)->get();
        $count = 0;

        foreach ($models as $model) {
            $count++;
            match($action) {
                'active' => $this->setModelActive($model),
                'archived' => $this->setModelArchived($model),
                'trashed' => $this->trashModel($model),
                'restore_to_active' => $model->restoreToActive(),
                'restore_to_archive' => $model->restoreToArchive(),
                default => null,
            };
        }

        if ($count > 0) {
            $this->showToast(
                __(':count :name status updated successfully.', [
                    'count' => $count,
                    'name' => $count === 1
                        ? $this->getModelDisplayName()
                        : $this->getModelDisplayNamePlural()
                ]),
                'Success',
                'success'
            );
        }

        $this->resetSelections();
        $this->dispatchStatusEvents();
    }

    /**
     * Permanentes Löschen mehrerer Modelle aus dem Papierkorb
     */
    public function bulkForceDelete(): void
    {
        if (empty($this->selectedIds)) {
            $this->showToast(__('No :name selected.', ['name' => $this->getModelDisplayNamePlural()]), 'Info', 'info');
            return;
        }

        $models = $this->getModelQuery(true)->whereIn('id', $this->selectedIds)->get();
        $count = 0;

        foreach ($models as $model) {
            if ($model->trashed()) {
                $model->forceDelete();
                $count++;
            }
        }

        if ($count > 0) {
            $this->showToast(
                __(':count :name permanently deleted.', [
                    'count' => $count,
                    'name' => $count === 1
                        ? $this->getModelDisplayName()
                        : $this->getModelDisplayNamePlural()
                ]),
                'Success',
                'danger'
            );
        }

        $this->resetSelections();
        $this->dispatchStatusEvents();
    }

    // Hilfsmethoden

    /**
     * Setzt ein Model auf aktiv
     *
     * @param Model $model Das zu ändernde Model
     */
    private function setModelActive(Model $model): void
    {
        if ($model->trashed()) {
            $model->restoreToActive();
        } else {
            $model->model_status = ModelStatus::ACTIVE;
            $model->save();
        }
    }

    /**
     * Archiviert ein Model
     *
     * @param Model $model Das zu ändernde Model
     */
    private function setModelArchived(Model $model): void
    {
        if ($model->trashed()) {
            $model->restoreToArchive();
        } else {
            $model->model_status = ModelStatus::ARCHIVED;
            $model->save();
        }
    }

    /**
     * Verschiebt ein Model in den Papierkorb
     *
     * @param Model $model Das zu ändernde Model
     */
    private function trashModel(Model $model): void
    {
        if (!$model->trashed()) {
            $model->delete();
        }
    }

    /**
     * Zeigt eine Toast-Nachricht an
     *
     * @param string $message Die anzuzeigende Nachricht
     * @param string $heading Die Überschrift (Standard: 'Success')
     * @param string $variant Der Typ der Nachricht (Standard: 'success')
     * @param array $params Parameter für die Übersetzung
     */
    private function showToast(string $message, string $heading = 'Success', string $variant = 'success', array $params = []): void
    {
        Flux::toast(
            text: __($message, $params),
            heading: __($heading),
            variant: $variant
        );
    }

    /**
     * Setzt ausgewählte IDs zurück
     */
    private function resetSelections(): void
    {
        $this->selectedIds = [];
        $this->idsOnPage = [];
    }

    /**
     * Sendet Events zur UI-Aktualisierung
     * Der Event-Name kann in der Komponente überschrieben werden.
     */
    private function dispatchStatusEvents(): void
    {
        // Standardmäßig "modelUpdated" senden, kann in der Komponente angepasst werden
        $updateEvent = $this->getStatusUpdateEventName() ?? 'modelUpdated';
        $this->dispatch($updateEvent);
        $this->dispatch('update-table');
    }

    /**
     * Gibt den Event-Namen für Status-Updates zurück
     * Kann in der Komponente überschrieben werden, um benutzerdefinierte Events zu senden
     *
     * @return string|null Der Event-Name oder null für Standardverhalten
     */
    protected function getStatusUpdateEventName(): ?string
    {
        return null; // Standard: 'modelUpdated' (siehe dispatchStatusEvents)
    }
}
