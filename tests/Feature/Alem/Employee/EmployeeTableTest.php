<?php

use App\Enums\Employee\EmployeeStatus as EmpStatusEnum;
use App\Enums\Model\ModelStatus;
use App\Enums\Role\RoleHasAccessTo;
use App\Enums\Role\RoleVisibility;
use App\Enums\User\UserType;
use App\Livewire\Alem\Employee\EmployeeTable;
use App\Models\Alem\Company;
use App\Models\Alem\Department;
use App\Models\Alem\Employee;
use App\Models\Alem\Industry;
use App\Models\Alem\QuickCrud\Profession;
use App\Models\Alem\QuickCrud\Stage;
use App\Models\Spatie\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

// Alias hinzugefügt
// Für DB::raw

uses(RefreshDatabase::class);

// Hilfsfunktion zum Erstellen eines Mitarbeiters mit Beziehungen
function createEmployeeWithRelations(User $owner, Company $company, Team $team, Department $department, Profession $profession, Stage $stage, Role $role, array $userAttributes = [], array $employeeAttributes = []): User
{
    // Benutzer erstellen
    $employeeUser = User::factory()->create(array_merge([
        'user_type' => UserType::Employee,
        'model_status' => ModelStatus::ACTIVE,
        'company_id' => $company->id,
        'department_id' => $department->id,
        'created_by' => $owner->id, // Setze den Ersteller
    ], $userAttributes));

    // Mitarbeiterdatensatz erstellen
    Employee::factory()->create(array_merge([
        'user_id' => $employeeUser->id,
        'employee_status' => EmpStatusEnum::EMPLOYED, // Standard Employee Status
        'profession_id' => $profession->id,
        'stage_id' => $stage->id,
    ], $employeeAttributes));

    // Benutzer dem Team hinzufügen
    $team->users()->attach($employeeUser, ['role' => 'member']); // Annahme: Standardrolle 'member'
    $employeeUser->switchTeam($team); // Aktuelles Team setzen

    // Rolle zuweisen
    $employeeUser->assignRole($role);

    return $employeeUser->load('employee'); // Mitarbeiterdaten laden
}

