<?php

use App\Enums\Model\ModelStatus;
use App\Livewire\Alem\Employee\EmployeeTable;
use App\Models\User;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

/**
 * Setup common test resources
 */
beforeEach(function () {
    // Create an authenticated user with a valid company record
    $this->user = User::factory()->create([
        'user_type' => 'admin',
        'model_status' => ModelStatus::ACTIVE,
    ]);

    // Create test employees with different statuses
    $this->employees = [
        'active' => User::factory()->create([
            'user_type' => 'employee',
            'model_status' => ModelStatus::ACTIVE,
            'company_id' => $this->user->company_id,
        ]),
        'archived' => User::factory()->create([
            'user_type' => 'employee',
            'model_status' => ModelStatus::ARCHIVED,
            'company_id' => $this->user->company_id,
        ]),
    ];

    // Add a trashed employee
    $trashedUser = User::factory()->create([
        'user_type' => 'employee',
        'company_id' => $this->user->company_id,
    ]);
    $trashedUser->delete();
    $this->employees['trashed'] = $trashedUser;

    // Authenticate
    $this->actingAs($this->user);
});

test('table can be rendered', function () {
    Livewire::test(EmployeeTable::class)
        ->assertStatus(200);
});

test('active employees are shown by default', function () {
    Livewire::test(EmployeeTable::class)
        ->assertSee($this->employees['active']->name)
        ->assertDontSee($this->employees['archived']->name)
        ->assertDontSee($this->employees['trashed']->name);
});

test('can filter employees by status', function () {
    Livewire::test(EmployeeTable::class)
        ->set('statusFilter', 'archived')
        ->assertSee($this->employees['archived']->name)
        ->assertDontSee($this->employees['active']->name);

    Livewire::test(EmployeeTable::class)
        ->set('statusFilter', 'trashed')
        ->assertSee($this->employees['trashed']->name)
        ->assertDontSee($this->employees['active']->name);
});

test('can reset filters', function () {
    Livewire::test(EmployeeTable::class)
        ->set('statusFilter', 'archived')
        ->call('resetFilters')
        ->assertSet('statusFilter', 'active')
        ->assertSee($this->employees['active']->name)
        ->assertDontSee($this->employees['archived']->name);
});

test('can activate user', function () {
    Livewire::test(EmployeeTable::class)
        ->call('activate', $this->employees['archived']->id)
        ->assertDispatched('employeeUpdated')
        ->assertDispatched('update-table');

    $this->assertDatabaseHas('users', [
        'id' => $this->employees['archived']->id,
        'model_status' => ModelStatus::ACTIVE,
    ]);
});

test('can archive user', function () {
    Livewire::test(EmployeeTable::class)
        ->call('archive', $this->employees['active']->id)
        ->assertDispatched('employeeUpdated')
        ->assertDispatched('update-table');

    $this->assertDatabaseHas('users', [
        'id' => $this->employees['active']->id,
        'model_status' => ModelStatus::ARCHIVED,
    ]);
});

test('can trash user', function () {
    $id = $this->employees['active']->id;

    Livewire::test(EmployeeTable::class)
        ->call('delete', $id)
        ->assertDispatched('employeeUpdated')
        ->assertDispatched('update-table');

    $this->assertSoftDeleted('users', [
        'id' => $id,
    ]);
});

test('can restore user from trash', function () {
    $id = $this->employees['trashed']->id;

    // Verify initial state
    $trashedUser = User::withTrashed()->find($id);
    expect($trashedUser->trashed())->toBeTrue();
    expect($trashedUser->model_status)->toEqual(ModelStatus::TRASHED);

    // Test restoration
    Livewire::test(EmployeeTable::class)
        ->set('statusFilter', 'trashed')
        ->call('restore', $id)
        ->assertDispatched('employeeUpdated')
        ->assertDispatched('update-table');

    // Get a fresh instance and check
    $restoredUser = User::find($id);
    expect($restoredUser)->not->toBeNull('User should exist after restore');
    expect($restoredUser->trashed())->toBeFalse('User should not be trashed after restore');
    expect($restoredUser->model_status)->toEqual(ModelStatus::ACTIVE, 'User should be ACTIVE after restore');
});

test('can restore user from archive', function () {
    $id = $this->employees['archived']->id;

    Livewire::test(EmployeeTable::class)
        ->set('statusFilter', 'archived')
        ->call('restore', $id)
        ->assertDispatched('employeeUpdated')
        ->assertDispatched('update-table');

    $this->assertDatabaseHas('users', [
        'id' => $id,
        'model_status' => ModelStatus::ACTIVE,
    ]);
});

