<?php

uses(\Illuminate\Database\Eloquent\Model::class);
use App\Enums\Model\ModelStatus;
use \Illuminate\Database\Eloquent\Model;
use \Tests\Feature\Alem\Model\TestModel;
use \Livewire\Component;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

/**
 * Create a test component that uses the ModelStatusAction trait
 */
function getTestComponent()
{
    return new class extends Component
    {
        uses(\App\Traits\Model\ModelStatusAction::class);
        
        function getModelClass(): string
        {
            return TestModel::class;
        }

        function getModelDisplayName(): string
        {
            return 'TestModel';
        }

        function getModelDisplayNamePlural(): string
        {
            return 'TestModels';
        }

        function getStatusUpdateEventName(): string
        {
            return 'modelStatusUpdated';
        }

        function render()
        {
            return <<<'blade'
                    <div>
                        <h1>Test Component</h1>
                    </div>
                blade;
        }
    };
}
beforeEach(function () {
    // Create test model table for testing
    \Schema::create('test_models', function ($table) {
        $table->id();
        $table->string('name')->nullable();
        $table->string('model_status')->default(ModelStatus::ACTIVE->value);
        $table->timestamps();
        $table->softDeletes();
    });
});

afterEach(function () {
    \Schema::dropIfExists('test_models');
});

it('can apply active status filter', function () {
    // Create models with different statuses
    $activeModel = TestModel::create(['name' => 'Active Model', 'model_status' => ModelStatus::ACTIVE]);
    $archivedModel = TestModel::create(['name' => 'Archived Model', 'model_status' => ModelStatus::ARCHIVED]);
    $trashedModel = TestModel::create(['name' => 'Trashed Model']);
    $trashedModel->delete();

    // Create a test query
    $query = TestModel::query();

    // Create a reflection to access protected method
    $component = getTestComponent();
    $reflection = new \ReflectionClass($component);
    $method = $reflection->getMethod('applyStatusFilter');
    $method->setAccessible(true);

    // Test active filter
    $component->statusFilter = 'active';
    $filteredQuery = $method->invokeArgs($component, [$query]);
    $models = $filteredQuery->get();

    expect($models->contains('id', $activeModel->id))->toBeTrue();
    expect($models->contains('id', $archivedModel->id))->toBeFalse();
    expect($models->contains('id', $trashedModel->id))->toBeFalse();
});

it('can apply archived status filter', function () {
    // Create models with different statuses
    $activeModel = TestModel::create(['name' => 'Active Model', 'model_status' => ModelStatus::ACTIVE]);
    $archivedModel = TestModel::create(['name' => 'Archived Model', 'model_status' => ModelStatus::ARCHIVED]);

    // Create a test query
    $query = TestModel::query();

    // Create a reflection to access protected method
    $component = getTestComponent();
    $reflection = new \ReflectionClass($component);
    $method = $reflection->getMethod('applyStatusFilter');
    $method->setAccessible(true);

    // Test archived filter
    $component->statusFilter = 'archived';
    $filteredQuery = $method->invokeArgs($component, [$query]);
    $models = $filteredQuery->get();

    expect($models->contains('id', $activeModel->id))->toBeFalse();
    expect($models->contains('id', $archivedModel->id))->toBeTrue();
});

it('can apply trashed status filter', function () {
    // Create models with different statuses
    $activeModel = TestModel::create(['name' => 'Active Model', 'model_status' => ModelStatus::ACTIVE]);
    $trashedModel = TestModel::create(['name' => 'Trashed Model']);
    $trashedModel->delete();

    // Create a test query
    $query = TestModel::query();

    // Create a reflection to access protected method
    $component = getTestComponent();
    $reflection = new \ReflectionClass($component);
    $method = $reflection->getMethod('applyStatusFilter');
    $method->setAccessible(true);

    // Test trashed filter
    $component->statusFilter = 'trashed';
    $filteredQuery = $method->invokeArgs($component, [$query]);
    $models = $filteredQuery->get();

    expect($models->contains('id', $activeModel->id))->toBeFalse();
    expect($models->contains('id', $trashedModel->id))->toBeTrue();
});