// Setup für jeden Test
beforeEach(function () {
    // 1. Authentifizierten Benutzer (Owner/Admin) erstellen
    $this->owner = User::factory()->create([
        'user_type' => UserType::Owner, // Oder Admin, je nach Berechtigungslogik
        'model_status' => ModelStatus::ACTIVE,
    ]);

    // 2. Industry erstellen
    $industry = Industry::factory()->create();

    // 3. Company für den Owner erstellen
    $this->company = Company::factory()->create([
        'owner_id' => $this->owner->id,
        'created_by' => $this->owner->id, // Owner ist auch Ersteller
        'industry_id' => $industry->id,
    ]);
    // Owner der Company zuweisen
    $this->owner->update(['company_id' => $this->company->id]);

    // 4. Team für den Owner erstellen und als aktuelles Team setzen
    $this->team = Team::factory()->create([
        'user_id' => $this->owner->id,
        'company_id' => $this->company->id,
        'personal_team' => false, // Oder true, je nach Bedarf
    ]);
    $this->owner->ownedTeams()->save($this->team);
    $this->owner->switchTeam($this->team);
    $this->owner->save(); // Speichern nach Teamwechsel

    // 5. Notwendige Stammdaten für Mitarbeiter erstellen
    $this->department = Department::factory()->create(['company_id' => $this->company->id, 'team_id' => $this->team->id, 'created_by' => $this->owner->id]);
    $this->profession = Profession::factory()->create(['company_id' => $this->company->id, 'created_by' => $this->owner->id]);
    $this->stage = Stage::factory()->create(['company_id' => $this->company->id, 'created_by' => $this->owner->id]);

    // 6. Rolle für Mitarbeiter erstellen (sichtbar, EmployeePanel, von Firma oder System)
    $this->employeeRole = Role::factory()->create([
        'name' => 'Standard Employee Role',
        'access' => RoleHasAccessTo::EmployeePanel,
        'visible' => RoleVisibility::Visible,
        'company_id' => $this->company->id, // Erstellt von der Firma
        'created_by' => $this->owner->id,
    ]);

    // 7. Test-Mitarbeiter mit unterschiedlichen Status erstellen
    $this->employees = [];

    // Aktiver Mitarbeiter
    $this->employees['active_employed'] = createEmployeeWithRelations(
        $this->owner, $this->company, $this->team, $this->department, $this->profession, $this->stage, $this->employeeRole,
        ['name' => 'Active Employed', 'model_status' => ModelStatus::ACTIVE],
        ['employee_status' => EmpStatusEnum::EMPLOYED]
    );

    // Aktiver Mitarbeiter (Probation)
    $this->employees['active_probation'] = createEmployeeWithRelations(
        $this->owner, $this->company, $this->team, $this->department, $this->profession, $this->stage, $this->employeeRole,
        ['name' => 'Active Probation', 'model_status' => ModelStatus::ACTIVE],
        ['employee_status' => EmpStatusEnum::PROBATION]
    );

    // Aktiver Mitarbeiter (Onboarding)
    $this->employees['active_onboarding'] = createEmployeeWithRelations(
        $this->owner, $this->company, $this->team, $this->department, $this->profession, $this->stage, $this->employeeRole,
        ['name' => 'Active Onboarding', 'model_status' => ModelStatus::ACTIVE],
        ['employee_status' => EmpStatusEnum::ONBOARDING]
    );

    // Archivierter Mitarbeiter
    $this->employees['archived'] = createEmployeeWithRelations(
        $this->owner, $this->company, $this->team, $this->department, $this->profession, $this->stage, $this->employeeRole,
        ['name' => 'Archived User', 'model_status' => ModelStatus::ARCHIVED],
        ['employee_status' => EmpStatusEnum::EMPLOYED] // EmployeeStatus kann beliebig sein
    );

    // Gelöschter (Trashed) Mitarbeiter
    $trashedUser = createEmployeeWithRelations(
        $this->owner, $this->company, $this->team, $this->department, $this->profession, $this->stage, $this->employeeRole,
        ['name' => 'Trashed User'],
        ['employee_status' => EmpStatusEnum::LEAVE] // Oft sinnvoll für gelöschte
    );
    $trashedUser->delete(); // Setzt model_status auf TRASHED und deleted_at
    $this->employees['trashed'] = $trashedUser;


    // 8. Mitarbeiter in einer anderen Firma/Team (sollte nicht sichtbar sein)
    $otherOwner = User::factory()->create(['user_type' => UserType::Owner, 'model_status' => ModelStatus::ACTIVE]);
    $otherCompany = Company::factory()->create(['owner_id' => $otherOwner->id, 'industry_id' => $industry->id, 'created_by' => $otherOwner->id]);
    $otherOwner->update(['company_id' => $otherCompany->id]);
    $otherTeam = Team::factory()->create(['user_id' => $otherOwner->id, 'company_id' => $otherCompany->id]);
    $otherOwner->ownedTeams()->save($otherTeam);
    $otherOwner->switchTeam($otherTeam);
    $otherOwner->save();
    $otherDepartment = Department::factory()->create(['company_id' => $otherCompany->id, 'team_id' => $otherTeam->id, 'created_by' => $otherOwner->id]);
    $otherProfession = Profession::factory()->create(['company_id' => $otherCompany->id, 'created_by' => $otherOwner->id]);
    $otherStage = Stage::factory()->create(['company_id' => $otherCompany->id, 'created_by' => $otherOwner->id]);
    $otherRole = Role::factory()->create(['name' => 'Other Role', 'access' => RoleHasAccessTo::EmployeePanel, 'visible' => RoleVisibility::Visible, 'company_id' => $otherCompany->id, 'created_by' => $otherOwner->id]);

    $this->otherEmployee = createEmployeeWithRelations(
        $otherOwner, $otherCompany, $otherTeam, $otherDepartment, $otherProfession, $otherStage, $otherRole,
        ['name' => 'Other Company Employee']
    );


    // 9. Authentifizieren als der Haupt-Owner für die meisten Tests
    $this->actingAs($this->owner);

    // 10. Mount-Parameter vorbereiten
    $this->mountParams = [
        'authUserId' => $this->owner->id,
        'currentTeamId' => $this->team->id,
        'companyId' => $this->company->id,
    ];
});

