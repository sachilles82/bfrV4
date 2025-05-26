<?php

namespace App\Livewire\Alem\QuickCrud\Stage;

use App\Livewire\Alem\QuickCrud\Stage\Helper\ValidateStageForm;
use App\Models\Alem\QuickCrud\Stage;
use App\Traits\Modal\WithPlaceholder;
use App\Traits\Table\WithPerPagePagination;
use Flux\Flux;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class StageForm extends Component
{
    use ValidateStageForm, WithPerPagePagination, WithPlaceholder;

    #[Locked]
    public ?int $stageId = null;

    public ?string $name   = null;
    public bool   $editing = false;

    /**
     * Flag, ob das Modal (und damit die Daten) bereits geladen wurden
     */
    public bool $loaded = false;

    /**
     * Speichert oder aktualisiert eine Stage.
     */
    public function saveStage(): void
    {
        $this->validate();

        try {
            if ($this->editing && $this->stageId) {
                $user = Auth::user();
                $stage = Stage::query()
                    ->where(fn($q) => $q
                        ->where('created_by', $user->id)
                        ->when($user->company_id, fn($q2) => $q2
                            ->orWhereHas('creator', fn($q3) => $q3->where('company_id', $user->company_id))
                        )
                    )
                    ->findOrFail($this->stageId);

                $stage->update(['name' => $this->name]);
                $this->dispatch('stage-updated');
                Flux::toast(text: __('Stage updated successfully.'), heading: __('Success.'), variant: 'success');
            } else {
                $created = Stage::create(['name' => $this->name]);
                $this->dispatch('stage-created', id: $created->id);
                Flux::toast(text: __('Stage created successfully.'), heading: __('Success.'), variant: 'success');
            }
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Flux::toast(text: __('An error occurred while saving the Stage.'), heading: __('Error.'), variant: 'error');
        }

        $this->finish();
        $this->resetPage();
        $this->loaded = true; // nach dem Speichern beim nächsten render() Daten holen
    }

    /**
     * Lädt einen Datensatz zur Bearbeitung.
     */
    public function editStage(int $id): void
    {
        try {
            $stage = Stage::where('created_by', Auth::id())->findOrFail($id);
            $this->stageId = $stage->id;
            $this->name    = $stage->name;
            $this->editing = true;
            $this->resetValidation();
        } catch (\Throwable $e) {
            Flux::toast(text: __('Cannot edit this stage.'), heading: __('Error'), variant: 'danger');
        }
    }

    /**
     * Löscht eine Stage.
     */
    public function deleteStage(int $id): void
    {
        try {
            $stage = Stage::where('created_by', Auth::id())->findOrFail($id);
            $stage->delete();
            Flux::toast(text: __('Stage deleted successfully.'), heading: __('Success.'), variant: 'success');
        } catch (\Throwable $e) {
            Flux::toast(text: __('Cannot delete this stage.'), heading: __('Error'), variant: 'danger');
        }

        $this->finish();
        $this->resetPage();
        $this->loaded = true;
    }

    /**
     * Schließt das Modal und setzt Formular zurück.
     */
    public function finish(): void
    {
        $this->modal('create-stage')->close();
        $this->reset(['stageId', 'name', 'editing']);
        $this->resetValidation();
    }

    /**
     * Event-Handler: Modal öffnen
     */
    #[On('open-modal-manager')]
    public function openModal(): void
    {
        $this->loaded = true;
        $this->resetPage();
        $this->reset(['stageId', 'name', 'editing']);
        $this->resetValidation();
    }

    /**
     * Render-Methode: Erzeugt hier IMMER einen LengthAwarePaginator,
     * entweder mit realen Daten oder leer.
     */
    public function render(Request $request): View
    {
        if ($this->loaded) {
            $user = Auth::user();

            $query = Stage::query()
                ->select('id', 'name', 'created_by')
                ->with('creator:id,name,company_id')
                ->where(fn($q) => $q
                    ->where('created_by', optional($user)->id)
                    ->orWhere('created_by', 1)
                    ->when(optional($user)->company_id, fn($q2) => $q2
                        ->orWhereHas('creator', fn($q3) => $q3->where('company_id', $user->company_id))
                    )
                )
                ->orderBy('id');

            // simplePaginate liefert einen SimplePaginator mit links()
            $stagesPaginator = $this->applySimplePagination($query);
        } else {
            // Noch nicht geladen: leerer LengthAwarePaginator, damit ->links() geht
            $stagesPaginator = new LengthAwarePaginator(
                items: [],
                total: 0,
                perPage: $this->perPage,
                currentPage: 1,
                options: [
                    'path' => $request->url(),
                    'pageName' => 'page',
                ]
            );
        }

        return view('livewire.alem.quick-crud.stage.stage-form', [
            'stages' => $stagesPaginator,
        ]);
    }
}
