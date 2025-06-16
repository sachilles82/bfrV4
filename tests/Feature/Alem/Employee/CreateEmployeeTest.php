<?php

namespace Tests\Feature\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\User\Gender;
use App\Enums\User\UserType;
use App\Livewire\Alem\Employee\CreateEmployee;
use App\Models\Alem\Employee;
use App\Models\Alem\Department;
use App\Models\Alem\QuickCrud\Profession;
use App\Models\Alem\QuickCrud\Stage;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateEmployeeTest extends TestCase
{
    use RefreshDatabase;

    protected User $authUser;
    protected Team $team;
    protected Department $department;
    protected Profession $profession;
    protected Stage $stage;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup test data
        $this->authUser = User::factory()->create([
            'user_type' => UserType::Admin,
            'company_id' => 1,
        ]);

        $this->team = Team::factory()->create([
            'user_id' => $this->authUser->id,
        ]);

        $this->department = Department::factory()->create();
        $this->profession = Profession::factory()->create();
        $this->stage = Stage::factory()->create();

        $this->actingAs($this->authUser);
        $this->authUser->switchTeam($this->team);
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
            ->assertSet('selectedTeams', [$this->team->id]);
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
        $this->assertDatabaseCount('users', 1); // Only auth user
        $this->assertDatabaseCount('employees', 0);

        Livewire::test(CreateEmployee::class)
            ->call('openCreateEmployeeModal')
            ->set('name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john.doe@example.com')
            ->set('gender', Gender::Male)
            ->set('department', $this->department->id)
            ->set('profession', $this->profession->id)
            ->set('stage', $this->stage->id)
            ->set('model_status', ModelStatus::ACTIVE)
            ->set('employee_status', EmployeeStatus::PROBATION)
            ->call('saveEmployee')
            ->assertHasNoErrors()
            ->assertSet('showCreateModal', false)
            ->assertDispatched('employee-created');

        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseCount('employees', 1);

        $user = User::where('email', 'john.doe@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('John', $user->name);
        $this->assertEquals('Doe', $user->last_name);
        $this->assertEquals(Gender::Male, $user->gender);
        $this->assertEquals(UserType::Employee, $user->user_type);
        $this->assertEquals($this->authUser->company_id, $user->company_id);
        $this->assertEquals($this->authUser->id, $user->created_by);
        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue(Hash::check('password', $user->password) === false); // Password should be randomly generated

        $employee = $user->employee;
        $this->assertNotNull($employee);
        $this->assertEquals($this->profession->id, $employee->profession_id);
        $this->assertEquals($this->stage->id, $employee->stage_id);
        $this->assertEquals(EmployeeStatus::PROBATION, $employee->employee_status);
    }

    #[Test]
    public function it_assigns_teams_to_user()
    {
        $additionalTeam = Team::factory()->create([
            'user_id' => $this->authUser->id,
        ]);

        Livewire::test(CreateEmployee::class)
            ->call('openCreateEmployeeModal')
            ->set('name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john.doe@example.com')
            ->set('selectedTeams', [$this->team->id, $additionalTeam->id])
            ->call('saveEmployee')
            ->assertHasNoErrors();

        $user = User::where('email', 'john.doe@example.com')->first();
        $this->assertTrue($user->teams->contains($this->team));
        $this->assertTrue($user->teams->contains($additionalTeam));
        $this->assertEquals('member', $user->teams->first()->pivot->role);
    }

    #[Test]
    public function it_assigns_default_team_when_no_teams_selected()
    {
        Livewire::test(CreateEmployee::class)
            ->call('openCreateEmployeeModal')
            ->set('name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john.doe@example.com')
            ->set('selectedTeams', [])
            ->call('saveEmployee')
            ->assertHasNoErrors();

        $user = User::where('email', 'john.doe@example.com')->first();
        $this->assertTrue($user->teams->contains($this->team));
        $this->assertEquals('member', $user->teams->first()->pivot->role);
    }

    #[Test]
    public function it_handles_database_transaction_rollback_on_error()
    {
        // Mock DB to throw an exception during transaction
        DB::shouldReceive('transaction')
            ->once()
            ->andThrow(new \Exception('Database error'));

        $this->assertDatabaseCount('users', 1); // Only auth user
        $this->assertDatabaseCount('employees', 0);

        Livewire::test(CreateEmployee::class)
            ->call('openCreateEmployeeModal')
            ->set('name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john.doe@example.com')
            ->call('saveEmployee');

        // Database should remain unchanged due to transaction rollback
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('employees', 0);
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
        $supervisor = User::factory()->create([
            'company_id' => $this->authUser->company_id,
        ]);

        Employee::factory()->create([
            'user_id' => $supervisor->id,
        ]);

        Livewire::test(CreateEmployee::class)
            ->call('openCreateEmployeeModal')
            ->set('name', 'John')
            ->set('last_name', 'Doe')
            ->set('email', 'john.doe@example.com')
            ->set('supervisor', $supervisor->employee->id)
            ->call('saveEmployee')
            ->assertHasNoErrors();

        $user = User::where('email', 'john.doe@example.com')->first();
        $this->assertEquals($supervisor->employee->id, $user->employee->supervisor_id);
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
            ->set('joined_at', $joinedDate)
            ->call('saveEmployee')
            ->assertHasNoErrors();

        $user = User::where('email', 'john.doe@example.com')->first();
        $this->assertEquals($joinedDate->toDateString(), $user->joined_at);
    }
}