// --- Grundlegende Tests ---

test('table can be rendered', function () {
    Livewire::test(EmployeeTable::class, $this->mountParams)
        ->assertStatus(200)
        ->assertViewIs('livewire.alem.employee.table'); // Sicherstellen, dass die richtige View geladen wird
});

test('active employees of the current team and company are shown by default', function () {
    Livewire::test(EmployeeTable::class, $this->mountParams)
        ->assertSee($this->employees['active_employed']->name)
        ->assertSee($this->employees['active_probation']->name)
        ->assertSee($this->employees['active_onboarding']->name)
        ->assertDontSee($this->employees['archived']->name)
        ->assertDontSee($this->employees['trashed']->name)
        ->assertDontSee($this->otherEmployee->name); // Wichtig: Mitarbeiter anderer Firma/Teams prüfen
});

test('correct user type is enforced', function () {
    // Erstelle einen User mit anderem Typ in der gleichen Firma/Team
    $nonEmployeeUser = User::factory()->create([
        'user_type' => UserType::Owner, // Nicht Employee
        'model_status' => ModelStatus::ACTIVE,
        'company_id' => $this->company->id,
        'created_by' => $this->owner->id,
    ]);
    $this->team->users()->attach($nonEmployeeUser, ['role' => 'admin']); // Rolle 'admin' aus Jetstream
    $nonEmployeeUser->switchTeam($this->team);


    Livewire::test(EmployeeTable::class, $this->mountParams)
        ->assertDontSee($nonEmployeeUser->name);
});


// --- Filter Tests ---

test('can filter employees by model status', function (string $status, string $visibleName, array $hiddenNames) {
    Livewire::test(EmployeeTable::class, $this->mountParams)
        ->set('statusFilter', $status)
        ->assertSee($this->employees[$visibleName]->name)
        ->assertDontSee($this->otherEmployee->name); // Immer prüfen

    foreach ($hiddenNames as $hiddenKey) {
        // Prüfen, ob der Name wirklich existiert, bevor assertDontSee aufgerufen wird
        if (isset($this->employees[$hiddenKey])) {
            Livewire::test(EmployeeTable::class, $this->mountParams)
                ->set('statusFilter', $status)
                ->assertDontSee($this->employees[$hiddenKey]->name);
        }
    }

})->with([
    'active' => ['active', 'active_employed', ['archived', 'trashed']],
    'archived' => ['archived', 'archived', ['active_employed', 'active_probation', 'active_onboarding','trashed']],
    'trashed' => ['trashed', 'trashed', ['active_employed', 'active_probation', 'active_onboarding','archived']],
]);


test('can filter employees by employee status', function (string $employeeStatusValue, string $visibleName, array $hiddenNames) {
    // Finde den Enum-Case basierend auf dem Value
    $employeeStatusEnum = EmpStatusEnum::from($employeeStatusValue);

    Livewire::test(EmployeeTable::class, $this->mountParams)
        ->set('employeeStatusFilter', $employeeStatusEnum->value)
        ->assertSee($this->employees[$visibleName]->name)
        ->assertDontSee($this->otherEmployee->name); // Immer prüfen

    foreach ($hiddenNames as $hiddenKey) {
        if (isset($this->employees[$hiddenKey])) {
            Livewire::test(EmployeeTable::class, $this->mountParams)
                ->set('employeeStatusFilter', $employeeStatusEnum->value)
                ->assertDontSee($this->employees[$hiddenKey]->name);
        }
    }

})->with([
    'probation' => [EmpStatusEnum::PROBATION->value, 'active_probation', ['active_employed', 'active_onboarding', 'archived', 'trashed']],
    'employed' => [EmpStatusEnum::EMPLOYED->value, 'active_employed', ['active_probation', 'active_onboarding', 'trashed']], // Archived kann employed sein
    'onboarding' => [EmpStatusEnum::ONBOARDING->value, 'active_onboarding', ['active_probation', 'active_employed', 'archived', 'trashed']],
]);


