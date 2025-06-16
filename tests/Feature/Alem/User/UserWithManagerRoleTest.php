<?php

namespace Tests\Feature\Alem\User;

use App\Models\Alem\Company;
use App\Models\Spatie\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserWithManagerRoleTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $managerUser;
    protected User $testUser; // Für created_by
    protected Company $company;
    protected Role $managerRole;
    protected Role $regularRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Cache leeren vor jedem Test
        Cache::flush();

        // Zuerst einen Test-User erstellen für created_by
        $this->testUser = User::factory()->create([
            'created_by' => null, // Erster User hat keinen Creator
        ]);

        // Company erstellen
        $this->company = Company::factory()->create([
            'created_by' => $this->testUser->id
        ]);

        // Rollen erstellen mit allen erforderlichen Feldern
        $this->managerRole = Role::create([
            'name' => 'manager',
            'guard_name' => 'web',
            'is_manager' => true,
            'created_by' => $this->testUser->id,
            'company_id' => $this->company->id,
        ]);

        $this->regularRole = Role::create([
            'name' => 'employee',
            'guard_name' => 'web',
            'is_manager' => false,
            'created_by' => $this->testUser->id,
            'company_id' => $this->company->id,
        ]);

        // Users erstellen
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'created_by' => $this->testUser->id,
        ]);

        $this->managerUser = User::factory()->create([
            'company_id' => $this->company->id,
            'created_by' => $this->testUser->id,
        ]);

        // Manager-Rolle direkt über Spatie zuweisen (nicht über unsere Trait-Methode)
        $this->managerUser->assignRole($this->managerRole);
    }

    protected function tearDown(): void
    {
        // Cache nach jedem Test leeren
        Cache::flush();
        parent::tearDown();
    }

    #[Test]
    public function it_can_check_if_user_has_manager_role()
    {
        // User ohne Manager-Rolle
        $this->assertFalse($this->user->hasManagerRole(), 'User should not have manager role initially');

        // User mit Manager-Rolle
        $this->assertTrue($this->managerUser->hasManagerRole(), 'Manager user should have manager role');

        // User mit normaler Rolle - sollte false sein
        $this->user->assignRole($this->regularRole);
        $this->assertFalse($this->user->hasManagerRole(), 'User with regular role should not have manager role');

        // User mit Manager-Rolle hinzufügen
        $this->user->assignRole($this->managerRole);
        $this->assertTrue($this->user->hasManagerRole(), 'User should have manager role after assignment');
    }

    #[Test]
    public function it_can_clear_manager_cache()
    {
        $companyId = $this->company->id;

        // Cache-Key generieren wie in der Trait
        $persistentKey = "users:company:{$companyId}:managers";

        // Cache mit Testdaten füllen
        Cache::put($persistentKey, collect(['test' => 'data']), 3600);
        $this->assertTrue(Cache::has($persistentKey), 'Cache should be set');

        // Cache leeren
        User::clearManagerCache($companyId);

        // Prüfen ob Cache geleert wurde
        $this->assertFalse(Cache::has($persistentKey), 'Cache should be cleared');
    }

    #[Test]
    public function it_can_assign_manager_role_and_clears_cache()
    {
        $companyId = $this->company->id;
        $persistentKey = "users:company:{$companyId}:managers";

        // Cache mit Testdaten füllen
        Cache::put($persistentKey, collect(['old' => 'data']), 3600);
        $this->assertTrue(Cache::has($persistentKey), 'Cache should be set initially');

        // Manager-Rolle zuweisen
        $result = $this->user->assignManagerRole($this->managerRole);

        // Prüfen ob Rolle zugewiesen wurde
        $this->assertTrue($this->user->hasManagerRole(), 'User should have manager role');
        $this->assertTrue($this->user->hasRole($this->managerRole), 'User should have the specific manager role');

        // Prüfen ob Cache geleert wurde
        $this->assertFalse(Cache::has($persistentKey), 'Cache should be cleared after manager role assignment');

        // Return-Wert sollte das Ergebnis der assignRole-Methode sein
        $this->assertNotNull($result, 'assignManagerRole should return a result');
    }

    #[Test]
    public function it_can_assign_manager_role_by_string()
    {
        $result = $this->user->assignManagerRole('manager');

        $this->assertTrue($this->user->hasManagerRole(), 'User should have manager role');
        $this->assertTrue($this->user->hasRole('manager'), 'User should have the manager role by name');
    }

    #[Test]
    public function it_can_assign_manager_role_by_id()
    {
        $result = $this->user->assignManagerRole($this->managerRole->id);

        $this->assertTrue($this->user->hasManagerRole(), 'User should have manager role');
        $this->assertTrue($this->user->hasRole($this->managerRole), 'User should have the manager role by ID');
    }

    #[Test]
    public function it_can_assign_multiple_roles_including_manager()
    {
        $companyId = $this->company->id;
        $persistentKey = "users:company:{$companyId}:managers";

        Cache::put($persistentKey, collect(['old' => 'data']), 3600);

        // Mehrere Rollen zuweisen, inkl. Manager
        $this->user->assignManagerRole($this->regularRole, $this->managerRole);

        $this->assertTrue($this->user->hasManagerRole(), 'User should have manager role');
        $this->assertTrue($this->user->hasRole($this->regularRole), 'User should have regular role');
        $this->assertTrue($this->user->hasRole($this->managerRole), 'User should have manager role');
        $this->assertFalse(Cache::has($persistentKey), 'Cache should be cleared');
    }

    #[Test]
    public function it_does_not_clear_cache_when_assigning_non_manager_role()
    {
        $companyId = $this->company->id;
        $persistentKey = "users:company:{$companyId}:managers";

        Cache::put($persistentKey, collect(['should' => 'remain']), 3600);

        // Nur normale Rolle zuweisen
        $this->user->assignManagerRole($this->regularRole);

        $this->assertFalse($this->user->hasManagerRole(), 'User should not have manager role');
        $this->assertTrue($this->user->hasRole($this->regularRole), 'User should have regular role');

        // Cache sollte nicht geleert werden
        $this->assertTrue(Cache::has($persistentKey), 'Cache should not be cleared when assigning non-manager role');
    }

    #[Test]
    public function it_does_not_clear_cache_when_user_already_has_manager_role()
    {
        $companyId = $this->company->id;
        $persistentKey = "users:company:{$companyId}:managers";

        // User bereits Manager
        $this->user->assignRole($this->managerRole);

        Cache::put($persistentKey, collect(['should' => 'remain']), 3600);

        // Weitere Manager-Rolle zuweisen
        $secondManagerRole = Role::create([
            'name' => 'senior_manager',
            'guard_name' => 'web',
            'is_manager' => true,
            'created_by' => $this->testUser->id,
            'company_id' => $this->company->id,
        ]);

        $this->user->assignManagerRole($secondManagerRole);

        // Cache sollte nicht geleert werden, da User bereits Manager war
        $this->assertTrue(Cache::has($persistentKey), 'Cache should not be cleared when user already has manager role');
    }

    #[Test]
    public function it_does_not_clear_cache_when_user_has_no_company()
    {
        $persistentKey = "users:company:1:managers";
        Cache::put($persistentKey, collect(['should' => 'remain']), 3600);

        // User ohne Company
        $userWithoutCompany = User::factory()->create([
            'company_id' => null,
            'created_by' => $this->testUser->id,
        ]);

        $userWithoutCompany->assignManagerRole($this->managerRole);

        // Cache sollte nicht geleert werden
        $this->assertTrue(Cache::has($persistentKey), 'Cache should not be cleared when user has no company');
    }

    #[Test]
    public function it_can_remove_manager_role_and_clears_cache()
    {
        $companyId = $this->company->id;
        $persistentKey = "users:company:{$companyId}:managers";

        // User hat Manager-Rolle
        $this->user->assignRole($this->managerRole);
        $this->assertTrue($this->user->hasManagerRole(), 'User should have manager role initially');

        Cache::put($persistentKey, collect(['old' => 'data']), 3600);

        // Manager-Rolle entfernen
        $result = $this->user->removeManagerRole($this->managerRole);

        // Prüfen ob Rolle entfernt wurde
        $this->assertFalse($this->user->hasManagerRole(), 'User should not have manager role after removal');
        $this->assertFalse($this->user->hasRole($this->managerRole), 'User should not have the specific manager role');

        // Cache sollte geleert sein
        $this->assertFalse(Cache::has($persistentKey), 'Cache should be cleared after manager role removal');
    }

    #[Test]
    public function it_can_remove_manager_role_by_string()
    {
        $this->user->assignRole($this->managerRole);

        $result = $this->user->removeManagerRole('manager');

        $this->assertFalse($this->user->hasManagerRole(), 'User should not have manager role');
        $this->assertFalse($this->user->hasRole('manager'), 'User should not have manager role by name');
    }

    #[Test]
    public function it_can_remove_manager_role_by_id()
    {
        $this->user->assignRole($this->managerRole);

        $result = $this->user->removeManagerRole($this->managerRole->id);

        $this->assertFalse($this->user->hasManagerRole(), 'User should not have manager role');
        $this->assertFalse($this->user->hasRole($this->managerRole), 'User should not have the specific manager role');
    }

    #[Test]
    public function it_does_not_clear_cache_when_user_has_other_manager_roles()
    {
        $companyId = $this->company->id;
        $persistentKey = "users:company:{$companyId}:managers";

        // Zweite Manager-Rolle erstellen
        $secondManagerRole = Role::create([
            'name' => 'senior_manager',
            'guard_name' => 'web',
            'is_manager' => true,
            'created_by' => $this->testUser->id,
            'company_id' => $this->company->id,
        ]);

        // User hat beide Manager-Rollen
        $this->user->assignRole([$this->managerRole, $secondManagerRole]);

        Cache::put($persistentKey, collect(['should' => 'remain']), 3600);

        // Eine Manager-Rolle entfernen
        $this->user->removeManagerRole($this->managerRole);

        // User sollte noch Manager sein
        $this->assertTrue($this->user->hasManagerRole(), 'User should still have manager role');
        $this->assertTrue($this->user->hasRole($secondManagerRole), 'User should still have the second manager role');

        // Cache sollte nicht geleert werden
        $this->assertTrue(Cache::has($persistentKey), 'Cache should not be cleared when user still has other manager roles');
    }

    #[Test]
    public function it_does_not_clear_cache_when_removing_non_manager_role()
    {
        $companyId = $this->company->id;
        $persistentKey = "users:company:{$companyId}:managers";

        // User hat Manager- und normale Rolle
        $this->user->assignRole([$this->managerRole, $this->regularRole]);

        Cache::put($persistentKey, collect(['should' => 'remain']), 3600);

        // Normale Rolle entfernen
        $this->user->removeManagerRole($this->regularRole);

        // User sollte noch Manager sein
        $this->assertTrue($this->user->hasManagerRole(), 'User should still have manager role');
        $this->assertFalse($this->user->hasRole($this->regularRole), 'User should not have regular role');

        // Cache sollte nicht geleert werden
        $this->assertTrue(Cache::has($persistentKey), 'Cache should not be cleared when removing non-manager role');
    }

    #[Test]
    public function it_can_get_company_managers()
    {
        // Weitere Manager erstellen
        $manager2 = User::factory()->create([
            'company_id' => $this->company->id,
            'created_by' => $this->testUser->id,
        ]);
        $manager2->assignRole($this->managerRole);

        $manager3 = User::factory()->create([
            'company_id' => $this->company->id,
            'created_by' => $this->testUser->id,
        ]);
        $manager3->assignRole($this->managerRole);

        // Normale User erstellen (kein Manager)
        $regularUser = User::factory()->create([
            'company_id' => $this->company->id,
            'created_by' => $this->testUser->id,
        ]);
        $regularUser->assignRole($this->regularRole);

        // User aus anderer Company
        $otherCompany = Company::factory()->create([
            'created_by' => $this->testUser->id,
        ]);
        $otherManager = User::factory()->create([
            'company_id' => $otherCompany->id,
            'created_by' => $this->testUser->id,
        ]);
        $otherManager->assignRole($this->managerRole);

        // Manager abrufen
        $managers = User::getCompanyManagers($this->company->id);

        $this->assertInstanceOf(Collection::class, $managers, 'Should return a Collection');
        $this->assertCount(3, $managers, 'Should return 3 managers'); // managerUser, manager2, manager3

        // Prüfen ob nur Manager der richtigen Company
        $managerIds = $managers->pluck('id')->toArray();
        $this->assertContains($this->managerUser->id, $managerIds, 'Should contain managerUser');
        $this->assertContains($manager2->id, $managerIds, 'Should contain manager2');
        $this->assertContains($manager3->id, $managerIds, 'Should contain manager3');
        $this->assertNotContains($regularUser->id, $managerIds, 'Should not contain regular user');
        $this->assertNotContains($otherManager->id, $managerIds, 'Should not contain manager from other company');

        // Prüfen ob die richtigen Felder zurückgegeben werden
        $firstManager = $managers->first();
        $this->assertArrayHasKey('id', $firstManager->toArray(), 'Should have id field');
        $this->assertArrayHasKey('name', $firstManager->toArray(), 'Should have name field');
        $this->assertArrayHasKey('last_name', $firstManager->toArray(), 'Should have last_name field');
        $this->assertArrayHasKey('profile_photo_path', $firstManager->toArray(), 'Should have profile_photo_path field');
    }

    #[Test]
    public function it_caches_company_managers()
    {
        $companyId = $this->company->id;
        $persistentKey = "users:company:{$companyId}:managers";

        // Erste Abfrage - sollte Cache füllen
        $managers1 = User::getCompanyManagers($companyId);
        $this->assertTrue(Cache::has($persistentKey), 'Cache should be filled after first query');

        // Zweite Abfrage - sollte aus Cache kommen
        $managers2 = User::getCompanyManagers($companyId);

        $this->assertEquals($managers1->toArray(), $managers2->toArray(), 'Results should be identical from cache');
    }

    #[Test]
    public function it_returns_empty_collection_when_no_managers_exist()
    {
        $companyWithoutManagers = Company::factory()->create([
            'created_by' => $this->testUser->id,
        ]);

        $managers = User::getCompanyManagers($companyWithoutManagers->id);

        $this->assertInstanceOf(Collection::class, $managers, 'Should return a Collection');
        $this->assertCount(0, $managers, 'Should return empty collection');
    }

    #[Test]
    public function it_excludes_soft_deleted_managers()
    {
        // Manager soft delete
        $this->managerUser->delete();

        $managers = User::getCompanyManagers($this->company->id);

        $this->assertCount(0, $managers, 'Should not include soft-deleted managers');
        $this->assertNotContains($this->managerUser->id, $managers->pluck('id'), 'Should not contain deleted manager');
    }

    #[Test]
    public function it_orders_managers_by_name()
    {
        // Manager mit verschiedenen Namen erstellen
        $managerZ = User::factory()->create([
            'company_id' => $this->company->id,
            'name' => 'Zoe',
            'created_by' => $this->testUser->id,
        ]);
        $managerZ->assignRole($this->managerRole);

        $managerA = User::factory()->create([
            'company_id' => $this->company->id,
            'name' => 'Alice',
            'created_by' => $this->testUser->id,
        ]);
        $managerA->assignRole($this->managerRole);

        $managers = User::getCompanyManagers($this->company->id);

        // Prüfen ob nach Namen sortiert
        $names = $managers->pluck('name')->toArray();
        $sortedNames = collect($names)->sort()->values()->toArray();

        $this->assertEquals($sortedNames, $names, 'Managers should be ordered by name');
    }

    #[Test]
    public function it_removes_duplicates_when_user_has_multiple_manager_roles()
    {
        // Zweite Manager-Rolle erstellen
        $secondManagerRole = Role::create([
            'name' => 'senior_manager',
            'guard_name' => 'web',
            'is_manager' => true,
            'created_by' => $this->testUser->id,
            'company_id' => $this->company->id,
        ]);

        // User beide Manager-Rollen zuweisen
        $this->managerUser->assignRole($secondManagerRole);

        $managers = User::getCompanyManagers($this->company->id);

        // User sollte nur einmal in der Liste stehen
        $userOccurrences = $managers->where('id', $this->managerUser->id)->count();
        $this->assertEquals(1, $userOccurrences, 'User should appear only once even with multiple manager roles');
    }
}
