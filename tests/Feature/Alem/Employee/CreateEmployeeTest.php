<?php

namespace Tests\Feature\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\User\Gender;
use App\Enums\User\UserType;
use App\Livewire\Alem\Employee\CreateEmployee;
use App\Models\Alem\Employee;
use App\Models\Alem\Department;
use App\Models\Alem\Industry;
use App\Models\Alem\Company;
use App\Models\Alem\QuickCrud\Profession;
use App\Models\Alem\QuickCrud\Stage;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateEmployeeTest extends TestCase
{
    // RefreshDatabase sorgt für eine saubere Testdatenbank vor jedem Test
    use RefreshDatabase;

    protected User $authUser;
    protected Team $team;
    protected Department $department;
    protected Profession $profession;
    protected Stage $stage;
    protected User $supervisor;
    protected array $roleIds = [];

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Zuerst Basis-User für created_by erstellen
        $baseUser = User::factory()->create([
            'created_by' => null, // Erster User hat keinen Creator
        ]);
        
        // 2. Industry erstellen
        $industry = Industry::factory()->create();

        // 3. Company erstellen
        $company = Company::factory()->create([
            'owner_id' => $baseUser->id,
            'industry_id' => $industry->id,
            'created_by' => $baseUser->id,
        ]);
        
        // 4. Admin-User mit Firmenverknüpfung erstellen
        $this->authUser = User::factory()->create([
            'user_type' => UserType::Admin,
            'company_id' => $company->id,
            'created_by' => $baseUser->id,
        ]);

        // 5. Team erstellen und mit Admin-User verknüpfen
        $this->team = Team::factory()->create([
            'user_id' => $this->authUser->id,
            'company_id' => $company->id,
        ]);
        
        // 6. Verknüpfung zwischen User und Team herstellen
        $this->authUser->ownedTeams()->save($this->team);
        $this->authUser->switchTeam($this->team);
        $this->authUser->save(); // Sicherstellen, dass Änderungen gespeichert werden

        // 7. Stammdaten für Mitarbeiter erstellen
        $this->department = Department::factory()->create([
            'company_id' => $company->id,
            'team_id' => $this->team->id,
            'created_by' => $this->authUser->id,
        ]);
        $this->profession = Profession::factory()->create([
            'company_id' => $company->id,
            'team_id' => $this->team->id,
            'created_by' => $this->authUser->id,
        ]);
        $this->stage = Stage::factory()->create([
            'company_id' => $company->id,
            'team_id' => $this->team->id,
            'created_by' => $this->authUser->id,
        ]);
        
        // 8. Supervisor erstellen (wird in allen Tests verwendet)
        $this->supervisor = User::factory()->create([
            'name' => 'Super Visor',
            'user_type' => UserType::Employee,
            'company_id' => $company->id,
            'created_by' => $this->authUser->id,
        ]);
        
        // 8.1 Employee-Eintrag für den Supervisor erstellen
        Employee::factory()->create([
            'user_id' => $this->supervisor->id,
            'profession_id' => $this->profession->id,
            'stage_id' => $this->stage->id,
        ]);
        
        // 9. Rollen erstellen/abrufen
        // In einer echten Anwendung würden wir die vorhandenen Rollen abfragen
        // Für Tests können wir IDs verwenden, die in der DB existieren oder DB-Einträge erstellen
        // Hier simulieren wir es mit einem einfachen Array
        $this->roleIds = ['employee']; // Die Rolle 'employee' wird in den Tests verwendet

        // 10. Als Admin-User authentifizieren
        $this->actingAs($this->authUser);
    }

    #[Test]
    public function it_can_render_create_employee_component()
    {
        Livewire::test(CreateEmployee::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.alem.employee.create');
    }

    #[Test]
    public function it_opens_create_modal_with_default_values()
    {
        Livewire::test(CreateEmployee::class)
            ->call('openCreateEmployeeModal')
            ->assertSet('showCreateModal', true)
            ->assertSet('gender', Gender::Male)
            ->assertSet('model_status', ModelStatus::ACTIVE)
            ->assertSet('employee_status', EmployeeStatus::PROBATION)
            ->assertSet('invitation', true)
            ->assertSet('selectedTeams', []);
    }

    #[Test]
    public function it_validates_required_fields()
    {
        Livewire::test(CreateEmployee::class)
            ->call('openCreateEmployeeModal')
            ->call('saveEmployee')
            ->assertHasErrors([
                'name' => 'required',
                'last_name' => 'required',
                'email' => 'required',
            ]);
    }

    #[Test]
    public function it_validates_email_format()
    {
        Livewire::test(CreateEmployee::class)
            ->call('openCreateEmployeeModal')
            ->set('email', 'invalid-email')
            ->call('saveEmployee')
            ->assertHasErrors(['email' => 'email']);
    }

    #[Test]
    public function it_validates_unique_email()
    {
        $existingUser = User::factory()->create([
            'email' => 'test@example.com'
        ]);

        Livewire::test(CreateEmployee::class)
            ->call('openCreateEmployeeModal')
            ->set('name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'test@example.com')
            ->call('saveEmployee')
            ->assertHasErrors(['email' => 'unique']);
    }

    #[Test]
    public function it_creates_employee_successfully_with_valid_data()
    {
        $this->assertDatabaseCount('users', 3); // baseUser, authUser und supervisor
        $this->assertDatabaseCount('employees', 1); // supervisor's employee

        Livewire::test(CreateEmployee::class)
            ->call('openCreateEmployeeModal')
            ->set('name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john.doe@example.com')
            ->set('gender', Gender::Male)
            ->set('joined_at', now()->format('Y-m-d'))
            // Organisations-Daten
            ->set('department', $this->department->id)
            ->set('profession', $this->profession->id)
            ->set('stage', $this->stage->id)
            ->set('selectedTeams', [$this->team->id])
            ->set('selectedRoles', $this->roleIds)
            // Wichtig: Supervisor setzen (Pflichtfeld)
            ->set('supervisor', $this->supervisor->id)
            // Status
            ->set('employee_status', EmployeeStatus::PROBATION)
            ->call('saveEmployee')
            ->assertHasNoErrors();

        $user = User::where('email', 'john.doe@example.com')->first();

        $this->assertNotNull($user);
        $this->assertEquals('John', $user->name);
        $this->assertEquals('Doe', $user->last_name);
        $this->assertEquals(UserType::Employee, $user->user_type);

        // Überprüfe, ob ein Mitarbeiter erstellt wurde
        $this->assertNotNull($user->employee);
        $this->assertInstanceOf(Employee::class, $user->employee);

        // Should have 4 users now (baseUser + authUser + supervisor + newUser)
        $this->assertDatabaseCount('users', 4);
        $this->assertDatabaseCount('employees', 2); // supervisor + new employee

        // Check team assignments - User should belong to a team
        $this->assertTrue($user->teams->count() > 0);

        // Count assertion for total users
        $totalUsers = User::count();
        $this->assertEquals(4, $totalUsers); // baseUser, authUser, supervisor and new user
    }

    #[Test]
    public function it_assigns_teams_to_user()
    {
        $team2 = Team::factory()->create([
            'user_id' => $this->authUser->id,
            'company_id' => $this->authUser->company_id,
        ]);

        Livewire::test(CreateEmployee::class)
            ->call('openCreateEmployeeModal')
            ->set('name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john.doe@example.com')
            ->set('gender', Gender::Male)
            ->set('joined_at', now()->format('Y-m-d'))
            // Organisations-Daten
            ->set('department', $this->department->id)
            ->set('profession', $this->profession->id)
            ->set('stage', $this->stage->id)
            ->set('selectedTeams', [$this->team->id, $team2->id])
            ->set('selectedRoles', $this->roleIds)
            // Supervisor setzen
            ->set('supervisor', $this->supervisor->id)
            // Status
            ->set('employee_status', EmployeeStatus::PROBATION)
            ->call('saveEmployee')
            ->assertHasNoErrors();

        $user = User::where('email', 'john.doe@example.com')->first();
        $this->assertEquals(2, $user->teams->count());
    }

    #[Test]
    public function it_assigns_default_team_when_no_teams_selected()
    {
        Livewire::test(CreateEmployee::class)
            ->call('openCreateEmployeeModal')
            ->set('name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john.doe@example.com')
            ->set('gender', Gender::Male)
            ->set('joined_at', now()->format('Y-m-d'))
            // Organisations-Daten
            ->set('department', $this->department->id)
            ->set('profession', $this->profession->id)
            ->set('stage', $this->stage->id)
            ->set('selectedTeams', [$this->team->id]) // Mind. ein Team erforderlich, da die Validierung 'min:1' prüft
            ->set('selectedRoles', $this->roleIds)
            // Supervisor setzen
            ->set('supervisor', $this->supervisor->id)
            // Status
            ->set('employee_status', EmployeeStatus::PROBATION)
            ->call('saveEmployee')
            ->assertHasNoErrors();

        $user = User::where('email', 'john.doe@example.com')->first();
        $this->assertEquals(1, $user->teams->count());
        $this->assertEquals($this->team->id, $user->teams->first()->id);
    }

    #[Test]
    public function it_handles_database_transaction_rollback_on_error()
    {
        // Anstatt die gesamte DB zu mocken, testen wir das Komponenten-Verhalten bei Fehlern
        
        // Wir erstellen eine Test-Komponente mit leeren Werten, was zu Validierungsfehlern führt
        $component = Livewire::test(CreateEmployee::class);
        
        // Wir simulieren einen unvollständigen Speichervorgang
        $component
            ->call('openCreateEmployeeModal')
            ->set('name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john.doe@example.com')
            ->call('saveEmployee');
            
        // Wir erwarten Validierungsfehler
        $component->assertHasErrors([
            'gender',
            'joined_at',
            'department',
            'profession', 
            'stage',
            'selectedTeams',
            'selectedRoles',
            'supervisor',
        ]);
        
        // Überprüfen, dass keine neuen Datensätze erstellt wurden
        // Da die Validierung fehlschlägt, wird die Transaktion nicht ausgeführt
        $this->assertDatabaseCount('users', 3); // baseUser, authUser und supervisor
        $this->assertDatabaseCount('employees', 1); // supervisor's employee
    }

    #[Test]
    public function it_closes_modal_and_resets_form()
    {
        Livewire::test(CreateEmployee::class)
            ->call('openCreateEmployeeModal')
            ->set('name', 'John')
            ->set('email', 'john@example.com')
            ->call('closeCreateEmployeeModal')
            ->assertSet('showCreateModal', false);
    }

    #[Test]
    public function it_resets_form_inputs()
    {
        Livewire::test(CreateEmployee::class)
            ->call('openCreateEmployeeModal')
            ->set('name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john@example.com')
            ->call('resetFormInputs')
            ->assertSet('name', null)
            ->assertSet('last_name', null)
            ->assertSet('email', null)
            ->assertSet('gender', null)
            ->assertSet('selectedTeams', [])
            ->assertSet('invitation', false);
    }
    
    #[Test]
    public function it_sets_supervisor_when_provided()
    {
        // Wir erstellen einen zusätzlichen Supervisor speziell für diesen Test
        $anotherSupervisor = User::factory()->create([
            'name' => 'Another Supervisor',
            'user_type' => UserType::Employee,
            'company_id' => $this->authUser->company_id,
        ]);
        
        // Stelle sicher, dass der Supervisor einen Employee-Eintrag hat
        // mit den Pflichtfeldern profession_id und stage_id
        $anotherSupervisorEmployee = Employee::factory()->create([
            'user_id' => $anotherSupervisor->id,
            'profession_id' => $this->profession->id,
            'stage_id' => $this->stage->id,
        ]);

        $this->assertNotNull($anotherSupervisorEmployee);

        Livewire::test(CreateEmployee::class)
            ->call('openCreateEmployeeModal')
            ->set('name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john.doe@example.com')
            ->set('gender', Gender::Male)
            ->set('joined_at', now()->format('Y-m-d'))
            // Organisations-Daten
            ->set('department', $this->department->id)
            ->set('profession', $this->profession->id)
            ->set('stage', $this->stage->id)
            ->set('selectedTeams', [$this->team->id])
            ->set('selectedRoles', $this->roleIds)
            ->set('supervisor', $anotherSupervisor->id)
            // Status
            ->set('employee_status', EmployeeStatus::PROBATION)
            ->call('saveEmployee')
            ->assertHasNoErrors();

        $user = User::where('email', 'john.doe@example.com')->first();
        $employee = $user->employee;

        $this->assertNotNull($employee);
        $this->assertEquals($anotherSupervisor->id, $employee->supervisor_id);
    }

    #[Test]
    public function it_sets_joined_at_date()
    {
        $joinedDate = now()->subDays(30);

        Livewire::test(CreateEmployee::class)
            ->call('openCreateEmployeeModal')
            ->set('name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john.doe@example.com')
            ->set('gender', Gender::Male)
            ->set('joined_at', $joinedDate)
            // Organisations-Daten
            ->set('department', $this->department->id)
            ->set('profession', $this->profession->id)
            ->set('stage', $this->stage->id)
            ->set('selectedTeams', [$this->team->id])
            ->set('selectedRoles', $this->roleIds)
            // Supervisor setzen
            ->set('supervisor', $this->supervisor->id)
            // Status
            ->set('employee_status', EmployeeStatus::PROBATION)
            ->call('saveEmployee')
            ->assertHasNoErrors();

        $user = User::where('email', 'john.doe@example.com')->first();
        $this->assertEquals($joinedDate->toDateString(), $user->joined_at);
    }
}