test('can reset filters', function () {
    Livewire::test(EmployeeTable::class, $this->mountParams)
        ->set('statusFilter', 'archived')
        ->set('employeeStatusFilter', EmpStatusEnum::PROBATION->value)
        ->set('search', 'SomeSearch')
        ->set('sortCol', 'name')
        ->call('resetFilters')
        // Überprüfe, ob die Filter auf ihre Standardwerte zurückgesetzt wurden
        ->assertSet('statusFilter', 'active') // Standard für ModelStatus
        ->assertSet('employeeStatusFilter', '') // Standard für EmployeeStatus
        ->assertSet('search', '')
        ->assertSet('sortCol', null)
        ->assertSet('sortAsc', false) // Standard-Sortierrichtung
        ->assertSet('selectedIds', []) // Auswahl sollte geleert werden
        // Überprüfe, ob die Standardansicht (aktive Benutzer) wiederhergestellt wird
        ->assertSee($this->employees['active_employed']->name)
        ->assertSee($this->employees['active_probation']->name)
        ->assertSee($this->employees['active_onboarding']->name)
        ->assertDontSee($this->employees['archived']->name);
});

test('changing status filters resets selections', function () {
    // Model Status Filter
    $componentModel = Livewire::test(EmployeeTable::class, $this->mountParams)
        ->set('selectedIds', [$this->employees['active_employed']->id]); // Setze eine Auswahl
    $componentModel->assertSet('selectedIds', [$this->employees['active_employed']->id]);
    $componentModel->set('statusFilter', 'archived'); // Ändere Filter
    $componentModel->assertSet('selectedIds', []); // Erwarte leere Auswahl

    // Employee Status Filter
    $componentEmployee = Livewire::test(EmployeeTable::class, $this->mountParams)
        ->set('selectedIds', [$this->employees['active_employed']->id]); // Setze eine Auswahl
    $componentEmployee->assertSet('selectedIds', [$this->employees['active_employed']->id]);
    $componentEmployee->set('employeeStatusFilter', EmpStatusEnum::PROBATION->value); // Ändere Filter
    $componentEmployee->assertSet('selectedIds', []); // Erwarte leere Auswahl
});

// --- Search and Sort Tests ---

test('search filters employees correctly by name', function () {
    $searchTerm = 'Active Probation'; // Eindeutiger Teil des Namens
    Livewire::test(EmployeeTable::class, $this->mountParams)
        ->set('search', $searchTerm)
        ->assertSee($this->employees['active_probation']->name)
        ->assertDontSee($this->employees['active_employed']->name)
        ->assertDontSee($this->employees['active_onboarding']->name)
        ->assertDontSee($this->employees['archived']->name)
        ->assertDontSee($this->employees['trashed']->name)
        ->assertDontSee($this->otherEmployee->name);
});

test('search filters employees correctly by email', function () {
    $searchTerm = $this->employees['active_onboarding']->email;
    Livewire::test(EmployeeTable::class, $this->mountParams)
        ->set('search', $searchTerm)
        ->assertSee($this->employees['active_onboarding']->name)
        ->assertDontSee($this->employees['active_employed']->name)
        ->assertDontSee($this->employees['active_probation']->name)
        ->assertDontSee($this->employees['archived']->name)
        ->assertDontSee($this->employees['trashed']->name)
        ->assertDontSee($this->otherEmployee->name);
});

test('sorting is applied correctly by name', function () {
    // Erstelle Benutzer für den Sortiertest
    $userA = createEmployeeWithRelations($this->owner, $this->company, $this->team, $this->department, $this->profession, $this->stage, $this->employeeRole, ['name' => 'AAA User']);
    $userZ = createEmployeeWithRelations($this->owner, $this->company, $this->team, $this->department, $this->profession, $this->stage, $this->employeeRole, ['name' => 'ZZZ User']);

    // Test Ascending
    Livewire::test(EmployeeTable::class, $this->mountParams)
        ->call('sortBy', 'name') // Einmal klicken -> ASC
        ->assertSet('sortCol', 'name')
        ->assertSet('sortAsc', true)
        ->assertSeeInOrder([$userA->name, $userZ->name]); // Reihenfolge prüfen

    // Test Descending
    Livewire::test(EmployeeTable::class, $this->mountParams)
        ->call('sortBy', 'name') // Erster Klick -> ASC
        ->call('sortBy', 'name') // Zweiter Klick -> DESC
        ->assertSet('sortCol', 'name')
        ->assertSet('sortAsc', false)
        ->assertSeeInOrder([$userZ->name, $userA->name]); // Reihenfolge prüfen
});

