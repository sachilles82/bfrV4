<?php

namespace Tests\Feature\Alem\User;

use App\Enums\Model\ModelStatus;
use App\Livewire\Alem\Traits\UserStatusAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Component;
use Livewire\Livewire;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class UserStatusActionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Create a test component that uses the UserStatusAction trait
     */
    protected function getTestComponent()
    {
        return new class extends Component {
            use UserStatusAction;

            public $selectedIds = [];
            public $idsOnPage = [];
            public $statusFilter = 'active';

            protected function getStatusUpdateEventName(): string
            {
                return 'userStatusUpdated';
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

    #[Test]
    public function it_can_apply_active_status_filter()
    {
        // Create users with different statuses
        $activeUser = User::factory()->create(['model_status' => ModelStatus::ACTIVE]);
        $inactiveUser = User::factory()->create(['model_status' => ModelStatus::INACTIVE]);
        $archivedUser = User::factory()->create(['model_status' => ModelStatus::ARCHIVED]);
        $trashedUser = User::factory()->create();
        $trashedUser->delete();

        // Create a test query
        $query = User::query();

        // Create a reflection to access protected method
        $component = $this->getTestComponent();
        $reflection = new \ReflectionClass($component);
        $method = $reflection->getMethod('applyStatusFilter');
        $method->setAccessible(true);

        // Test active filter
        $component->statusFilter = 'active';
        $filteredQuery = $method->invokeArgs($component, [$query]);
        $users = $filteredQuery->get();

        $this->assertTrue($users->contains($activeUser->id));
        $this->assertFalse($users->contains($inactiveUser->id));
        $this->assertFalse($users->contains($archivedUser->id));
        $this->assertFalse($users->contains($trashedUser->id));
    }

    #[Test]
    public function it_can_apply_inactive_status_filter()
    {
        // Create users with different statuses
        $activeUser = User::factory()->create(['model_status' => ModelStatus::ACTIVE]);
        $inactiveUser = User::factory()->create(['model_status' => ModelStatus::INACTIVE]);

        // Create a test query
        $query = User::query();

        // Create a reflection to access protected method
        $component = $this->getTestComponent();
        $reflection = new \ReflectionClass($component);
        $method = $reflection->getMethod('applyStatusFilter');
        $method->setAccessible(true);

        // Test inactive filter
        $component->statusFilter = 'inactive';
        $filteredQuery = $method->invokeArgs($component, [$query]);
        $users = $filteredQuery->get();

        $this->assertFalse($users->contains($activeUser->id));
        $this->assertTrue($users->contains($inactiveUser->id));
    }

    #[Test]
    public function it_can_apply_archived_status_filter()
    {
        // Create users with different statuses
        $activeUser = User::factory()->create(['model_status' => ModelStatus::ACTIVE]);
        $archivedUser = User::factory()->create(['model_status' => ModelStatus::ARCHIVED]);

        // Create a test query
        $query = User::query();

        // Create a reflection to access protected method
        $component = $this->getTestComponent();
        $reflection = new \ReflectionClass($component);
        $method = $reflection->getMethod('applyStatusFilter');
        $method->setAccessible(true);

        // Test archived filter
        $component->statusFilter = 'archived';
        $filteredQuery = $method->invokeArgs($component, [$query]);
        $users = $filteredQuery->get();

        $this->assertFalse($users->contains($activeUser->id));
        $this->assertTrue($users->contains($archivedUser->id));
    }

    #[Test]
    public function it_can_apply_trashed_status_filter()
    {
        // Create users with different statuses
        $activeUser = User::factory()->create(['model_status' => ModelStatus::ACTIVE]);
        $trashedUser = User::factory()->create();
        $trashedUser->delete();

        // Create a test query
        $query = User::query();

        // Create a reflection to access protected method
        $component = $this->getTestComponent();
        $reflection = new \ReflectionClass($component);
        $method = $reflection->getMethod('applyStatusFilter');
        $method->setAccessible(true);

        // Test trashed filter
        $component->statusFilter = 'trashed';
        $filteredQuery = $method->invokeArgs($component, [$query]);
        $users = $filteredQuery->get();

        $this->assertFalse($users->contains($activeUser->id));
        $this->assertTrue($users->contains($trashedUser->id));
    }

    #[Test]
    public function it_can_set_status_filter()
    {
        $component = Livewire::test(get_class($this->getTestComponent()))
            ->set('selectedIds', [1, 2, 3])
            ->set('idsOnPage', [1, 2, 3, 4])
            ->set('statusFilter', 'active');

        $component->call('setStatusFilter', 'inactive')
            ->assertSet('statusFilter', 'inactive')
            ->assertSet('selectedIds', [])
            ->assertSet('idsOnPage', [])
            ->assertDispatched('update-table');
    }

    #[Test]
    public function it_can_activate_a_user()
    {
        // Erstelle einen inaktiven Benutzer
        $user = User::factory()->create(['model_status' => ModelStatus::INACTIVE]);

        // Teste die Aktivierung
        Livewire::test(get_class($this->getTestComponent()))
            ->call('activate', $user->id)
            ->assertDispatched('userStatusUpdated')
            ->assertDispatched('update-table');

        // Überprüfe, ob der Benutzer jetzt aktiv ist
        $this->assertEquals(ModelStatus::ACTIVE, $user->fresh()->model_status);
    }

    #[Test]
    public function it_can_set_a_user_to_inactive()
    {
        // Erstelle einen aktiven Benutzer
        $user = User::factory()->create(['model_status' => ModelStatus::ACTIVE]);

        // Teste die Deaktivierung
        Livewire::test(get_class($this->getTestComponent()))
            ->call('notActivate', $user->id)
            ->assertDispatched('userStatusUpdated')
            ->assertDispatched('update-table');

        // Überprüfe, ob der Benutzer jetzt inaktiv ist
        $this->assertEquals(ModelStatus::INACTIVE, $user->fresh()->model_status);
    }

    #[Test]
    public function it_can_archive_a_user()
    {
        // Erstelle einen aktiven Benutzer
        $user = User::factory()->create(['model_status' => ModelStatus::ACTIVE]);

        // Teste die Archivierung
        Livewire::test(get_class($this->getTestComponent()))
            ->call('archive', $user->id)
            ->assertDispatched('userStatusUpdated')
            ->assertDispatched('update-table');

        // Überprüfe, ob der Benutzer jetzt archiviert ist
        $this->assertEquals(ModelStatus::ARCHIVED, $user->fresh()->model_status);
    }

    #[Test]
    public function it_can_move_a_user_to_trash()
    {
        // Erstelle einen Benutzer
        $user = User::factory()->create(['model_status' => ModelStatus::ACTIVE]);

        // Teste das Löschen (in den Papierkorb)
        Livewire::test(get_class($this->getTestComponent()))
            ->call('delete', $user->id)
            ->assertDispatched('userStatusUpdated')
            ->assertDispatched('update-table');

        // Überprüfe, ob der Benutzer im Papierkorb ist
        $this->assertTrue($user->fresh()->trashed());
        $this->assertEquals(ModelStatus::TRASHED, $user->fresh()->model_status);
    }

    #[Test]
    public function it_can_restore_a_user_from_trash_to_active()
    {
        // Erstelle einen Benutzer und lege ihn in den Papierkorb
        $user = User::factory()->create(['model_status' => ModelStatus::ACTIVE]);
        $user->delete();

        // Überprüfe, ob der Benutzer im Papierkorb ist
        $this->assertTrue($user->fresh()->trashed());
        $this->assertEquals(ModelStatus::TRASHED, $user->fresh()->model_status);

        // Teste die Wiederherstellung
        Livewire::test(get_class($this->getTestComponent()))
            ->call('restore', $user->id)
            ->assertDispatched('userStatusUpdated')
            ->assertDispatched('update-table');

        // Überprüfe, ob der Benutzer wiederhergestellt und aktiv ist
        $this->assertFalse($user->fresh()->trashed());
        $this->assertEquals(ModelStatus::ACTIVE, $user->fresh()->model_status);
    }

    #[Test]
    public function it_can_restore_a_user_to_active_from_inactive_status()
    {
        // Erstelle einen inaktiven Benutzer
        $user = User::factory()->create(['model_status' => ModelStatus::INACTIVE]);

        // Teste die Aktivierung via restore
        Livewire::test(get_class($this->getTestComponent()))
            ->call('restore', $user->id)
            ->assertDispatched('userStatusUpdated')
            ->assertDispatched('update-table');

        // Überprüfe, ob der Benutzer jetzt aktiv ist
        $this->assertEquals(ModelStatus::ACTIVE, $user->fresh()->model_status);
    }

    #[Test]
    public function it_can_restore_a_user_to_archive()
    {
        // Erstelle einen Benutzer und lege ihn in den Papierkorb
        $user = User::factory()->create(['model_status' => ModelStatus::ACTIVE]);
        $user->delete();

        // Teste die Wiederherstellung als archiviert
        Livewire::test(get_class($this->getTestComponent()))
            ->call('restoreToArchive', $user->id)
            ->assertDispatched('userStatusUpdated')
            ->assertDispatched('update-table');

        // Überprüfe, ob der Benutzer wiederhergestellt und archiviert ist
        $this->assertFalse($user->fresh()->trashed());
        $this->assertEquals(ModelStatus::ARCHIVED, $user->fresh()->model_status);
    }

    #[Test]
    public function it_can_restore_a_user_to_inactive()
    {
        // Erstelle einen Benutzer und lege ihn in den Papierkorb
        $user = User::factory()->create(['model_status' => ModelStatus::ACTIVE]);
        $user->delete();

        // Teste die Wiederherstellung als inaktiv
        Livewire::test(get_class($this->getTestComponent()))
            ->call('restoreToInactive', $user->id)
            ->assertDispatched('userStatusUpdated')
            ->assertDispatched('update-table');

        // Überprüfe, ob der Benutzer wiederhergestellt und inaktiv ist
        $this->assertFalse($user->fresh()->trashed());
        $this->assertEquals(ModelStatus::INACTIVE, $user->fresh()->model_status);
    }

    #[Test]
    public function it_can_permanently_delete_a_user()
    {
        // Erstelle einen Benutzer und lege ihn in den Papierkorb
        $user = User::factory()->create(['model_status' => ModelStatus::ACTIVE]);
        $user->delete();

        // Teste das permanente Löschen
        Livewire::test(get_class($this->getTestComponent()))
            ->call('forceDelete', $user->id)
            ->assertDispatched('userStatusUpdated')
            ->assertDispatched('update-table');

        // Überprüfe, ob der Benutzer dauerhaft gelöscht wurde
        $this->assertNull(User::withTrashed()->find($user->id));
    }

    #[Test]
    public function it_can_empty_trash()
    {
        // Erstelle einige Benutzer und lege sie in den Papierkorb
        $user1 = User::factory()->create(['model_status' => ModelStatus::ACTIVE]);
        $user2 = User::factory()->create(['model_status' => ModelStatus::ACTIVE]);
        $user1->delete();
        $user2->delete();

        // Überprüfe, ob die Benutzer im Papierkorb sind
        $this->assertTrue($user1->fresh()->trashed());
        $this->assertTrue($user2->fresh()->trashed());

        // Teste das Leeren des Papierkorbs
        Livewire::test(get_class($this->getTestComponent()))
            ->call('emptyTrash')
            ->assertDispatched('userStatusUpdated')
            ->assertDispatched('update-table');

        // Überprüfe, ob alle Benutzer aus dem Papierkorb gelöscht wurden
        $this->assertEquals(0, User::onlyTrashed()->count());
    }

    #[Test]
    public function it_can_bulk_update_status_to_active()
    {
        // Erstelle einige Benutzer mit unterschiedlichen Status
        $user1 = User::factory()->create(['model_status' => ModelStatus::INACTIVE]);
        $user2 = User::factory()->create(['model_status' => ModelStatus::ARCHIVED]);

        // Teste die Bulk-Aktion
        Livewire::test(get_class($this->getTestComponent()))
            ->set('selectedIds', [$user1->id, $user2->id])
            ->call('bulkUpdateStatus', 'active')
            ->assertDispatched('userStatusUpdated')
            ->assertDispatched('update-table');

        // Überprüfe, ob alle Benutzer aktiv sind
        $this->assertEquals(ModelStatus::ACTIVE, $user1->fresh()->model_status);
        $this->assertEquals(ModelStatus::ACTIVE, $user2->fresh()->model_status);
    }

    #[Test]
    public function it_can_bulk_update_status_to_inactive()
    {
        // Erstelle einige Benutzer mit unterschiedlichen Status
        $user1 = User::factory()->create(['model_status' => ModelStatus::ACTIVE]);
        $user2 = User::factory()->create(['model_status' => ModelStatus::ARCHIVED]);

        // Teste die Bulk-Aktion
        Livewire::test(get_class($this->getTestComponent()))
            ->set('selectedIds', [$user1->id, $user2->id])
            ->call('bulkUpdateStatus', 'inactive')
            ->assertDispatched('userStatusUpdated')
            ->assertDispatched('update-table');

        // Überprüfe, ob alle Benutzer inaktiv sind
        $this->assertEquals(ModelStatus::INACTIVE, $user1->fresh()->model_status);
        $this->assertEquals(ModelStatus::INACTIVE, $user2->fresh()->model_status);
    }

    #[Test]
    public function it_can_bulk_update_status_to_archived()
    {
        // Erstelle einige Benutzer mit unterschiedlichen Status
        $user1 = User::factory()->create(['model_status' => ModelStatus::ACTIVE]);
        $user2 = User::factory()->create(['model_status' => ModelStatus::INACTIVE]);

        // Teste die Bulk-Aktion
        Livewire::test(get_class($this->getTestComponent()))
            ->set('selectedIds', [$user1->id, $user2->id])
            ->call('bulkUpdateStatus', 'archived')
            ->assertDispatched('userStatusUpdated')
            ->assertDispatched('update-table');

        // Überprüfe, ob alle Benutzer archiviert sind
        $this->assertEquals(ModelStatus::ARCHIVED, $user1->fresh()->model_status);
        $this->assertEquals(ModelStatus::ARCHIVED, $user2->fresh()->model_status);
    }

    #[Test]
    public function it_can_bulk_move_to_trash()
    {
        // Erstelle einige Benutzer
        $user1 = User::factory()->create(['model_status' => ModelStatus::ACTIVE]);
        $user2 = User::factory()->create(['model_status' => ModelStatus::INACTIVE]);

        // Teste die Bulk-Aktion
        Livewire::test(get_class($this->getTestComponent()))
            ->set('selectedIds', [$user1->id, $user2->id])
            ->call('bulkUpdateStatus', 'trashed')
            ->assertDispatched('userStatusUpdated')
            ->assertDispatched('update-table');

        // Überprüfe, ob alle Benutzer im Papierkorb sind
        $this->assertTrue($user1->fresh()->trashed());
        $this->assertTrue($user2->fresh()->trashed());
        $this->assertEquals(ModelStatus::TRASHED, $user1->fresh()->model_status);
        $this->assertEquals(ModelStatus::TRASHED, $user2->fresh()->model_status);
    }

    #[Test]
    public function it_can_bulk_restore_to_active()
    {
        // Erstelle einige Benutzer und lege sie in den Papierkorb
        $user1 = User::factory()->create(['model_status' => ModelStatus::ACTIVE]);
        $user2 = User::factory()->create(['model_status' => ModelStatus::ACTIVE]);
        $user1->delete();
        $user2->delete();

        // Teste die Bulk-Aktion
        Livewire::test(get_class($this->getTestComponent()))
            ->set('selectedIds', [$user1->id, $user2->id])
            ->call('bulkUpdateStatus', 'restore_to_active')
            ->assertDispatched('userStatusUpdated')
            ->assertDispatched('update-table');

        // Überprüfe, ob alle Benutzer aktiv sind
        $this->assertFalse($user1->fresh()->trashed());
        $this->assertFalse($user2->fresh()->trashed());
        $this->assertEquals(ModelStatus::ACTIVE, $user1->fresh()->model_status);
        $this->assertEquals(ModelStatus::ACTIVE, $user2->fresh()->model_status);
    }

    #[Test]
    public function it_can_bulk_restore_to_archive()
    {
        // Erstelle einige Benutzer und lege sie in den Papierkorb
        $user1 = User::factory()->create(['model_status' => ModelStatus::ACTIVE]);
        $user2 = User::factory()->create(['model_status' => ModelStatus::ACTIVE]);
        $user1->delete();
        $user2->delete();

        // Teste die Bulk-Aktion
        Livewire::test(get_class($this->getTestComponent()))
            ->set('selectedIds', [$user1->id, $user2->id])
            ->call('bulkUpdateStatus', 'restore_to_archive')
            ->assertDispatched('userStatusUpdated')
            ->assertDispatched('update-table');

        // Überprüfe, ob alle Benutzer archiviert sind
        $this->assertFalse($user1->fresh()->trashed());
        $this->assertFalse($user2->fresh()->trashed());
        $this->assertEquals(ModelStatus::ARCHIVED, $user1->fresh()->model_status);
        $this->assertEquals(ModelStatus::ARCHIVED, $user2->fresh()->model_status);
    }

    #[Test]
    public function it_can_bulk_restore_to_inactive()
    {
        // Erstelle einige Benutzer und lege sie in den Papierkorb
        $user1 = User::factory()->create(['model_status' => ModelStatus::ACTIVE]);
        $user2 = User::factory()->create(['model_status' => ModelStatus::ACTIVE]);
        $user1->delete();
        $user2->delete();

        // Teste die Bulk-Aktion
        Livewire::test(get_class($this->getTestComponent()))
            ->set('selectedIds', [$user1->id, $user2->id])
            ->call('bulkUpdateStatus', 'restore_to_inactive')
            ->assertDispatched('userStatusUpdated')
            ->assertDispatched('update-table');

        // Überprüfe, ob alle Benutzer inaktiv sind
        $this->assertFalse($user1->fresh()->trashed());
        $this->assertFalse($user2->fresh()->trashed());
        $this->assertEquals(ModelStatus::INACTIVE, $user1->fresh()->model_status);
        $this->assertEquals(ModelStatus::INACTIVE, $user2->fresh()->model_status);
    }

    #[Test]
    public function it_can_bulk_force_delete()
    {
        // Erstelle einige Benutzer und lege sie in den Papierkorb
        $user1 = User::factory()->create(['model_status' => ModelStatus::ACTIVE]);
        $user2 = User::factory()->create(['model_status' => ModelStatus::ACTIVE]);
        $user1->delete();
        $user2->delete();

        // Teste die Bulk-Aktion
        Livewire::test(get_class($this->getTestComponent()))
            ->set('selectedIds', [$user1->id, $user2->id])
            ->call('bulkForceDelete')
            ->assertDispatched('userStatusUpdated')
            ->assertDispatched('update-table');

        // Überprüfe, ob alle Benutzer dauerhaft gelöscht wurden
        $this->assertNull(User::withTrashed()->find($user1->id));
        $this->assertNull(User::withTrashed()->find($user2->id));
    }

    #[Test]
    public function it_dispatches_status_events()
    {
        // Erstelle einen Benutzer zum Testen
        $user = User::factory()->create(['model_status' => ModelStatus::INACTIVE]);

        // Aktiviere den Benutzer (dies löst dispatchStatusEvents aus)
        Livewire::test(get_class($this->getTestComponent()))
            ->call('activate', $user->id)
            ->assertDispatched('userStatusUpdated')
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
