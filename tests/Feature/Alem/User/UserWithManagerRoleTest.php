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
    protected Company $company;
    protected Role $managerRole;
    protected Role $regularRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Company erstellen
        $this->company = Company::factory()->create();

        // Rollen erstellen
        $this->managerRole = Role::create([
            'name' => 'manager',
            'guard_name' => 'web',
            'is_manager' => true
        ]);

        $this->regularRole = Role::create([
            'name' => 'employee',
            'guard_name' => 'web',
            'is_manager' => false
        ]);

        // Users erstellen
        $this->user = User::factory()->create([
            'company_id' => $this->company->id
        ]);

        $this->managerUser = User::factory()->create([
            'company_id' => $this->company->id
        ]);
        $this->managerUser->assignRole($this->managerRole);
    }

    #[Test]
    public function it_can_check_if_user_has_manager_role()
    {
        // User ohne Manager-Rolle
        $this->assertFalse($this->user->hasManagerRole());

        // User mit Manager-Rolle
        $this->assertTrue($this->managerUser->hasManagerRole());

        // User mit normaler Rolle - sollte false sein
        $this->user->assignRole($this->regularRole);
        $this->assertFalse($this->user->hasManagerRole());

        // User mit Manager-Rolle hinzufügen
        $this->user->assignRole($this->managerRole);
        $this->assertTrue($this->user->hasManagerRole());
    }

    #[Test]
    public function it_can_clear_manager_cache()
    {
        $companyId = $this->company->id;

        // Cache-Key generieren wie in der Trait
        $persistentKey = "users:company:{$companyId}:managers";

        // Cache mit Testdaten füllen
        Cache::put($persistentKey, collect(['test' => 'data']), 3600);
        $this->assertTrue(Cache::has($persistentKey));

        // Cache leeren
        User::clearManagerCache($companyId);

        // Prüfen ob Cache geleert wurde
        $this->assertFalse(Cache::has($persistentKey));
    }

    #[Test]
    public function it_can_assign_manager_role_and_clears_cache()
    {
        $companyId = $this->company->id;
        $persistentKey = "users:company:{$companyId}:managers";

        // Cache mit Testdaten füllen
        Cache::put($persistentKey, collect(['old' => 'data']), 3600);
        $this->assertTrue(Cache::has($persistentKey));

        // Manager-Rolle zuweisen
        $result = $this->user->assignManagerRole($this->managerRole);

        // Prüfen ob Rolle zugewiesen wurde
        $this->assertTrue($this->user->hasManagerRole());
        $this->assertTrue($this->user->hasRole($this->managerRole));

        // Prüfen ob Cache geleert wurde
        $this->assertFalse(Cache::has($persistentKey));

        // Return-Wert sollte das Ergebnis der parent-Methode sein
        $this->assertNotNull($result);
    }

    #[Test]
    public function it_can_assign_manager_role_by_string()
    {
        $result = $this->user->assignManagerRole('manager');

        $this->assertTrue($this->user->hasManagerRole());
        $this->assertTrue($this->user->hasRole('manager'));
    }

    #[Test]
    public function it_can_assign_manager_role_by_id()
    {
        $result = $this->user->assignManagerRole($this->managerRole->id);

        $this->assertTrue($this->user->hasManagerRole());
        $this->assertTrue($this->user->hasRole($this->managerRole));
    }

    #[Test]
    public function it_can_assign_multiple_roles_including_manager()
    {
        $companyId = $this->company->id;
        $persistentKey = "users:company:{$companyId}:managers";

        Cache::put($persistentKey, collect(['old' => 'data']), 3600);

        // Mehrere Rollen zuweisen, inkl. Manager
        $this->user->assignManagerRole($this->regularRole, $this->managerRole);

        $this->assertTrue($this->user->hasManagerRole());
        $this->assertTrue($this->user->hasRole($this->regularRole));
        $this->assertTrue($this->user->hasRole($this->managerRole));
        $this->assertFalse(Cache::has($persistentKey));
    }

    #[Test]
    public function it_does_not_clear_cache_when_assigning_non_manager_role()
    {
        $companyId = $this->company->id;
        $persistentKey = "users:company:{$companyId}:managers";

        Cache::put($persistentKey, collect(['should' => 'remain']), 3600);

        // Nur normale Rolle zuweisen
        $this->user->assignManagerRole($this->regularRole);

        $this->assertFalse($this->user->hasManagerRole());
        $this->assertTrue($this->user->hasRole($this->regularRole));

        // Cache sollte nicht geleert werden
        $this->assertTrue(Cache::has($persistentKey));
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
            'is_manager' => true
        ]);

        $this->user->assignManagerRole($secondManagerRole);

        // Cache sollte nicht geleert werden, da User bereits Manager war
        $this->assertTrue(Cache::has($persistentKey));
    }

    #[Test]
    public function it_does_not_clear_cache_when_user_has_no_company()
    {
        $persistentKey = "users:company:1:managers";
        Cache::put($persistentKey, collect(['should' => 'remain']), 3600);

        // User ohne Company
        $userWithoutCompany = User::factory()->create(['company_id' => null]);

        $userWithoutCompany->assignManagerRole($this->managerRole);

        // Cache sollte nicht geleert werden
        $this->assertTrue(Cache::has($persistentKey));
    }

    #[Test]
    public function it_can_remove_manager_role_and_clears_cache()
    {
        $companyId = $this->company->id;
        $persistentKey = "users:company:{$companyId}:managers";

        // User hat Manager-Rolle
        $this->user->assignRole($this->managerRole);
        $this->assertTrue($this->user->hasManagerRole());

        Cache::put($persistentKey, collect(['old' => 'data']), 3600);

        // Manager-Rolle entfernen
        $result = $this->user->removeManagerRole($this->managerRole);

        // Prüfen ob Rolle entfernt wurde
        $this->assertFalse($this->user->hasManagerRole());
        $this->assertFalse($this->user->hasRole($this->managerRole));

        // Cache sollte geleert sein
        $this->assertFalse(Cache::has($persistentKey));
    }

    #[Test]
    public function it_can_remove_manager_role_by_string()
    {
        $this->user->assignRole($this->managerRole);

        $result = $this->user->removeManagerRole('manager');

        $this->assertFalse($this->user->hasManagerRole());
        $this->assertFalse($this->user->hasRole('manager'));
    }

    #[Test]
    public function it_can_remove_manager_role_by_id()
    {
        $this->user->assignRole($this->managerRole);

        $result = $this->user->removeManagerRole($this->managerRole->id);

        $this->assertFalse($this->user->hasManagerRole());
        $this->assertFalse($this->user->hasRole($this->managerRole));
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
            'is_manager' => true
        ]);

        // User hat beide Manager-Rollen
        $this->user->assignRole([$this->managerRole, $secondManagerRole]);

        Cache::put($persistentKey, collect(['should' => 'remain']), 3600);

        // Eine Manager-Rolle entfernen
        $this->user->removeManagerRole($this->managerRole);

        // User sollte noch Manager sein
        $this->assertTrue($this->user->hasManagerRole());
        $this->assertTrue($this->user->hasRole($secondManagerRole));

        // Cache sollte nicht geleert werden
        $this->assertTrue(Cache::has($persistentKey));
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
        $this->assertTrue($this->user->hasManagerRole());
        $this->assertFalse($this->user->hasRole($this->regularRole));

        // Cache sollte nicht geleert werden
        $this->assertTrue(Cache::has($persistentKey));
    }

    #[Test]
    public function it_can_get_company_managers()
    {
        // Weitere Manager erstellen
        $manager2 = User::factory()->create(['company_id' => $this->company->id]);
        $manager2->assignRole($this->managerRole);

        $manager3 = User::factory()->create(['company_id' => $this->company->id]);
        $manager3->assignRole($this->managerRole);

        // Normale User erstellen (kein Manager)
        $regularUser = User::factory()->create(['company_id' => $this->company->id]);
        $regularUser->assignRole($this->regularRole);

        // User aus anderer Company
        $otherCompany = Company::factory()->create();
        $otherManager = User::factory()->create(['company_id' => $otherCompany->id]);
        $otherManager->assignRole($this->managerRole);

        // Manager abrufen
        $managers = User::getCompanyManagers($this->company->id);

        $this->assertInstanceOf(Collection::class, $managers);
        $this->assertCount(3, $managers); // managerUser, manager2, manager3

        // Prüfen ob nur Manager der richtigen Company
        $managerIds = $managers->pluck('id')->toArray();
        $this->assertContains($this->managerUser->id, $managerIds);
        $this->assertContains($manager2->id, $managerIds);
        $this->assertContains($manager3->id, $managerIds);
        $this->assertNotContains($regularUser->id, $managerIds);
        $this->assertNotContains($otherManager->id, $managerIds);

        // Prüfen ob die richtigen Felder zurückgegeben werden
        $firstManager = $managers->first();
        $this->assertArrayHasKey('id', $firstManager->toArray());
        $this->assertArrayHasKey('name', $firstManager->toArray());
        $this->assertArrayHasKey('last_name', $firstManager->toArray());
        $this->assertArrayHasKey('profile_photo_path', $firstManager->toArray());
    }

    #[Test]
    public function it_caches_company_managers()
    {
        $companyId = $this->company->id;
        $persistentKey = "users:company:{$companyId}:managers";

        // Erste Abfrage - sollte Cache füllen
        $managers1 = User::getCompanyManagers($companyId);
        $this->assertTrue(Cache::has($persistentKey));

        // Zweite Abfrage - sollte aus Cache kommen
        $managers2 = User::getCompanyManagers($companyId);

        $this->assertEquals($managers1->toArray(), $managers2->toArray());
    }

    #[Test]
    public function it_returns_empty_collection_when_no_managers_exist()
    {
        $companyWithoutManagers = Company::factory()->create();

        $managers = User::getCompanyManagers($companyWithoutManagers->id);

        $this->assertInstanceOf(Collection::class, $managers);
        $this->assertCount(0, $managers);
    }

    #[Test]
    public function it_excludes_soft_deleted_managers()
    {
        // Manager soft delete
        $this->managerUser->delete();

        $managers = User::getCompanyManagers($this->company->id);

        $this->assertCount(0, $managers);
        $this->assertNotContains($this->managerUser->id, $managers->pluck('id'));
    }

    #[Test]
    public function it_orders_managers_by_name()
    {
        // Manager mit verschiedenen Namen erstellen
        $managerZ = User::factory()->create([
            'company_id' => $this->company->id,
            'name' => 'Zoe'
        ]);
        $managerZ->assignRole($this->managerRole);

        $managerA = User::factory()->create([
            'company_id' => $this->company->id,
            'name' => 'Alice'
        ]);
        $managerA->assignRole($this->managerRole);

        $managers = User::getCompanyManagers($this->company->id);

        // Prüfen ob nach Namen sortiert
        $names = $managers->pluck('name')->toArray();
        $sortedNames = collect($names)->sort()->values()->toArray();

        $this->assertEquals($sortedNames, $names);
    }

    #[Test]
    public function it_removes_duplicates_when_user_has_multiple_manager_roles()
    {
        // Zweite Manager-Rolle erstellen
        $secondManagerRole = Role::create([
            'name' => 'senior_manager',
            'guard_name' => 'web',
            'is_manager' => true
        ]);

        // User beide Manager-Rollen zuweisen
        $this->managerUser->assignRole($secondManagerRole);

        $managers = User::getCompanyManagers($this->company->id);

        // User sollte nur einmal in der Liste stehen
        $userOccurrences = $managers->where('id', $this->managerUser->id)->count();
        $this->assertEquals(1, $userOccurrences);
    }
}