// --- Pagination Tests ---

test('pagination works correctly', function () {
    // Erstelle mehr Benutzer als die Standard-Seitengröße (perPage = 7)
    for ($i = 0; $i < 10; $i++) {
        createEmployeeWithRelations($this->owner, $this->company, $this->team, $this->department, $this->profession, $this->stage, $this->employeeRole, ['name' => "Paginate User {$i}"]);
    }

    Livewire::test(EmployeeTable::class, $this->mountParams)
        ->assertViewHas('users', function ($users) {
            // Prüfe, ob die Anzahl der Benutzer auf der ersten Seite der perPage-Einstellung entspricht
            return $users->count() === 7; // Standard perPage
        })
        ->set('perPage', 5) // Ändere perPage
        ->assertViewHas('users', function ($users) {
            // Prüfe erneut mit der neuen Seitengröße
            return $users->count() === 5;
        });
});


// --- Single Model Status Action Tests ---

test('can activate user', function () {
    $archivedUserId = $this->employees['archived']->id;

    Livewire::test(EmployeeTable::class, $this->mountParams)
        ->call('activate', $archivedUserId)
        ->assertDispatched('employeeUpdated') // Prüfe spezifisches Event
        ->assertDispatched('update-table');

    $this->assertDatabaseHas('users', [
        'id' => $archivedUserId,
        'model_status' => ModelStatus::ACTIVE->value,
        'deleted_at' => null, // Sicherstellen, dass es nicht gelöscht ist
    ]);
});

test('can archive user', function () {
    $activeUserId = $this->employees['active_employed']->id;

    Livewire::test(EmployeeTable::class, $this->mountParams)
        ->call('archive', $activeUserId)
        ->assertDispatched('employeeUpdated')
        ->assertDispatched('update-table');

    $this->assertDatabaseHas('users', [
        'id' => $activeUserId,
        'model_status' => ModelStatus::ARCHIVED->value,
        'deleted_at' => null,
    ]);
});

test('can trash user', function () {
    $activeUserId = $this->employees['active_employed']->id;

    Livewire::test(EmployeeTable::class, $this->mountParams)
        ->call('delete', $activeUserId)
        ->assertDispatched('employeeUpdated')
        ->assertDispatched('update-table');

    // Überprüfe Soft Delete und den Status
    $this->assertSoftDeleted('users', ['id' => $activeUserId]);
    $this->assertDatabaseHas('users', [
        'id' => $activeUserId,
        'model_status' => ModelStatus::TRASHED->value, // Status sollte TRASHED sein
    ]);
});

test('can restore user from trash to active', function () {
    $trashedUserId = $this->employees['trashed']->id;

    // Verify initial state (redundant durch beforeEach, aber zur Klarheit)
    $this->assertSoftDeleted('users', ['id' => $trashedUserId]);
    $this->assertDatabaseHas('users', ['id' => $trashedUserId, 'model_status' => ModelStatus::TRASHED->value]);

    Livewire::test(EmployeeTable::class, $this->mountParams)
        ->set('statusFilter', 'trashed') // Wichtig: Filter setzen, um den Benutzer zu sehen
        ->call('restore', $trashedUserId)
        ->assertDispatched('employeeUpdated')
        ->assertDispatched('update-table');

    // Überprüfe den Zustand nach der Wiederherstellung
    $this->assertDatabaseHas('users', [
        'id' => $trashedUserId,
        'deleted_at' => null, // Nicht mehr soft deleted
        'model_status' => ModelStatus::ACTIVE->value, // Sollte ACTIVE sein
    ]);
});