it('can set status filter', function () {
    $component = Livewire::test(get_class(getTestComponent()))
        ->set('selectedIds', [1, 2, 3])
        ->set('idsOnPage', [1, 2, 3, 4])
        ->set('statusFilter', 'active');

    $component->call('setStatusFilter', 'archived')
        ->assertSet('statusFilter', 'archived')
        ->assertSet('selectedIds', [])
        ->assertSet('idsOnPage', [])
        ->assertDispatched('update-table');
});

it('can activate a model', function () {
    // Create an archived model
    $model = TestModel::create(['name' => 'Archived Model', 'model_status' => ModelStatus::ARCHIVED]);

    // Test activation
    Livewire::test(get_class(getTestComponent()))
        ->call('activate', $model->id)
        ->assertDispatched('modelStatusUpdated')
        ->assertDispatched('update-table');

    // Check if the model is now active
    expect($model->fresh()->model_status)->toEqual(ModelStatus::ACTIVE);
});

it('can archive a model', function () {
    // Create an active model
    $model = TestModel::create(['name' => 'Active Model', 'model_status' => ModelStatus::ACTIVE]);

    // Test archiving
    Livewire::test(get_class(getTestComponent()))
        ->call('archive', $model->id)
        ->assertDispatched('modelStatusUpdated')
        ->assertDispatched('update-table');

    // Check if the model is now archived
    expect($model->fresh()->model_status)->toEqual(ModelStatus::ARCHIVED);
});

it('can move a model to trash', function () {
    // Create a model
    $model = TestModel::create(['name' => 'Active Model', 'model_status' => ModelStatus::ACTIVE]);

    // Test deletion (to trash)
    Livewire::test(get_class(getTestComponent()))
        ->call('delete', $model->id)
        ->assertDispatched('modelStatusUpdated')
        ->assertDispatched('update-table');

    // Check if the model is in trash
    expect($model->fresh()->trashed())->toBeTrue();
    expect($model->fresh()->model_status)->toEqual(ModelStatus::TRASHED);
});

it('can restore a model from trash to active', function () {
    // Create a model and put it in trash
    $model = TestModel::create(['name' => 'Active Model', 'model_status' => ModelStatus::ACTIVE]);
    $model->delete();

    // Check if the model is in trash
    expect($model->fresh()->trashed())->toBeTrue();
    expect($model->fresh()->model_status)->toEqual(ModelStatus::TRASHED);

    // Test restoration
    Livewire::test(get_class(getTestComponent()))
        ->call('restore', $model->id)
        ->assertDispatched('modelStatusUpdated')
        ->assertDispatched('update-table');

    // Check if the model is restored and active
    expect($model->fresh()->trashed())->toBeFalse();
    expect($model->fresh()->model_status)->toEqual(ModelStatus::ACTIVE);
});

it('can restore a model to archive', function () {
    // Create a model and put it in trash
    $model = TestModel::create(['name' => 'Active Model', 'model_status' => ModelStatus::ACTIVE]);
    $model->delete();

    // Test restoration as archived
    Livewire::test(get_class(getTestComponent()))
        ->call('restoreToArchive', $model->id)
        ->assertDispatched('modelStatusUpdated')
        ->assertDispatched('update-table');

    // Check if the model is restored and archived
    expect($model->fresh()->trashed())->toBeFalse();
    expect($model->fresh()->model_status)->toEqual(ModelStatus::ARCHIVED);
});

it('can permanently delete a model', function () {
    // Create a model and put it in trash
    $model = TestModel::create(['name' => 'Active Model', 'model_status' => ModelStatus::ACTIVE]);
    $model->delete();

    // Test permanent deletion
    Livewire::test(get_class(getTestComponent()))
        ->call('forceDelete', $model->id)
        ->assertDispatched('modelStatusUpdated')
        ->assertDispatched('update-table');

    // Check if the model is permanently deleted
    expect(TestModel::withTrashed()->find($model->id))->toBeNull();
});

