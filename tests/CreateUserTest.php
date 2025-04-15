<?php

use App\Actions\Fortify\CreateNewUser;
use App\Enums\Company\CompanySize;
use App\Enums\Company\CompanyType;
use App\Models\User;
test('create new user', function () {
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
    $createNewUser = new CreateNewUser;
    $user = $createNewUser->create($userData);

    // Überprüfe, ob der Benutzer erstellt wurde
    expect($user)->toBeInstanceOf(User::class);
    expect($user->name)->toEqual('Test');
    expect($user->last_name)->toEqual('Benutzer');
    expect($user->email)->toEqual('test.benutzer@example.com');

    // Überprüfe, ob die Beziehungen korrekt angelegt wurden
    expect($user->company_id)->not->toBeNull();
    expect($user->company)->not->toBeNull();
    expect($user->company->company_name)->toEqual('Test GmbH');

    // Überprüfe, ob ein Team erstellt wurde
    expect($user->ownedTeams()->exists())->toBeTrue();
    expect($user->ownedTeams()->first()->company_id)->toEqual($user->company_id);

    echo 'Der Test wurde erfolgreich durchgeführt! Ein neuer Benutzer mit zugehörigem Unternehmen und Team wurde erstellt.';
});