test('can restore user from archive to active', function () {
    $archivedUserId = $this->employees['archived']->id;

    // Verify initial state
    $this->assertDatabaseHas('users', ['id' => $archivedUserId, 'model_status' => ModelStatus::ARCHIVED->value, 'deleted_at' => null]);

    Livewire::test(EmployeeTable::class, $this->mountParams)
        ->set('statusFilter', 'archived') // Wichtig: Filter setzen
        ->call('restore', $archivedUserId) // 'restore' setzt von ARCHIVED auf ACTIVE
        ->assertDispatched('employeeUpdated')
        ->assertDispatched('update-table');

    // Überprüfe den Zustand nach der Aktion
    $this->assertDatabaseHas('users', [
        'id' => $archivedUserId,
        'model_status' => ModelStatus::ACTIVE->value, // Sollte ACTIVE sein
        'deleted_at' => null,
    ]);
});


test('can restore user from trash to archive', function () {
    $trashedUserId = $this->employees['trashed']->id;

    // Verify initial state
    $this->assertSoftDeleted('users', ['id' => $trashedUserId]);
    $this->assertDatabaseHas('users', ['id' => $trashedUserId, 'model_status' => ModelStatus::TRASHED->value]);


    Livewire::test(EmployeeTable::class, $this->mountParams)
        ->set('statusFilter', 'trashed') // Wichtig: Filter setzen
        ->call('restoreToArchive', $trashedUserId)
        ->assertDispatched('employeeUpdated')
        ->assertDispatched('update-table');

    // Überprüfe den Zustand nach der Aktion
    $this->assertDatabaseHas('users', [
        'id' => $trashedUserId,
        'deleted_at' => null, // Nicht mehr soft deleted
        'model_status' => ModelStatus::ARCHIVED->value, // Sollte ARCHIVED sein
    ]);
});

test('can force delete user', function () {
    $trashedUserId = $this->employees['trashed']->id;

    // Verify it exists (soft deleted)
    $this->assertSoftDeleted('users', ['id' => $trashedUserId]);

    Livewire::test(EmployeeTable::class, $this->mountParams)
        ->set('statusFilter', 'trashed') // Wichtig: Filter setzen
        ->call('forceDelete', $trashedUserId)
        ->assertDispatched('employeeUpdated')
        ->assertDispatched('update-table');

    // Überprüfe, ob der Benutzer endgültig gelöscht wurde
    $this->assertDatabaseMissing('users', ['id' => $trashedUserId]);
});

// --- Bulk Model Status Action Tests ---

test('can bulk activate users', function () {
    $archivedUserId = $this->employees['archived']->id;
    // Erstelle einen weiteren archivierten Benutzer für den Bulk-Test
    $anotherArchived = createEmployeeWithRelations($this->owner, $this->company, $this->team, $this->department, $this->profession, $this->stage, $this->employeeRole, ['name' => 'Another Archived', 'model_status' => ModelStatus::ARCHIVED]);

    $selectedIds = [$archivedUserId, $anotherArchived->id];

    Livewire::test(EmployeeTable::class, $this->mountParams)
        ->set('statusFilter', 'archived') // Filter setzen, um sie auszuwählen
        ->set('selectedIds', $selectedIds)
        ->call('bulkUpdateStatus', 'active')
        ->assertDispatched('employeeUpdated')
        ->assertDispatched('update-table')
        ->assertSet('selectedIds', []); // Auswahl sollte geleert werden

    foreach ($selectedIds as $id) {
        $this->assertDatabaseHas('users', [
            'id' => $id,
            'model_status' => ModelStatus::ACTIVE->value,
            'deleted_at' => null,
        ]);
    }
});

test('can bulk archive users', function () {
    $activeUserId1 = $this->employees['active_employed']->id;
    $activeUserId2 = $this->employees['active_probation']->id;
    $selectedIds = [$activeUserId1, $activeUserId2];

    Livewire::test(EmployeeTable::class, $this->mountParams)
        ->set('statusFilter', 'active') // Standardfilter ist active
        ->set('selectedIds', $selectedIds)
        ->call('bulkUpdateStatus', 'archived')
        ->assertDispatched('employeeUpdated')
        ->assertDispatched('update-table')
        ->assertSet('selectedIds', []);

    foreach ($selectedIds as $id) {
        $this->assertDatabaseHas('users', [
            'id' => $id,
            'model_status' => ModelStatus::ARCHIVED->value,
            'deleted_at' => null,
        ]);
    }
});