it('can empty trash', function () {
    // Create some models and put them in trash
    $model1 = TestModel::create(['name' => 'Model 1', 'model_status' => ModelStatus::ACTIVE]);
    $model2 = TestModel::create(['name' => 'Model 2', 'model_status' => ModelStatus::ACTIVE]);
    $model1->delete();
    $model2->delete();

    // Check if the models are in trash
    expect($model1->fresh()->trashed())->toBeTrue();
    expect($model2->fresh()->trashed())->toBeTrue();

    // Test emptying the trash
    Livewire::test(get_class(getTestComponent()))
        ->call('emptyTrash')
        ->assertDispatched('modelStatusUpdated')
        ->assertDispatched('update-table');

    // Check if all models are removed from trash
    expect(TestModel::onlyTrashed()->count())->toEqual(0);
});

it('can bulk update status to active', function () {
    // Create some models with different statuses
    $model1 = TestModel::create(['name' => 'Model 1', 'model_status' => ModelStatus::ARCHIVED]);
    $model2 = TestModel::create(['name' => 'Model 2', 'model_status' => ModelStatus::ARCHIVED]);

    // Test bulk action
    Livewire::test(get_class(getTestComponent()))
        ->set('selectedIds', [$model1->id, $model2->id])
        ->call('bulkUpdateStatus', 'active')
        ->assertDispatched('modelStatusUpdated')
        ->assertDispatched('update-table');

    // Check if all models are active
    expect($model1->fresh()->model_status)->toEqual(ModelStatus::ACTIVE);
    expect($model2->fresh()->model_status)->toEqual(ModelStatus::ACTIVE);
});

it('can bulk update status to archived', function () {
    // Create some models with different statuses
    $model1 = TestModel::create(['name' => 'Model 1', 'model_status' => ModelStatus::ACTIVE]);
    $model2 = TestModel::create(['name' => 'Model 2', 'model_status' => ModelStatus::ACTIVE]);

    // Test bulk action
    Livewire::test(get_class(getTestComponent()))
        ->set('selectedIds', [$model1->id, $model2->id])
        ->call('bulkUpdateStatus', 'archived')
        ->assertDispatched('modelStatusUpdated')
        ->assertDispatched('update-table');

    // Check if all models are archived
    expect($model1->fresh()->model_status)->toEqual(ModelStatus::ARCHIVED);
    expect($model2->fresh()->model_status)->toEqual(ModelStatus::ARCHIVED);
});

it('can bulk move to trash', function () {
    // Create some models
    $model1 = TestModel::create(['name' => 'Model 1', 'model_status' => ModelStatus::ACTIVE]);
    $model2 = TestModel::create(['name' => 'Model 2', 'model_status' => ModelStatus::ARCHIVED]);

    // Test bulk action
    Livewire::test(get_class(getTestComponent()))
        ->set('selectedIds', [$model1->id, $model2->id])
        ->call('bulkUpdateStatus', 'trashed')
        ->assertDispatched('modelStatusUpdated')
        ->assertDispatched('update-table');

    // Check if all models are in trash
    expect($model1->fresh()->trashed())->toBeTrue();
    expect($model2->fresh()->trashed())->toBeTrue();
    expect($model1->fresh()->model_status)->toEqual(ModelStatus::TRASHED);
    expect($model2->fresh()->model_status)->toEqual(ModelStatus::TRASHED);
});

it('can bulk restore to active', function () {
    // Create some models and put them in trash
    $model1 = TestModel::create(['name' => 'Model 1', 'model_status' => ModelStatus::ACTIVE]);
    $model2 = TestModel::create(['name' => 'Model 2', 'model_status' => ModelStatus::ACTIVE]);
    $model1->delete();
    $model2->delete();

    // Test bulk action
    Livewire::test(get_class(getTestComponent()))
        ->set('selectedIds', [$model1->id, $model2->id])
        ->call('bulkUpdateStatus', 'restore_to_active')
        ->assertDispatched('modelStatusUpdated')
        ->assertDispatched('update-table');

    // Check if all models are active
    expect($model1->fresh()->trashed())->toBeFalse();
    expect($model2->fresh()->trashed())->toBeFalse();
    expect($model1->fresh()->model_status)->toEqual(ModelStatus::ACTIVE);
    expect($model2->fresh()->model_status)->toEqual(ModelStatus::ACTIVE);
});

