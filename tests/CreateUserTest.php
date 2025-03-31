<?php

use App\Actions\Fortify\CreateNewUser;
use App\Enums\Company\CompanySize;
use App\Enums\Company\CompanyType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateUserTest extends TestCase
{
    /**
     * Test, ob ein neuer Benutzer erstellt werden kann.
     *
     * @return void
     */
    public function test_create_new_user()
    {
        // Erstelle einen Test-Benutzer mit allen notwendigen Daten
        $userData = [
            'name' => 'Test',
            'last_name' => 'Benutzer',
            'company_name' => 'Test GmbH',
            'email' => 'test.benutzer@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'company_type' => CompanyType::GmbH->value,
            'company_size' => CompanySize::OneToFive->value,
            'industry_id' => 1, // Sicherstellen, dass diese ID existiert
            'terms' => true,
        ];

        // Rufe die CreateNewUser-Klasse auf
        $createNewUser = new CreateNewUser();
        $user = $createNewUser->create($userData);

        // Überprüfe, ob der Benutzer erstellt wurde
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test', $user->name);
        $this->assertEquals('Benutzer', $user->last_name);
        $this->assertEquals('test.benutzer@example.com', $user->email);

        // Überprüfe, ob die Beziehungen korrekt angelegt wurden
        $this->assertNotNull($user->company_id);
        $this->assertNotNull($user->company);
        $this->assertEquals('Test GmbH', $user->company->company_name);

        // Überprüfe, ob ein Team erstellt wurde
        $this->assertTrue($user->ownedTeams()->exists());
        $this->assertEquals($user->company_id, $user->ownedTeams()->first()->company_id);
        
        echo "Der Test wurde erfolgreich durchgeführt! Ein neuer Benutzer mit zugehörigem Unternehmen und Team wurde erstellt.";
    }
}
