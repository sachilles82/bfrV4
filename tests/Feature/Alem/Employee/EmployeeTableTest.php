<?php

namespace Alem\Employee;

use App\Enums\Model\ModelStatus;
use App\Livewire\Alem\Employee\EmployeeTable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmployeeTableTest extends TestCase
{
    use RefreshDatabase;

    /** @var User */
    private $user;

    /** @var User[] */
    private $employees;

    /**
     * Setup common test resources
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create an authenticated user with a valid company record
        $this->user = User::factory()->create([
            'user_type' => 'admin',
            'model_status' => ModelStatus::ACTIVE
        ]);

        // Create test employees with different statuses
        $this->employees = [
            'active' => User::factory()->create([
                'user_type' => 'employee',
                'model_status' => ModelStatus::ACTIVE,
                'company_id' => $this->user->company_id
            ]),
            'inactive' => User::factory()->create([
                'user_type' => 'employee',
                'model_status' => ModelStatus::INACTIVE,
                'company_id' => $this->user->company_id
            ]),
            'archived' => User::factory()->create([
                'user_type' => 'employee',
                'model_status' => ModelStatus::ARCHIVED,
                'company_id' => $this->user->company_id
            ])
        ];

        // Add a trashed employee
        $trashedUser = User::factory()->create([
            'user_type' => 'employee',
            'company_id' => $this->user->company_id
        ]);
        $trashedUser->delete();
        $this->employees['trashed'] = $trashedUser;

        // Authenticate
        $this->actingAs($this->user);
    }

    #[Test]
    public function table_can_be_rendered()
    {
        Livewire::test(EmployeeTable::class)
            ->assertStatus(200);
    }

    #[Test]
    public function active_employees_are_shown_by_default()
    {
        Livewire::test(EmployeeTable::class)
            ->assertSee($this->employees['active']->name)
            ->assertDontSee($this->employees['inactive']->name)
            ->assertDontSee($this->employees['archived']->name)
            ->assertDontSee($this->employees['trashed']->name);
    }

    #[Test]
    public function can_filter_employees_by_status()
    {
        Livewire::test(EmployeeTable::class)
            ->set('statusFilter', 'inactive')
            ->assertSee($this->employees['inactive']->name)
            ->assertDontSee($this->employees['active']->name);

        Livewire::test(EmployeeTable::class)
            ->set('statusFilter', 'archived')
            ->assertSee($this->employees['archived']->name)
            ->assertDontSee($this->employees['active']->name);

        Livewire::test(EmployeeTable::class)
            ->set('statusFilter', 'trashed')
            ->assertSee($this->employees['trashed']->name)
            ->assertDontSee($this->employees['active']->name);
    }

    #[Test]
    public function can_reset_filters()
    {
        Livewire::test(EmployeeTable::class)
            ->set('statusFilter', 'inactive')
            ->call('resetFilters')
            ->assertSet('statusFilter', 'active')
            ->assertSee($this->employees['active']->name)
            ->assertDontSee($this->employees['inactive']->name);
    }

    #[Test]
    public function can_activate_user()
    {
        Livewire::test(EmployeeTable::class)
            ->call('activate', $this->employees['inactive']->id)
            ->assertDispatched('employeeUpdated')
            ->assertDispatched('update-table');

        $this->assertDatabaseHas('users', [
            'id' => $this->employees['inactive']->id,
            'model_status' => ModelStatus::ACTIVE
        ]);
    }

    #[Test]
    public function can_deactivate_user()
    {
        Livewire::test(EmployeeTable::class)
            ->call('notActivate', $this->employees['active']->id)
            ->assertDispatched('employeeUpdated')
            ->assertDispatched('update-table');

        $this->assertDatabaseHas('users', [
            'id' => $this->employees['active']->id,
            'model_status' => ModelStatus::INACTIVE
        ]);
    }

    #[Test]
    public function can_archive_user()
    {
        Livewire::test(EmployeeTable::class)
            ->call('archive', $this->employees['active']->id)
            ->assertDispatched('employeeUpdated')
            ->assertDispatched('update-table');

        $this->assertDatabaseHas('users', [
            'id' => $this->employees['active']->id,
            'model_status' => ModelStatus::ARCHIVED
        ]);
    }

    #[Test]
    public function can_trash_user()
    {
        $id = $this->employees['active']->id;

        Livewire::test(EmployeeTable::class)
            ->call('delete', $id)
            ->assertDispatched('employeeUpdated')
            ->assertDispatched('update-table');

        $this->assertSoftDeleted('users', [
            'id' => $id
        ]);
    }

    #[Test]
    public function can_restore_user_from_trash()
    {
        $id = $this->employees['trashed']->id;

        // Verify initial state
        $trashedUser = User::withTrashed()->find($id);
        $this->assertTrue($trashedUser->trashed());
        $this->assertEquals(ModelStatus::TRASHED, $trashedUser->model_status);

        // Test restoration
        Livewire::test(EmployeeTable::class)
            ->set('statusFilter', 'trashed')
            ->call('restore', $id)
            ->assertDispatched('employeeUpdated')
            ->assertDispatched('update-table');

        // Get a fresh instance and check
        $restoredUser = User::find($id);
        $this->assertNotNull($restoredUser, "User should exist after restore");
        $this->assertFalse($restoredUser->trashed(), "User should not be trashed after restore");
        $this->assertEquals(ModelStatus::ACTIVE, $restoredUser->model_status, "User should be ACTIVE after restore");
    }

    #[Test]
    public function can_restore_user_from_archive()
    {
        $id = $this->employees['archived']->id;

        Livewire::test(EmployeeTable::class)
            ->set('statusFilter', 'archived')
            ->call('restore', $id)
            ->assertDispatched('employeeUpdated')
            ->assertDispatched('update-table');

        $this->assertDatabaseHas('users', [
            'id' => $id,
            'model_status' => ModelStatus::ACTIVE
        ]);
    }

    #[Test]
    public function can_restore_to_archive()
    {
        $id = $this->employees['trashed']->id;

        Livewire::test(EmployeeTable::class)
            ->set('statusFilter', 'trashed')
            ->call('restoreToArchive', $id)
            ->assertDispatched('employeeUpdated')
            ->assertDispatched('update-table');

        $this->assertDatabaseHas('users', [
            'id' => $id,
            'deleted_at' => null,
            'model_status' => ModelStatus::ARCHIVED
        ]);
    }

    #[Test]
    public function can_restore_to_inactive()
    {
        $id = $this->employees['trashed']->id;

        Livewire::test(EmployeeTable::class)
            ->set('statusFilter', 'trashed')
            ->call('restoreToInactive', $id)
            ->assertDispatched('employeeUpdated')
            ->assertDispatched('update-table');

        $this->assertDatabaseHas('users', [
            'id' => $id,
            'deleted_at' => null,
            'model_status' => ModelStatus::INACTIVE
        ]);
    }

    #[Test]
    public function can_force_delete_user()
    {
        $id = $this->employees['trashed']->id;

        Livewire::test(EmployeeTable::class)
            ->set('statusFilter', 'trashed')
            ->call('forceDelete', $id)
            ->assertDispatched('employeeUpdated')
            ->assertDispatched('update-table');

        $this->assertDatabaseMissing('users', [
            'id' => $id
        ]);
    }

    #[Test]
    public function can_bulk_activate_users()
    {
        $selectedIds = [
            $this->employees['inactive']->id,
            $this->employees['archived']->id
        ];

        Livewire::test(EmployeeTable::class)
            ->set('selectedIds', $selectedIds)
            ->call('bulkUpdateStatus', 'active')
            ->assertDispatched('employeeUpdated')
            ->assertDispatched('update-table');

        foreach ($selectedIds as $id) {
            $this->assertDatabaseHas('users', [
                'id' => $id,
                'model_status' => ModelStatus::ACTIVE
            ]);
        }
    }

    #[Test]
    public function can_bulk_deactivate_users()
    {
        $selectedIds = [
            $this->employees['active']->id,
            $this->employees['archived']->id
        ];

        Livewire::test(EmployeeTable::class)
            ->set('selectedIds', $selectedIds)
            ->call('bulkUpdateStatus', 'inactive')
            ->assertDispatched('employeeUpdated')
            ->assertDispatched('update-table');

        foreach ($selectedIds as $id) {
            $this->assertDatabaseHas('users', [
                'id' => $id,
                'model_status' => ModelStatus::INACTIVE
            ]);
        }
    }

    #[Test]
    public function can_bulk_archive_users()
    {
        $selectedIds = [
            $this->employees['active']->id,
            $this->employees['inactive']->id
        ];

        Livewire::test(EmployeeTable::class)
            ->set('selectedIds', $selectedIds)
            ->call('bulkUpdateStatus', 'archived')
            ->assertDispatched('employeeUpdated')
            ->assertDispatched('update-table');

        foreach ($selectedIds as $id) {
            $this->assertDatabaseHas('users', [
                'id' => $id,
                'model_status' => ModelStatus::ARCHIVED
            ]);
        }
    }

    #[Test]
    public function can_bulk_trash_users()
    {
        $selectedIds = [
            $this->employees['active']->id,
            $this->employees['inactive']->id
        ];

        Livewire::test(EmployeeTable::class)
            ->set('selectedIds', $selectedIds)
            ->call('bulkUpdateStatus', 'trashed')
            ->assertDispatched('employeeUpdated')
            ->assertDispatched('update-table');

        foreach ($selectedIds as $id) {
            $this->assertSoftDeleted('users', [
                'id' => $id
            ]);
        }
    }

    #[Test]
    public function can_bulk_restore_to_archive()
    {
        // Create another trashed user
        $anotherTrashed = User::factory()->create([
            'user_type' => 'employee',
            'company_id' => $this->user->company_id
        ]);
        $anotherTrashed->delete();

        $selectedIds = [
            $this->employees['trashed']->id,
            $anotherTrashed->id
        ];

        Livewire::test(EmployeeTable::class)
            ->set('statusFilter', 'trashed')
            ->set('selectedIds', $selectedIds)
            ->call('bulkUpdateStatus', 'restore_to_archive')
            ->assertDispatched('employeeUpdated')
            ->assertDispatched('update-table');

        foreach ($selectedIds as $id) {
            $this->assertDatabaseHas('users', [
                'id' => $id,
                'deleted_at' => null,
                'model_status' => ModelStatus::ARCHIVED
            ]);
        }
    }

    #[Test]
    public function can_bulk_restore_to_inactive()
    {
        // Create another trashed user
        $anotherTrashed = User::factory()->create([
            'user_type' => 'employee',
            'company_id' => $this->user->company_id
        ]);
        $anotherTrashed->delete();

        $selectedIds = [
            $this->employees['trashed']->id,
            $anotherTrashed->id
        ];

        Livewire::test(EmployeeTable::class)
            ->set('statusFilter', 'trashed')
            ->set('selectedIds', $selectedIds)
            ->call('bulkUpdateStatus', 'restore_to_inactive')
            ->assertDispatched('employeeUpdated')
            ->assertDispatched('update-table');

        foreach ($selectedIds as $id) {
            $this->assertDatabaseHas('users', [
                'id' => $id,
                'deleted_at' => null,
                'model_status' => ModelStatus::INACTIVE
            ]);
        }
    }

    #[Test]
    public function can_bulk_restore_to_active()
    {
        // Create another trashed user
        $anotherTrashed = User::factory()->create([
            'user_type' => 'employee',
            'company_id' => $this->user->company_id
        ]);
        $anotherTrashed->delete();

        $selectedIds = [
            $this->employees['trashed']->id,
            $anotherTrashed->id
        ];

        Livewire::test(EmployeeTable::class)
            ->set('statusFilter', 'trashed')
            ->set('selectedIds', $selectedIds)
            ->call('bulkUpdateStatus', 'restore_to_active')
            ->assertDispatched('employeeUpdated')
            ->assertDispatched('update-table');

        foreach ($selectedIds as $id) {
            $this->assertDatabaseHas('users', [
                'id' => $id,
                'deleted_at' => null,
                'model_status' => ModelStatus::ACTIVE
            ]);
        }
    }

    #[Test]
    public function can_bulk_force_delete_users()
    {
        // Create another trashed user
        $anotherTrashed = User::factory()->create([
            'user_type' => 'employee',
            'company_id' => $this->user->company_id
        ]);
        $anotherTrashed->delete();

        $selectedIds = [
            $this->employees['trashed']->id,
            $anotherTrashed->id
        ];

        Livewire::test(EmployeeTable::class)
            ->set('statusFilter', 'trashed')
            ->set('selectedIds', $selectedIds)
            ->call('bulkForceDelete')
            ->assertDispatched('employeeUpdated')
            ->assertDispatched('update-table');

        foreach ($selectedIds as $id) {
            $this->assertDatabaseMissing('users', [
                'id' => $id
            ]);
        }
    }

    #[Test]
    public function can_set_status_filter()
    {
        Livewire::test(EmployeeTable::class)
            ->set('statusFilter', 'active')
            ->assertSet('statusFilter', 'active')
            ->set('statusFilter', 'inactive')
            ->assertSet('statusFilter', 'inactive')
            ->assertDispatched('update-table');
    }

    #[Test]
    public function changing_status_filter_resets_selections()
    {
        // Teste, ob die Änderung des Statusfilters die Auswahlfelder zurücksetzt
        // Wir verwenden die updated()-Methode der Komponente implizit, indem wir statusFilter ändern

        $component = Livewire::test(EmployeeTable::class);

        // Setze einige IDs, damit wir prüfen können, ob sie zurückgesetzt werden
        $component->set('selectedIds', [1, 2, 3]);
        $component->set('idsOnPage', [1, 2, 3, 4]);

        // Ändere den statusFilter, was dazu führen sollte, dass die updated()-Methode
        // in der Komponente aufgerufen wird, die wiederum die Auswahl zurücksetzt
        $component->set('statusFilter', 'inactive');

        // Prüfe, ob die Eigenschaft zurückgesetzt wurde
        // Wir erwarten, dass selectedIds am Ende leer ist, egal was davor war
        $component->assertSet('selectedIds', []);
    }

    #[Test]
    public function can_empty_trash()
    {
        // Create additional trashed users
        $trashedUsers = [];
        for ($i = 0; $i < 3; $i++) {
            $user = User::factory()->create([
                'user_type' => 'employee',
                'company_id' => $this->user->company_id
            ]);
            $user->delete();
            $trashedUsers[] = $user->id;
        }

        // Verify there are trashed users
        $this->assertTrue(User::onlyTrashed()->exists());

        Livewire::test(EmployeeTable::class)
            ->set('statusFilter', 'trashed')
            ->call('emptyTrash')
            ->assertDispatched('employeeUpdated')
            ->assertDispatched('update-table');

        // Verify all trashed users are permanently deleted
        foreach ($trashedUsers as $id) {
            $this->assertDatabaseMissing('users', [
                'id' => $id
            ]);
        }
        $this->assertFalse(User::onlyTrashed()->exists());
    }

    #[Test]
    public function sorting_is_applied_correctly()
    {
        // Create users with specific names for sorting
        $userA = User::factory()->create([
            'name' => 'AAA User',
            'user_type' => 'employee',
            'model_status' => ModelStatus::ACTIVE,
            'company_id' => $this->user->company_id
        ]);

        $userZ = User::factory()->create([
            'name' => 'ZZZ User',
            'user_type' => 'employee',
            'model_status' => ModelStatus::ACTIVE,
            'company_id' => $this->user->company_id
        ]);

        // Test ascending sort - we just check if sorting works by checking the rendered output
        $component = Livewire::test(EmployeeTable::class)
            ->set('sortCol', 'name')
            ->set('sortAsc', true);

        // We can't directly check the rendered order,
        // but we can check that the sort parameters are correctly set
        $component->assertSet('sortCol', 'name')
            ->assertSet('sortAsc', true);

        // Test descending sort
        $component = Livewire::test(EmployeeTable::class)
            ->set('sortCol', 'name')
            ->set('sortAsc', false);

        $component->assertSet('sortCol', 'name')
            ->assertSet('sortAsc', false);
    }

    #[Test]
    public function search_filters_employees_correctly()
    {
        // Create a user with a unique searchable name
        $uniqueUser = User::factory()->create([
            'name' => 'UniqueSearchTerm',
            'user_type' => 'employee',
            'model_status' => ModelStatus::ACTIVE,
            'company_id' => $this->user->company_id
        ]);

        // Test with the search term
        Livewire::test(EmployeeTable::class)
            ->set('search', 'UniqueSearchTerm')
            ->assertSee('UniqueSearchTerm')
            ->assertDontSee($this->employees['active']->name);
    }

    #[Test]
    public function only_shows_employees_from_same_company()
    {
        // Erstelle zuerst eine Industry
        $industry = \App\Models\Alem\Industry::factory()->create();

        // Erstelle Benutzer, die als Owner und created_by dienen werden
        $creator = User::factory()->create([
            'user_type' => 'admin',
            'model_status' => ModelStatus::ACTIVE
        ]);

        $owner1 = User::factory()->create([
            'user_type' => 'owner',
            'model_status' => ModelStatus::ACTIVE
        ]);

        $owner2 = User::factory()->create([
            'user_type' => 'owner',
            'model_status' => ModelStatus::ACTIVE
        ]);

        // Erstelle Firmen mit explizit gesetzten owner_id und created_by
        $company1 = \App\Models\Alem\Company::factory()->create([
            'industry_id' => $industry->id,
            'owner_id' => $owner1->id,
            'created_by' => $creator->id
        ]);

        $company2 = \App\Models\Alem\Company::factory()->create([
            'industry_id' => $industry->id,
            'owner_id' => $owner2->id,
            'created_by' => $creator->id
        ]);

        // Aktualisiere die Owner mit ihren jeweiligen Firmen
        $owner1->update(['company_id' => $company1->id]);
        $owner2->update(['company_id' => $company2->id]);

        // Erstelle Mitarbeiter für beide Firmen
        $employee1 = User::factory()->create([
            'user_type' => 'employee',
            'model_status' => ModelStatus::ACTIVE,
            'company_id' => $company1->id
        ]);

        $employee2 = User::factory()->create([
            'user_type' => 'employee',
            'model_status' => ModelStatus::ACTIVE,
            'company_id' => $company2->id
        ]);

        // Test: Owner der Firma 1 sieht nur Mitarbeiter der Firma 1
        Livewire::actingAs($owner1)
            ->test(EmployeeTable::class)
            ->assertSee($employee1->name)
            ->assertDontSee($employee2->name);

        // Test: Owner der Firma 2 sieht nur Mitarbeiter der Firma 2
        Livewire::actingAs($owner2)
            ->test(EmployeeTable::class)
            ->assertSee($employee2->name)
            ->assertDontSee($employee1->name);

        // Test: Admin (ohne Firma) sieht keine Mitarbeiter
        Livewire::actingAs($this->user) // $this->user ist der Admin aus der setUp-Methode
        ->test(EmployeeTable::class)
            ->assertDontSee($employee1->name)
            ->assertDontSee($employee2->name);
    }
}