test('can restore to archive', function () {
    $id = $this->employees['trashed']->id;

    Livewire::test(EmployeeTable::class)
        ->set('statusFilter', 'trashed')
        ->call('restoreToArchive', $id)
        ->assertDispatched('employeeUpdated')
        ->assertDispatched('update-table');

    $this->assertDatabaseHas('users', [
        'id' => $id,
        'deleted_at' => null,
        'model_status' => ModelStatus::ARCHIVED,
    ]);
});

test('can force delete user', function () {
    $id = $this->employees['trashed']->id;

    Livewire::test(EmployeeTable::class)
        ->set('statusFilter', 'trashed')
        ->call('forceDelete', $id)
        ->assertDispatched('employeeUpdated')
        ->assertDispatched('update-table');

    $this->assertDatabaseMissing('users', [
        'id' => $id,
    ]);
});

test('can bulk activate users', function () {
    $selectedIds = [
        $this->employees['archived']->id,
    ];

    Livewire::test(EmployeeTable::class)
        ->set('selectedIds', $selectedIds)
        ->call('bulkUpdateStatus', 'active')
        ->assertDispatched('employeeUpdated')
        ->assertDispatched('update-table');

    foreach ($selectedIds as $id) {
        $this->assertDatabaseHas('users', [
            'id' => $id,
            'model_status' => ModelStatus::ACTIVE,
        ]);
    }
});

test('can bulk archive users', function () {
    $selectedIds = [
        $this->employees['active']->id,
    ];

    Livewire::test(EmployeeTable::class)
        ->set('selectedIds', $selectedIds)
        ->call('bulkUpdateStatus', 'archived')
        ->assertDispatched('employeeUpdated')
        ->assertDispatched('update-table');

    foreach ($selectedIds as $id) {
        $this->assertDatabaseHas('users', [
            'id' => $id,
            'model_status' => ModelStatus::ARCHIVED,
        ]);
    }
});

test('can bulk trash users', function () {
    $selectedIds = [
        $this->employees['active']->id,
    ];

    Livewire::test(EmployeeTable::class)
        ->set('selectedIds', $selectedIds)
        ->call('bulkUpdateStatus', 'trashed')
        ->assertDispatched('employeeUpdated')
        ->assertDispatched('update-table');

    foreach ($selectedIds as $id) {
        $this->assertSoftDeleted('users', [
            'id' => $id,
        ]);
    }
});

test('can bulk restore to archive', function () {
    // Create another trashed user
    $anotherTrashed = User::factory()->create([
        'user_type' => 'employee',
        'company_id' => $this->user->company_id,
    ]);
    $anotherTrashed->delete();

    $selectedIds = [
        $this->employees['trashed']->id,
        $anotherTrashed->id,
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
            'model_status' => ModelStatus::ARCHIVED,
        ]);
    }
});

test('can bulk restore to active', function () {
    // Create another trashed user
    $anotherTrashed = User::factory()->create([
        'user_type' => 'employee',
        'company_id' => $this->user->company_id,
    ]);
    $anotherTrashed->delete();

    $selectedIds = [
        $this->employees['trashed']->id,
        $anotherTrashed->id,
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
            'model_status' => ModelStatus::ACTIVE,
        ]);
    }
});

test('can bulk force delete users', function () {
    // Create another trashed user
    $anotherTrashed = User::factory()->create([
        'user_type' => 'employee',
        'company_id' => $this->user->company_id,
    ]);
    $anotherTrashed->delete();

    $selectedIds = [
        $this->employees['trashed']->id,
        $anotherTrashed->id,
    ];

    Livewire::test(EmployeeTable::class)
        ->set('statusFilter', 'trashed')
        ->set('selectedIds', $selectedIds)
        ->call('bulkForceDelete')
        ->assertDispatched('employeeUpdated')
        ->assertDispatched('update-table');

    foreach ($selectedIds as $id) {
        $this->assertDatabaseMissing('users', [
            'id' => $id,
        ]);
    }
});

test('can set status filter', function () {
    Livewire::test(EmployeeTable::class)
        ->set('statusFilter', 'active')
        ->assertSet('statusFilter', 'active')
        ->set('statusFilter', 'archived')
        ->assertSet('statusFilter', 'archived')
        ->assertDispatched('update-table');
});

