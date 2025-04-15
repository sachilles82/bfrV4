<?php

namespace Tests\Feature\Alem\Model;

use App\Enums\Model\ModelStatus;
use App\Traits\Model\ModelPermanentDeletion;
use App\Traits\Model\ModelStatusAction;
use App\Traits\Model\ModelStatusManagement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Component;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ModelStatusActionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Create a test component that uses the ModelStatusAction trait
     */
    protected function getTestComponent()
    {
        return new class extends Component
        {
            use ModelStatusAction;

            public $selectedIds = [];

            public $idsOnPage = [];

            public $statusFilter = 'active';

            protected function getModelClass(): string
            {
                return TestModel::class;
            }

            protected function getModelDisplayName(): string
            {
                return 'TestModel';
            }

            protected function getModelDisplayNamePlural(): string
            {
                return 'TestModels';
            }

            protected function getStatusUpdateEventName(): string
            {
                return 'modelStatusUpdated';
            }

            public function render()
            {
                return <<<'blade'
                    <div>
                        <h1>Test Component</h1>
                    </div>
                blade;
            }
        };
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Create test model table for testing
        \Schema::create('test_models', function ($table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('model_status')->default(ModelStatus::ACTIVE->value);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    protected function tearDown(): void
    {
        \Schema::dropIfExists('test_models');
        parent::tearDown();
    }

    #[Test]
    public function it_can_apply_active_status_filter()
    {
        // Create models with different statuses
        $activeModel = TestModel::create(['name' => 'Active Model', 'model_status' => ModelStatus::ACTIVE]);
        $archivedModel = TestModel::create(['name' => 'Archived Model', 'model_status' => ModelStatus::ARCHIVED]);
        $trashedModel = TestModel::create(['name' => 'Trashed Model']);
        $trashedModel->delete();

        // Create a test query
        $query = TestModel::query();

        // Create a reflection to access protected method
        $component = $this->getTestComponent();
        $reflection = new \ReflectionClass($component);
        $method = $reflection->getMethod('applyStatusFilter');
        $method->setAccessible(true);

        // Test active filter
        $component->statusFilter = 'active';
        $filteredQuery = $method->invokeArgs($component, [$query]);
        $models = $filteredQuery->get();

        $this->assertTrue($models->contains('id', $activeModel->id));
        $this->assertFalse($models->contains('id', $archivedModel->id));
        $this->assertFalse($models->contains('id', $trashedModel->id));
    }

    #[Test]
    public function it_can_apply_archived_status_filter()
    {
        // Create models with different statuses
        $activeModel = TestModel::create(['name' => 'Active Model', 'model_status' => ModelStatus::ACTIVE]);
        $archivedModel = TestModel::create(['name' => 'Archived Model', 'model_status' => ModelStatus::ARCHIVED]);

        // Create a test query
        $query = TestModel::query();

        // Create a reflection to access protected method
        $component = $this->getTestComponent();
        $reflection = new \ReflectionClass($component);
        $method = $reflection->getMethod('applyStatusFilter');
        $method->setAccessible(true);

        // Test archived filter
        $component->statusFilter = 'archived';
        $filteredQuery = $method->invokeArgs($component, [$query]);
        $models = $filteredQuery->get();

        $this->assertFalse($models->contains('id', $activeModel->id));
        $this->assertTrue($models->contains('id', $archivedModel->id));
    }

    #[Test]
    public function it_can_apply_trashed_status_filter()
    {
        // Create models with different statuses
        $activeModel = TestModel::create(['name' => 'Active Model', 'model_status' => ModelStatus::ACTIVE]);
        $trashedModel = TestModel::create(['name' => 'Trashed Model']);
        $trashedModel->delete();

        // Create a test query
        $query = TestModel::query();

        // Create a reflection to access protected method
        $component = $this->getTestComponent();
        $reflection = new \ReflectionClass($component);
        $method = $reflection->getMethod('applyStatusFilter');
        $method->setAccessible(true);

        // Test trashed filter
        $component->statusFilter = 'trashed';
        $filteredQuery = $method->invokeArgs($component, [$query]);
        $models = $filteredQuery->get();

        $this->assertFalse($models->contains('id', $activeModel->id));
        $this->assertTrue($models->contains('id', $trashedModel->id));
    }

    #[Test]
    public function it_can_set_status_filter()
    {
        $component = Livewire::test(get_class($this->getTestComponent()))
            ->set('selectedIds', [1, 2, 3])
            ->set('idsOnPage', [1, 2, 3, 4])
            ->set('statusFilter', 'active');

        $component->call('setStatusFilter', 'archived')
            ->assertSet('statusFilter', 'archived')
            ->assertSet('selectedIds', [])
            ->assertSet('idsOnPage', [])
            ->assertDispatched('update-table');
    }

    #[Test]
    public function it_can_activate_a_model()
    {
        // Create an archived model
        $model = TestModel::create(['name' => 'Archived Model', 'model_status' => ModelStatus::ARCHIVED]);

        // Test activation
        Livewire::test(get_class($this->getTestComponent()))
            ->call('activate', $model->id)
            ->assertDispatched('modelStatusUpdated')
            ->assertDispatched('update-table');

        // Check if the model is now active
        $this->assertEquals(ModelStatus::ACTIVE, $model->fresh()->model_status);
    }

    #[Test]
    public function it_can_archive_a_model()
    {
        // Create an active model
        $model = TestModel::create(['name' => 'Active Model', 'model_status' => ModelStatus::ACTIVE]);

        // Test archiving
        Livewire::test(get_class($this->getTestComponent()))
            ->call('archive', $model->id)
            ->assertDispatched('modelStatusUpdated')
            ->assertDispatched('update-table');

        // Check if the model is now archived
        $this->assertEquals(ModelStatus::ARCHIVED, $model->fresh()->model_status);
    }

    #[Test]
    public function it_can_move_a_model_to_trash()
    {
        // Create a model
        $model = TestModel::create(['name' => 'Active Model', 'model_status' => ModelStatus::ACTIVE]);

        // Test deletion (to trash)
        Livewire::test(get_class($this->getTestComponent()))
            ->call('delete', $model->id)
            ->assertDispatched('modelStatusUpdated')
            ->assertDispatched('update-table');

        // Check if the model is in trash
        $this->assertTrue($model->fresh()->trashed());
        $this->assertEquals(ModelStatus::TRASHED, $model->fresh()->model_status);
    }

    #[Test]
    public function it_can_restore_a_model_from_trash_to_active()
    {
        // Create a model and put it in trash
        $model = TestModel::create(['name' => 'Active Model', 'model_status' => ModelStatus::ACTIVE]);
        $model->delete();

        // Check if the model is in trash
        $this->assertTrue($model->fresh()->trashed());
        $this->assertEquals(ModelStatus::TRASHED, $model->fresh()->model_status);

        // Test restoration
        Livewire::test(get_class($this->getTestComponent()))
            ->call('restore', $model->id)
            ->assertDispatched('modelStatusUpdated')
            ->assertDispatched('update-table');

        // Check if the model is restored and active
        $this->assertFalse($model->fresh()->trashed());
        $this->assertEquals(ModelStatus::ACTIVE, $model->fresh()->model_status);
    }

    #[Test]
    public function it_can_restore_a_model_to_archive()
    {
        // Create a model and put it in trash
        $model = TestModel::create(['name' => 'Active Model', 'model_status' => ModelStatus::ACTIVE]);
        $model->delete();

        // Test restoration as archived
        Livewire::test(get_class($this->getTestComponent()))
            ->call('restoreToArchive', $model->id)
            ->assertDispatched('modelStatusUpdated')
            ->assertDispatched('update-table');

        // Check if the model is restored and archived
        $this->assertFalse($model->fresh()->trashed());
        $this->assertEquals(ModelStatus::ARCHIVED, $model->fresh()->model_status);
    }

    #[Test]
    public function it_can_permanently_delete_a_model()
    {
        // Create a model and put it in trash
        $model = TestModel::create(['name' => 'Active Model', 'model_status' => ModelStatus::ACTIVE]);
        $model->delete();

        // Test permanent deletion
        Livewire::test(get_class($this->getTestComponent()))
            ->call('forceDelete', $model->id)
            ->assertDispatched('modelStatusUpdated')
            ->assertDispatched('update-table');

        // Check if the model is permanently deleted
        $this->assertNull(TestModel::withTrashed()->find($model->id));
    }

    #[Test]
    public function it_can_empty_trash()
    {
        // Create some models and put them in trash
        $model1 = TestModel::create(['name' => 'Model 1', 'model_status' => ModelStatus::ACTIVE]);
        $model2 = TestModel::create(['name' => 'Model 2', 'model_status' => ModelStatus::ACTIVE]);
        $model1->delete();
        $model2->delete();

        // Check if the models are in trash
        $this->assertTrue($model1->fresh()->trashed());
        $this->assertTrue($model2->fresh()->trashed());

        // Test emptying the trash
        Livewire::test(get_class($this->getTestComponent()))
            ->call('emptyTrash')
            ->assertDispatched('modelStatusUpdated')
            ->assertDispatched('update-table');

        // Check if all models are removed from trash
        $this->assertEquals(0, TestModel::onlyTrashed()->count());
    }

    #[Test]
    public function it_can_bulk_update_status_to_active()
    {
        // Create some models with different statuses
        $model1 = TestModel::create(['name' => 'Model 1', 'model_status' => ModelStatus::ARCHIVED]);
        $model2 = TestModel::create(['name' => 'Model 2', 'model_status' => ModelStatus::ARCHIVED]);

        // Test bulk action
        Livewire::test(get_class($this->getTestComponent()))
            ->set('selectedIds', [$model1->id, $model2->id])
            ->call('bulkUpdateStatus', 'active')
            ->assertDispatched('modelStatusUpdated')
            ->assertDispatched('update-table');

        // Check if all models are active
        $this->assertEquals(ModelStatus::ACTIVE, $model1->fresh()->model_status);
        $this->assertEquals(ModelStatus::ACTIVE, $model2->fresh()->model_status);
    }

    #[Test]
    public function it_can_bulk_update_status_to_archived()
    {
        // Create some models with different statuses
        $model1 = TestModel::create(['name' => 'Model 1', 'model_status' => ModelStatus::ACTIVE]);
        $model2 = TestModel::create(['name' => 'Model 2', 'model_status' => ModelStatus::ACTIVE]);

        // Test bulk action
        Livewire::test(get_class($this->getTestComponent()))
            ->set('selectedIds', [$model1->id, $model2->id])
            ->call('bulkUpdateStatus', 'archived')
            ->assertDispatched('modelStatusUpdated')
            ->assertDispatched('update-table');

        // Check if all models are archived
        $this->assertEquals(ModelStatus::ARCHIVED, $model1->fresh()->model_status);
        $this->assertEquals(ModelStatus::ARCHIVED, $model2->fresh()->model_status);
    }

    #[Test]
    public function it_can_bulk_move_to_trash()
    {
        // Create some models
        $model1 = TestModel::create(['name' => 'Model 1', 'model_status' => ModelStatus::ACTIVE]);
        $model2 = TestModel::create(['name' => 'Model 2', 'model_status' => ModelStatus::ARCHIVED]);

        // Test bulk action
        Livewire::test(get_class($this->getTestComponent()))
            ->set('selectedIds', [$model1->id, $model2->id])
            ->call('bulkUpdateStatus', 'trashed')
            ->assertDispatched('modelStatusUpdated')
            ->assertDispatched('update-table');

        // Check if all models are in trash
        $this->assertTrue($model1->fresh()->trashed());
        $this->assertTrue($model2->fresh()->trashed());
        $this->assertEquals(ModelStatus::TRASHED, $model1->fresh()->model_status);
        $this->assertEquals(ModelStatus::TRASHED, $model2->fresh()->model_status);
    }

    #[Test]
    public function it_can_bulk_restore_to_active()
    {
        // Create some models and put them in trash
        $model1 = TestModel::create(['name' => 'Model 1', 'model_status' => ModelStatus::ACTIVE]);
        $model2 = TestModel::create(['name' => 'Model 2', 'model_status' => ModelStatus::ACTIVE]);
        $model1->delete();
        $model2->delete();

        // Test bulk action
        Livewire::test(get_class($this->getTestComponent()))
            ->set('selectedIds', [$model1->id, $model2->id])
            ->call('bulkUpdateStatus', 'restore_to_active')
            ->assertDispatched('modelStatusUpdated')
            ->assertDispatched('update-table');

        // Check if all models are active
        $this->assertFalse($model1->fresh()->trashed());
        $this->assertFalse($model2->fresh()->trashed());
        $this->assertEquals(ModelStatus::ACTIVE, $model1->fresh()->model_status);
        $this->assertEquals(ModelStatus::ACTIVE, $model2->fresh()->model_status);
    }

    #[Test]
    public function it_can_bulk_restore_to_archive()
    {
        // Create some models and put them in trash
        $model1 = TestModel::create(['name' => 'Model 1', 'model_status' => ModelStatus::ACTIVE]);
        $model2 = TestModel::create(['name' => 'Model 2', 'model_status' => ModelStatus::ACTIVE]);
        $model1->delete();
        $model2->delete();

        // Test bulk action
        Livewire::test(get_class($this->getTestComponent()))
            ->set('selectedIds', [$model1->id, $model2->id])
            ->call('bulkUpdateStatus', 'restore_to_archive')
            ->assertDispatched('modelStatusUpdated')
            ->assertDispatched('update-table');

        // Check if all models are archived
        $this->assertFalse($model1->fresh()->trashed());
        $this->assertFalse($model2->fresh()->trashed());
        $this->assertEquals(ModelStatus::ARCHIVED, $model1->fresh()->model_status);
        $this->assertEquals(ModelStatus::ARCHIVED, $model2->fresh()->model_status);
    }

    #[Test]
    public function it_can_bulk_force_delete()
    {
        // Create some models and put them in trash
        $model1 = TestModel::create(['name' => 'Model 1', 'model_status' => ModelStatus::ACTIVE]);
        $model2 = TestModel::create(['name' => 'Model 2', 'model_status' => ModelStatus::ACTIVE]);
        $model1->delete();
        $model2->delete();

        // Test bulk action
        Livewire::test(get_class($this->getTestComponent()))
            ->set('selectedIds', [$model1->id, $model2->id])
            ->call('bulkForceDelete')
            ->assertDispatched('modelStatusUpdated')
            ->assertDispatched('update-table');

        // Check if all models are permanently deleted
        $this->assertNull(TestModel::withTrashed()->find($model1->id));
        $this->assertNull(TestModel::withTrashed()->find($model2->id));
    }

    #[Test]
    public function it_dispatches_status_events()
    {
        // Create a model for testing
        $model = TestModel::create(['name' => 'Archived Model', 'model_status' => ModelStatus::ARCHIVED]);

        // Activate the model (this triggers dispatchStatusEvents)
        Livewire::test(get_class($this->getTestComponent()))
            ->call('activate', $model->id)
            ->assertDispatched('modelStatusUpdated')
            ->assertDispatched('update-table');
    }

    #[Test]
    public function it_resets_selections()
    {
        // Create a component with selected IDs
        $component = Livewire::test(get_class($this->getTestComponent()))
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
    }
}

/**
 * Test model class for ModelStatusAction tests
 */
class TestModel extends Model
{
    use ModelPermanentDeletion;
    use ModelStatusManagement{
        ModelStatusManagement::restore insteadof SoftDeletes;
        // Alias für die originale SoftDeletes::restore()-Methode.
        SoftDeletes::restore as softRestore;
    }
    use SoftDeletes;

    protected $table = 'test_models';

    protected $fillable = ['name', 'model_status'];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'model_status' => ModelStatus::class,
    ];

    // Implement required methods for ModelStatusManagement

    public function isActive(): bool
    {
        return $this->model_status === ModelStatus::ACTIVE && ! $this->trashed();
    }

    public function isArchived(): bool
    {
        return $this->model_status === ModelStatus::ARCHIVED && ! $this->trashed();
    }

    public function hasStatus(ModelStatus $status): bool
    {
        return $this->model_status === $status;
    }

    public function restoreToActive(): bool
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

    public function restoreToArchive(): bool
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