test('can bulk trash users', function () {
    $activeUserId1 = $this->employees['active_employed']->id;
    $activeUserId2 = $this->employees['active_probation']->id;
    $selectedIds = [$activeUserId1, $activeUserId2];

    Livewire::test(EmployeeTable::class, $this->mountParams)
        ->set('statusFilter', 'active')
        ->set('selectedIds', $selectedIds)
        ->call('bulkUpdateStatus', 'trashed')
        ->assertDispatched('employeeUpdated')
        ->assertDispatched('update-table')
        ->assertSet('selectedIds', []);

    foreach ($selectedIds as $id) {
        $this->assertSoftDeleted('users', ['id' => $id]);
        $this->assertDatabaseHas('users', [
            'id' => $id,
            'model_status' => ModelStatus::TRASHED->value, // Status prüfen
        ]);
    }
});


test('can bulk restore to active', function () {
    $trashedUserId1 = $this->employees['trashed']->id;
    $anotherTrashed = createEmployeeWithRelations($this->owner, $this->company, $this->team, $this->department, $this->profession, $this->stage, $this->employeeRole, ['name' => 'Another Trashed']);
    $anotherTrashed->delete();
    $trashedUserId2 = $anotherTrashed->id;

    $selectedIds = [$trashedUserId1, $trashedUserId2];

    Livewire::test(EmployeeTable::class, $this->mountParams)
        ->set('statusFilter', 'trashed')
        ->set('selectedIds', $selectedIds)
        ->call('bulkUpdateStatus', 'restore_to_active')
        ->assertDispatched('employeeUpdated')
        ->assertDispatched('update-table')
        ->assertSet('selectedIds', []);

    foreach ($selectedIds as $id) {
        $this->assertDatabaseHas('users', [
            'id' => $id,
            'deleted_at' => null,
            'model_status' => ModelStatus::ACTIVE->value,
        ]);
    }
});

test('can bulk restore to archive', function () {
    $trashedUserId1 = $this->employees['trashed']->id;
    $anotherTrashed = createEmployeeWithRelations($this->owner, $this->company, $this->team, $this->department, $this->profession, $this->stage, $this->employeeRole, ['name' => 'Yet Another Trashed']);
    $anotherTrashed->delete();
    $trashedUserId2 = $anotherTrashed->id;

    $selectedIds = [$trashedUserId1, $trashedUserId2];

    Livewire::test(EmployeeTable::class, $this->mountParams)
        ->set('statusFilter', 'trashed')
        ->set('selectedIds', $selectedIds)
        ->call('bulkUpdateStatus', 'restore_to_archive')
        ->assertDispatched('employeeUpdated')
        ->assertDispatched('update-table')
        ->assertSet('selectedIds', []);

    foreach ($selectedIds as $id) {
        $this->assertDatabaseHas('users', [
            'id' => $id,
            'deleted_at' => null,
            'model_status' => ModelStatus::ARCHIVED->value,
        ]);
    }
});


test('can bulk force delete users', function () {
    $trashedUserId1 = $this->employees['trashed']->id;
    $anotherTrashed = createEmployeeWithRelations($this->owner, $this->company, $this->team, $this->department, $this->profession, $this->stage, $this->employeeRole, ['name' => 'Trash Me Bulk']);
    $anotherTrashed->delete();
    $trashedUserId2 = $anotherTrashed->id;

    $selectedIds = [$trashedUserId1, $trashedUserId2];

    // Verify they exist before deletion
    $this->assertSoftDeleted('users', ['id' => $trashedUserId1]);
    $this->assertSoftDeleted('users', ['id' => $trashedUserId2]);


    Livewire::test(EmployeeTable::class, $this->mountParams)
        ->set('statusFilter', 'trashed')
        ->set('selectedIds', $selectedIds)
        ->call('bulkForceDelete')
        ->assertDispatched('employeeUpdated')
        ->assertDispatched('update-table')
        ->assertSet('selectedIds', []);

    foreach ($selectedIds as $id) {
        $this->assertDatabaseMissing('users', ['id' => $id]);
    }
});