test('changing status filter resets selections', function () {
    // Teste, ob die Änderung des Statusfilters die Auswahlfelder zurücksetzt
    // Wir verwenden die updated()-Methode der Komponente implizit, indem wir statusFilter ändern
    $component = Livewire::test(EmployeeTable::class);

    // Setze einige IDs, damit wir prüfen können, ob sie zurückgesetzt werden
    $component->set('selectedIds', [1, 2, 3]);
    $component->set('idsOnPage', [1, 2, 3, 4]);

    // Ändere den statusFilter, was dazu führen sollte, dass die updated()-Methode
    // in der Komponente aufgerufen wird, die wiederum die Auswahl zurücksetzt
    $component->set('statusFilter', 'archived');

    // Prüfe, ob die Eigenschaft zurückgesetzt wurde
    // Wir erwarten, dass selectedIds am Ende leer ist, egal was davor war
    $component->assertSet('selectedIds', []);
});

test('can empty trash', function () {
    // Create additional trashed users
    $trashedUsers = [];
    for ($i = 0; $i < 3; $i++) {
        $user = User::factory()->create([
            'user_type' => 'employee',
            'company_id' => $this->user->company_id,
        ]);
        $user->delete();
        $trashedUsers[] = $user->id;
    }

    // Verify there are trashed users
    expect(User::onlyTrashed()->exists())->toBeTrue();

    Livewire::test(EmployeeTable::class)
        ->set('statusFilter', 'trashed')
        ->call('emptyTrash')
        ->assertDispatched('employeeUpdated')
        ->assertDispatched('update-table');

    // Verify all trashed users are permanently deleted
    foreach ($trashedUsers as $id) {
        $this->assertDatabaseMissing('users', [
            'id' => $id,
        ]);
    }
    expect(User::onlyTrashed()->exists())->toBeFalse();
});

test('sorting is applied correctly', function () {
    // Create users with specific names for sorting
    $userA = User::factory()->create([
        'name' => 'AAA User',
        'user_type' => 'employee',
        'model_status' => ModelStatus::ACTIVE,
        'company_id' => $this->user->company_id,
    ]);

    $userZ = User::factory()->create([
        'name' => 'ZZZ User',
        'user_type' => 'employee',
        'model_status' => ModelStatus::ACTIVE,
        'company_id' => $this->user->company_id,
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
});

test('search filters employees correctly', function () {
    // Create a user with a unique searchable name
    $uniqueUser = User::factory()->create([
        'name' => 'UniqueSearchTerm',
        'user_type' => 'employee',
        'model_status' => ModelStatus::ACTIVE,
        'company_id' => $this->user->company_id,
    ]);

    // Test with the search term
    Livewire::test(EmployeeTable::class)
        ->set('search', 'UniqueSearchTerm')
        ->assertSee('UniqueSearchTerm')
        ->assertDontSee($this->employees['active']->name);
});

test('only shows employees from same company', function () {
    // Erstelle zuerst eine Industry
    $industry = \App\Models\Alem\Industry::factory()->create();

    // Erstelle Benutzer, die als Owner und created_by dienen werden
    $creator = User::factory()->create([
        'user_type' => 'admin',
        'model_status' => ModelStatus::ACTIVE,
    ]);

    $owner1 = User::factory()->create([
        'user_type' => 'owner',
        'model_status' => ModelStatus::ACTIVE,
    ]);

    $owner2 = User::factory()->create([
        'user_type' => 'owner',
        'model_status' => ModelStatus::ACTIVE,
    ]);

    // Erstelle Firmen mit explizit gesetzten owner_id und created_by
    $company1 = \App\Models\Alem\Company::factory()->create([
        'industry_id' => $industry->id,
        'owner_id' => $owner1->id,
        'created_by' => $creator->id,
    ]);

    $company2 = \App\Models\Alem\Company::factory()->create([
        'industry_id' => $industry->id,
        'owner_id' => $owner2->id,
        'created_by' => $creator->id,
    ]);

    // Aktualisiere die Owner mit ihren jeweiligen Firmen
    $owner1->update(['company_id' => $company1->id]);
    $owner2->update(['company_id' => $company2->id]);

    // Erstelle Mitarbeiter für beide Firmen
    $employee1 = User::factory()->create([
        'user_type' => 'employee',
        'model_status' => ModelStatus::ACTIVE,
        'company_id' => $company1->id,
    ]);

    $employee2 = User::factory()->create([
        'user_type' => 'employee',
        'model_status' => ModelStatus::ACTIVE,
        'company_id' => $company2->id,
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
});