it('can bulk restore to archive', function () {
    // Create some models and put them in trash
    $model1 = TestModel::create(['name' => 'Model 1', 'model_status' => ModelStatus::ACTIVE]);
    $model2 = TestModel::create(['name' => 'Model 2', 'model_status' => ModelStatus::ACTIVE]);
    $model1->delete();
    $model2->delete();

    // Test bulk action
    Livewire::test(get_class(getTestComponent()))
        ->set('selectedIds', [$model1->id, $model2->id])
        ->call('bulkUpdateStatus', 'restore_to_archive')
        ->assertDispatched('modelStatusUpdated')
        ->assertDispatched('update-table');

    // Check if all models are archived
    expect($model1->fresh()->trashed())->toBeFalse();
    expect($model2->fresh()->trashed())->toBeFalse();
    expect($model1->fresh()->model_status)->toEqual(ModelStatus::ARCHIVED);
    expect($model2->fresh()->model_status)->toEqual(ModelStatus::ARCHIVED);
});

it('can bulk force delete', function () {
    // Create some models and put them in trash
    $model1 = TestModel::create(['name' => 'Model 1', 'model_status' => ModelStatus::ACTIVE]);
    $model2 = TestModel::create(['name' => 'Model 2', 'model_status' => ModelStatus::ACTIVE]);
    $model1->delete();
    $model2->delete();

    // Test bulk action
    Livewire::test(get_class(getTestComponent()))
        ->set('selectedIds', [$model1->id, $model2->id])
        ->call('bulkForceDelete')
        ->assertDispatched('modelStatusUpdated')
        ->assertDispatched('update-table');

    // Check if all models are permanently deleted
    expect(TestModel::withTrashed()->find($model1->id))->toBeNull();
    expect(TestModel::withTrashed()->find($model2->id))->toBeNull();
});

it('dispatches status events', function () {
    // Create a model for testing
    $model = TestModel::create(['name' => 'Archived Model', 'model_status' => ModelStatus::ARCHIVED]);

    // Activate the model (this triggers dispatchStatusEvents)
    Livewire::test(get_class(getTestComponent()))
        ->call('activate', $model->id)
        ->assertDispatched('modelStatusUpdated')
        ->assertDispatched('update-table');
});

it('resets selections', function () {
    // Create a component with selected IDs
    $component = Livewire::test(get_class(getTestComponent()))
        ->set('selectedIds', [1, 2, 3])
        ->set('idsOnPage', [1, 2, 3, 4]);

    // Create a reflection to access protected method
    $reflection = new \ReflectionClass($component->instance());
    $method = $reflection->getMethod('resetSelections');
    $method->setAccessible(true);

    // Call the method
    $method->invoke($component->instance());

    // Assert selections were reset
    $component->assertSet('selectedIds', []);
    $component->assertSet('idsOnPage', []);
});

/**
 * Test model class for ModelStatusAction tests
 */
class TestModel extends Model
{
    uses(\App\Traits\Model\ModelPermanentDeletion::class);
    
    uses(\App\Traits\Model\ModelStatusManagement::class);
    
    uses(\Illuminate\Database\Eloquent\SoftDeletes::class);
    
    // Implement required methods for ModelStatusManagement

    function isActive(): bool
    {
        return $this->model_status === ModelStatus::ACTIVE && ! $this->trashed();
    }

    function isArchived(): bool
    {
        return $this->model_status === ModelStatus::ARCHIVED && ! $this->trashed();
    }

    function hasStatus(ModelStatus $status): bool
    {
        return $this->model_status === $status;
    }

    function restoreToActive(): bool
    {
        $restored = false;
        if ($this->trashed()) {
            $this->restore();
            $restored = true;
        }
        $this->model_status = ModelStatus::ACTIVE;
        $this->save();

        return $restored;
    }

    function restoreToArchive(): bool
    {
        $restored = false;
        if ($this->trashed()) {
            $this->restore();
            $restored = true;
        }
        $this->model_status = ModelStatus::ARCHIVED;
        $this->save();

        return $restored;
    }
}