// --- Other Functionality Tests ---

test('can empty trash', function () {
    // Stelle sicher, dass mindestens ein gelöschter Benutzer existiert
    $this->assertSoftDeleted('users', ['id' => $this->employees['trashed']->id]);
    $initialTrashedCount = User::onlyTrashed()->where('company_id', $this->company->id)->count();
    expect($initialTrashedCount)->toBeGreaterThan(0);


    Livewire::test(EmployeeTable::class, $this->mountParams)
        ->set('statusFilter', 'trashed')
        ->call('emptyTrash')
        ->assertDispatched('employeeUpdated')
        ->assertDispatched('update-table');

    // Verify all trashed users for this company are permanently deleted
    $finalTrashedCount = User::onlyTrashed()->where('company_id', $this->company->id)->count();
    expect($finalTrashedCount)->toBe(0);
});

test('refreshTable resets pagination', function () {
    // Erstelle genug Benutzer für mehrere Seiten
    for ($i = 0; $i < 15; $i++) {
        createEmployeeWithRelations($this->owner, $this->company, $this->team, $this->department, $this->profession, $this->stage, $this->employeeRole, ['name' => "Page User {$i}"]);
    }

    // Gehe zu Seite 2
    $component = Livewire::test(EmployeeTable::class, $this->mountParams)
        ->set('perPage', 7)
        ->set('page', 2);
    $component->assertSet('page', 2); // Stelle sicher, dass wir auf Seite 2 sind

    // Rufe refreshTable auf (simuliert das Event)
    $component->call('refreshTable');

    // Überprüfe, ob die Seite auf 1 zurückgesetzt wurde
    // Livewire 3 setzt nicht direkt die 'page'-Property zurück,
    // aber das erneute Rendern mit resetPage() sollte dazu führen,
    // dass die Paginierungs-Infos für Seite 1 gelten.
    // Wir prüfen, ob der erste Benutzer wieder sichtbar ist.
    $component->assertSee(User::where('name', 'Active Employed')->first()->name); // Annahme: dieser Benutzer ist auf Seite 1
});


test('query uses FORCE INDEX when not searching', function () {
    $query = Livewire::test(EmployeeTable::class, $this->mountParams)
        ->instance() // Zugriff auf die Komponenteninstanz
        ->render() // render() ausführen, um die Abfrage zu bauen
        ->instance() // Erneuter Zugriff auf die Instanz nach dem Rendern
        ->lastRenderedView // Zugriff auf die gerenderte View (oder eine Eigenschaft, die die Query hält, falls verfügbar)
        ->getData()['users'] // Zugriff auf die Paginator-Instanz
        ->getQuery() // Zugriff auf den zugrunde liegenden Query Builder
        ->toSql(); // SQL generieren

    // Erwarte, dass FORCE INDEX im SQL enthalten ist
    expect($query)->toContain('FORCE INDEX (`idx_users_filter_sort`)');

});

test('query does not use FORCE INDEX when searching', function () {
    $query = Livewire::test(EmployeeTable::class, $this->mountParams)
        ->set('search', 'test') // Suchbegriff setzen
        ->instance()
        ->render()
        ->instance()
        ->lastRenderedView
        ->getData()['users']
        ->getQuery()
        ->toSql();

    // Erwarte, dass FORCE INDEX NICHT im SQL enthalten ist
    expect($query)->not->toContain('FORCE INDEX');
    // Optional: Erwarte, dass die Tabelle einfach nur `users` ist
    expect($query)->toContain('from `users` where');

});


test('placeholder view is correct', function () {
    // Testen, ob die Placeholder-Methode die korrekte View zurückgibt
    // Dies ist ein einfacher Test, der nicht die Rendering-Logik prüft
    $view = app(EmployeeTable::class)->placeholder();
    expect($view->getName())->toBe('livewire.placeholders.employee.index');
});
