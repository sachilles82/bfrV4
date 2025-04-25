<?php

namespace Database\Seeders\User;

use App\Enums\Company\CompanyRegistrationType;
use App\Enums\Company\CompanySize;
use App\Enums\Company\CompanyType;
use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\User\UserType;
use App\Models\Alem\Company;
use App\Models\Alem\Department;
use App\Models\Alem\Employee\Employee;
use App\Models\Alem\Employee\Setting\Profession;
use App\Models\Alem\Employee\Setting\Stage;
use App\Models\Alem\Industry;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

// Enum imports added for clarity
use App\Enums\Role\RoleVisibility;
use App\Enums\Role\RoleHasAccessTo;


class TestDataSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Initialisiere Faker für deutsche Namen
        $faker = Faker::create('de_DE');

        // Erhöhe das PHP Memory Limit für große Datensätze
        ini_set('memory_limit', '2G');

        // Deaktiviere Model Events für schnelleres Seeding
        Model::unguard();

        // Deaktiviere Query-Logging für bessere Performance
        DB::disableQueryLog();

        try {
            // Parameter für das Seeden
            $employeeCount = 2500; // Anzahl der zu erstellenden Mitarbeiter für Team 1 (Betrieb 48)
            $managerCount = 100;    // Anzahl der Manager für Team 1
            $chunkSize = 7500;      // Größere Chunks für bessere Performance

            // Parameter für Betrieb 55 (Team 2)
            $team2EmployeeCount = 1500; // Anzahl der Mitarbeiter für Team 2 (Betrieb 55)
            $team2ManagerCount = 4;   // Anzahl der Manager für Team 2
            $team2DepartmentCount = 4; // Anzahl der Abteilungen für Team 2
            $team2ProfessionCount = 10; // Anzahl der Berufe für Team 2
            $team2StageCount = 10;     // Anzahl der Stufen für Team 2

            // *** NEU: Parameter für Betrieb 56 (Team 3) ***
            $team3EmployeeCount = 500; // Beispiel: Anzahl der Mitarbeiter für Team 3 (Betrieb 56)
            $team3ManagerCount = 2;   // Beispiel: Anzahl der Manager für Team 3
            $team3DepartmentCount = 3; // Beispiel: Anzahl der Abteilungen für Team 3
            $team3ProfessionCount = 5; // Beispiel: Anzahl der Berufe für Team 3
            $team3StageCount = 5;     // Beispiel: Anzahl der Stufen für Team 3


            $this->command->info('Starte Erstellung der Testdaten...');

            // 1. Lösche alte Testdaten (falls vorhanden)
            $this->command->info('Entferne alte Testdaten (falls vorhanden)...');

            // Prüfe, ob ein Owner-Benutzer existiert und lösche ihn
            $ownerUser = User::where('email', 'daniel@firma.ch')->first();
            if ($ownerUser) {
                $companyId = $ownerUser->company_id;

                // Beginne eine Transaktion für die Löschvorgänge
                DB::beginTransaction();

                // Lösche alle abhängigen Datensätze
                $this->command->info('Lösche vorhandene Mitarbeiter-Datensätze...');
                Employee::whereHas('user', function ($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                })->delete();

                // Lösche Benutzer außer dem Owner
                User::where('company_id', $companyId)
                    ->where('id', '!=', $ownerUser->id)
                    ->forceDelete(); // Verwende forceDelete, um Soft Deletes zu umgehen

                // Lösche vorhandene Berufe, Stufen und Abteilungen
                $this->command->info('Lösche vorhandene Berufe, Stufen und Abteilungen...');
                Profession::where('company_id', $companyId)->delete();
                Stage::where('company_id', $companyId)->delete();
                Department::where('company_id', $companyId)->delete();

                // Lösche die Teams des Owners (inkl. Pivot-Einträge)
                $ownerUser->teams()->detach();
                Team::where('company_id', $companyId)->delete(); // Löscht alle Teams der Firma

                // Lösche das Unternehmen
                Company::where('id', $companyId)->delete();

                // Lösche den Owner selbst
                $ownerUser->forceDelete(); // Verwende forceDelete

                // Bestätige die Löschung
                DB::commit();
                $this->command->info('Alte Testdaten entfernt.');
            } else {
                $this->command->info('Keine alten Testdaten für daniel@firma.ch gefunden.');
            }


            // 2. Erstelle den Hauptbenutzer (Owner) - Daniel Skrbac
            $this->command->info('Erstelle Owner-Benutzer Daniel Skrbac...');

            // Starte eine neue Transaktion für die Erstellung
            DB::beginTransaction();

            // Erstelle den Hauptbenutzer
            $password = Hash::make('password');
            $owner = User::firstOrCreate(
                ['email' => 'daniel@firma.ch'],
                [
                    'name' => 'Daniel',
                    'last_name' => 'Skrbac',
                    'email' => 'daniel@firma.ch',
                    'email_verified_at' => now(),
                    'password' => $password,
                    'remember_token' => Str::random(10),
                    'user_type' => UserType::Owner,
                    'model_status' => ModelStatus::ACTIVE,
                    'slug' => 'daniel-skrbac-'.Str::random(5), // Eindeutiger Slug mit Zufallszeichen
                ]
            );

            // Erstelle eine Industrie (falls noch keine existiert)
            $this->command->info('Erstelle Industrie...');
            $industry = Industry::firstOrCreate(
                ['name' => 'IT'],
                ['name' => 'IT']
            );

            // 3. Erstelle ein Unternehmen
            $this->command->info('Erstelle Unternehmen...');
            $company = Company::create([
                'company_name' => 'Dani AG',
                'email' => 'info@firma.ch',
                'phone_1' => '+41 44 401 11 42',
                'is_active' => true,
                'owner_id' => $owner->id,
                'created_by' => $owner->id,
                'company_type' => CompanyType::AG,
                'company_size' => CompanySize::OneHundredOneToTwoHundred,
                'registration_type' => CompanyRegistrationType::SELF_REGISTERED,
                'industry_id' => $industry->id, // Industrie-ID verwenden
                'company_url' => 'dani-ag', // Beispiel URL
            ]);

            // Verknüpfe den Benutzer mit dem Unternehmen
            $owner->company_id = $company->id;
            $owner->save();

            // 4. Erstelle Teams
            $this->command->info('Erstelle Teams...');

            // Team 1 - Betrieb 48
            $team1 = Team::create([
                'name' => 'Betrieb 48',
                'user_id' => $owner->id,
                'company_id' => $company->id,
                'personal_team' => true, // Annahme: Das erste Team ist das persönliche Team
            ]);

            // Team 2 - Betrieb 55
            $team2 = Team::create([
                'name' => 'Betrieb 55',
                'user_id' => $owner->id,
                'company_id' => $company->id,
                'personal_team' => false,
            ]);

            // *** NEU: Team 3 - Betrieb 56 ***
            $team3 = Team::create([
                'name' => 'Betrieb 56',
                'user_id' => $owner->id,
                'company_id' => $company->id,
                'personal_team' => false,
            ]);

            // Enum-Werte holen
            $visibleValue = RoleVisibility::Visible->value;
            $employeePanelValue = RoleHasAccessTo::EmployeePanel->value;

            // Rollen-Daten vorbereiten
            $roleData = [
                'Worker' => [
                    'name' => 'Worker',
                    'guard_name' => 'web',
                    'created_by' => $owner->id,
                    'company_id' => $company->id,
                    'access' => $employeePanelValue,
                    'visible' => $visibleValue,
                    'is_manager' => false,
                ],
                'Manager' => [
                    'name' => 'Manager',
                    'guard_name' => 'web',
                    'created_by' => $owner->id,
                    'company_id' => $company->id,
                    'access' => $employeePanelValue,
                    'visible' => $visibleValue,
                    'is_manager' => true,
                ],
                'Editor' => [
                    'name' => 'Editor',
                    'guard_name' => 'web',
                    'created_by' => $owner->id,
                    'company_id' => $company->id,
                    'access' => $employeePanelValue,
                    'visible' => $visibleValue,
                    'is_manager' => false,
                ],
                'Temporary' => [
                    'name' => 'Temporary',
                    'guard_name' => 'web',
                    'created_by' => $owner->id,
                    'company_id' => $company->id,
                    'access' => $employeePanelValue,
                    'visible' => $visibleValue,
                    'is_manager' => false,
                ],
                // Owner Rolle für den Hauptbenutzer
                'owner' => [
                    'name' => 'owner',
                    'guard_name' => 'web',
                    'created_by' => $owner->id, // Oder System-ID, falls zutreffend
                    'company_id' => $company->id,
                    'access' => RoleHasAccessTo::OwnerPanel->value, // Beispiel-Zugang
                    'visible' => RoleVisibility::Hidden->value, // Standardrollen oft versteckt
                    'is_manager' => true, // Owner ist oft Manager
                ],
            ];

            // Rollen erstellen oder aktualisieren
            $roles = [];
            foreach ($roleData as $roleName => $data) {
                $role = Role::firstOrCreate(['name' => $roleName, 'company_id' => $company->id], $data);
                $roles[$roleName] = $role->id;
            }

            // 5. Weise die Rolle zu
            $this->command->info('Weise Rolle zu...');
            $owner->assignRole('owner'); // Verwende den Rollennamen

            // Setze das Team als current_team_id für den Owner
            $owner->current_team_id = $team1->id;
            $owner->save();

            // Commit die Transaktion für Basis-Entitäten
            DB::commit();

            // --- Erstellung der Daten für Team 1 ---
            $this->command->info('Erstelle abhängige Daten für Betrieb 48 (Team 1)...');
            // 6. Erstelle Abteilungen für Team 1 (Betrieb 48)
            $this->command->info('Erstelle 100 Abteilungen für Betrieb 48...');
            $departmentChunks = array_chunk(range(1, 100), min(100, $chunkSize));
            $this->command->getOutput()->progressStart(100);
            foreach ($departmentChunks as $chunk) {
                DB::beginTransaction();
                $departments = [];
                foreach ($chunk as $i) {
                    $departments[] = [
                        'name' => 'Abteilung '.$i, 'description' => 'Beschreibung für Abteilung '.$i,
                        'company_id' => $company->id, 'team_id' => $team1->id, 'created_by' => $owner->id,
                        'model_status' => ModelStatus::ACTIVE->value, 'created_at' => now(), 'updated_at' => now(),
                    ];
                    $this->command->getOutput()->progressAdvance();
                }
                Department::insert($departments);
                DB::commit();
            }
            $this->command->getOutput()->progressFinish(); $this->command->newLine();

            // 7. Erstelle Berufe für Team 1 (Betrieb 48)
            $this->command->info('Erstelle 50 Berufe für Betrieb 48...');
            $professionChunks = array_chunk(range(1, 50), min(50, $chunkSize));
            $this->command->getOutput()->progressStart(50);
            foreach ($professionChunks as $chunk) {
                DB::beginTransaction();
                $professions = [];
                foreach ($chunk as $index) {
                    $professions[] = [
                        'name' => 'Beruf'.$index, 'company_id' => $company->id, 'team_id' => $team1->id,
                        'created_by' => $owner->id, 'created_at' => now(), 'updated_at' => now(),
                    ];
                    $this->command->getOutput()->progressAdvance();
                }
                $uniqueProfessions = collect($professions)->unique('name')->toArray();
                Profession::insert($uniqueProfessions);
                DB::commit();
            }
            $this->command->getOutput()->progressFinish(); $this->command->newLine();

            // 8. Erstelle definierte Stufen für Team 1 (Betrieb 48)
            $stageNames = ['Lehrling', 'Praktikant', 'Angelernt', 'Geselle', 'Facharbeiter', 'Meister', 'Experte', 'Leiter', 'Direktor', 'CEO', 'CTO', 'CFO', 'COO', 'CIO', 'CSO', 'CMO'];
            $this->command->info('Erstelle '.count($stageNames).' definierte Stufen für Betrieb 48...');
            $this->command->getOutput()->progressStart(count($stageNames));
            DB::beginTransaction();
            $stages = [];
            foreach ($stageNames as $stageName) {
                $stages[] = [
                    'name' => $stageName, 'company_id' => $company->id, 'team_id' => $team1->id,
                    'created_by' => $owner->id, 'created_at' => now(), 'updated_at' => now(),
                ];
                $this->command->getOutput()->progressAdvance();
            }
            Stage::insert($stages);
            DB::commit();
            $this->command->getOutput()->progressFinish(); $this->command->newLine();


            // --- Erstellung der Daten für Team 2 ---
            $this->command->info('Erstelle abhängige Daten für Betrieb 55 (Team 2)...');
            // Erstelle Abteilungen für Team 2 (Betrieb 55)
            $this->command->info('Erstelle '.$team2DepartmentCount.' Abteilungen für Betrieb 55...');
            $departmentsTeam2 = [];
            $this->command->getOutput()->progressStart($team2DepartmentCount);
            DB::beginTransaction();
            for ($i = 1; $i <= $team2DepartmentCount; $i++) {
                $departmentsTeam2[] = [
                    'name' => 'B55 Abteilung '.$i, 'description' => 'Beschreibung für B55 Abteilung '.$i,
                    'company_id' => $company->id, 'team_id' => $team2->id, 'created_by' => $owner->id,
                    'model_status' => ModelStatus::ACTIVE->value, 'created_at' => now(), 'updated_at' => now(),
                ];
                $this->command->getOutput()->progressAdvance();
            }
            Department::insert($departmentsTeam2);
            DB::commit();
            $this->command->getOutput()->progressFinish(); $this->command->newLine();

            // Erstelle Berufe für Team 2 (Betrieb 55)
            $this->command->info('Erstelle '.$team2ProfessionCount.' Berufe für Betrieb 55...');
            $professionsTeam2 = [];
            $this->command->getOutput()->progressStart($team2ProfessionCount);
            DB::beginTransaction();
            for ($i = 1; $i <= $team2ProfessionCount; $i++) {
                $professionsTeam2[] = [
                    'name' => 'B55 Beruf'.$i, 'company_id' => $company->id, 'team_id' => $team2->id,
                    'created_by' => $owner->id, 'created_at' => now(), 'updated_at' => now(),
                ];
                $this->command->getOutput()->progressAdvance();
            }
            Profession::insert($professionsTeam2);
            DB::commit();
            $this->command->getOutput()->progressFinish(); $this->command->newLine();

            // Erstelle Stufen für Team 2 (Betrieb 55)
            $stageNamesTeam2 = array_slice($stageNames, 0, $team2StageCount); // Nimm die ersten N Stufen aus der Hauptliste
            $this->command->info('Erstelle '.$team2StageCount.' Stufen für Betrieb 55...');
            $this->command->getOutput()->progressStart($team2StageCount);
            DB::beginTransaction();
            $stagesTeam2 = [];
            foreach ($stageNamesTeam2 as $stageName) {
                $stagesTeam2[] = [
                    'name' => 'B55 '.$stageName, 'company_id' => $company->id, 'team_id' => $team2->id,
                    'created_by' => $owner->id, 'created_at' => now(), 'updated_at' => now(),
                ];
                $this->command->getOutput()->progressAdvance();
            }
            Stage::insert($stagesTeam2);
            DB::commit();
            $this->command->getOutput()->progressFinish(); $this->command->newLine();


            // *** NEU: Erstellung der Daten für Team 3 ***
            $this->command->info('Erstelle abhängige Daten für Betrieb 56 (Team 3)...');
            // Erstelle Abteilungen für Team 3 (Betrieb 56)
            $this->command->info('Erstelle '.$team3DepartmentCount.' Abteilungen für Betrieb 56...');
            $departmentsTeam3 = [];
            $this->command->getOutput()->progressStart($team3DepartmentCount);
            DB::beginTransaction();
            for ($i = 1; $i <= $team3DepartmentCount; $i++) {
                $departmentsTeam3[] = [
                    'name' => 'B56 Abteilung '.$i, 'description' => 'Beschreibung für B56 Abteilung '.$i,
                    'company_id' => $company->id, 'team_id' => $team3->id, 'created_by' => $owner->id,
                    'model_status' => ModelStatus::ACTIVE->value, 'created_at' => now(), 'updated_at' => now(),
                ];
                $this->command->getOutput()->progressAdvance();
            }
            Department::insert($departmentsTeam3);
            DB::commit();
            $this->command->getOutput()->progressFinish(); $this->command->newLine();

            // Erstelle Berufe für Team 3 (Betrieb 56)
            $this->command->info('Erstelle '.$team3ProfessionCount.' Berufe für Betrieb 56...');
            $professionsTeam3 = [];
            $this->command->getOutput()->progressStart($team3ProfessionCount);
            DB::beginTransaction();
            for ($i = 1; $i <= $team3ProfessionCount; $i++) {
                $professionsTeam3[] = [
                    'name' => 'B56 Beruf'.$i, 'company_id' => $company->id, 'team_id' => $team3->id,
                    'created_by' => $owner->id, 'created_at' => now(), 'updated_at' => now(),
                ];
                $this->command->getOutput()->progressAdvance();
            }
            Profession::insert($professionsTeam3);
            DB::commit();
            $this->command->getOutput()->progressFinish(); $this->command->newLine();

            // Erstelle Stufen für Team 3 (Betrieb 56)
            $stageNamesTeam3 = array_slice($stageNames, 0, $team3StageCount); // Nimm die ersten N Stufen
            $this->command->info('Erstelle '.$team3StageCount.' Stufen für Betrieb 56...');
            $this->command->getOutput()->progressStart($team3StageCount);
            DB::beginTransaction();
            $stagesTeam3 = [];
            foreach ($stageNamesTeam3 as $stageName) {
                $stagesTeam3[] = [
                    'name' => 'B56 '.$stageName, 'company_id' => $company->id, 'team_id' => $team3->id,
                    'created_by' => $owner->id, 'created_at' => now(), 'updated_at' => now(),
                ];
                $this->command->getOutput()->progressAdvance();
            }
            Stage::insert($stagesTeam3);
            DB::commit();
            $this->command->getOutput()->progressFinish(); $this->command->newLine();


            // --- Lade IDs für Beziehungen (alle Teams) ---
            $this->command->info('Lade IDs für Beziehungen aller Teams...');
            // Team 1 IDs
            $departmentIdsTeam1 = Department::where('company_id', $company->id)->where('team_id', $team1->id)->pluck('id')->toArray();
            $professionIdsTeam1 = Profession::where('company_id', $company->id)->where('team_id', $team1->id)->pluck('id')->toArray();
            $stageIdsTeam1 = Stage::where('company_id', $company->id)->where('team_id', $team1->id)->pluck('id')->toArray();

            // Team 2 IDs
            $departmentIdsTeam2 = Department::where('company_id', $company->id)->where('team_id', $team2->id)->pluck('id')->toArray();
            $professionIdsTeam2 = Profession::where('company_id', $company->id)->where('team_id', $team2->id)->pluck('id')->toArray();
            $stageIdsTeam2 = Stage::where('company_id', $company->id)->where('team_id', $team2->id)->pluck('id')->toArray();

            // *** NEU: Team 3 IDs ***
            $departmentIdsTeam3 = Department::where('company_id', $company->id)->where('team_id', $team3->id)->pluck('id')->toArray();
            $professionIdsTeam3 = Profession::where('company_id', $company->id)->where('team_id', $team3->id)->pluck('id')->toArray();
            $stageIdsTeam3 = Stage::where('company_id', $company->id)->where('team_id', $team3->id)->pluck('id')->toArray();


            // Hole die Rollen-IDs (bereits oben erstellt)
            $workerRoleId = $roles['Worker'];
            $managerRoleId = $roles['Manager'];
            $editorRoleId = $roles['Editor'];
            $temporaryRoleId = $roles['Temporary'];
            $nonManagerRoleIds = [$workerRoleId, $editorRoleId, $temporaryRoleId]; // Rolle-IDs für Nicht-Manager

            // --- Mitarbeiter erstellen ---

            // 9. Erstelle Mitarbeiter für Team 1 (Betrieb 48)
            $this->command->info('Erstelle '.$employeeCount.' Mitarbeiter für Betrieb 48 in Chunks von '.$chunkSize.'...');
            $this->command->getOutput()->progressStart($employeeCount);
            $passwordHash = Hash::make('password');
            $managersCreatedTeam1 = 0; // Zähler für Manager in Team 1
            $currentEmployeeIndex = 0; // Globaler Zähler für E-Mail/Slug

            for ($i = 0; $i < $employeeCount; $i += $chunkSize) {
                DB::beginTransaction();
                $usersToInsert = []; $employeesToInsert = []; $roleAssignments = []; $teamAssignments = [];
                $currentTime = now();
                $limit = min($chunkSize, $employeeCount - $i);

                for ($j = 0; $j < $limit; $j++) {
                    $currentEmployeeIndex++;
                    $firstName = $faker->firstName; $lastName = $faker->lastName;
                    $email = strtolower(Str::slug($firstName)).'.'.strtolower(Str::slug($lastName)).'.'.$currentEmployeeIndex.'@firma.ch';
                    $joinedDate = Carbon::now()->subDays(rand(0, 365 * 3));

                    $usersToInsert[] = [
                        'name' => $firstName, 'last_name' => $lastName, 'email' => $email,
                        'email_verified_at' => $currentTime, 'password' => $passwordHash,
                        'remember_token' => Str::random(10), 'company_id' => $company->id,
                        'user_type' => UserType::Employee->value,
                        'department_id' => $departmentIdsTeam1[array_rand($departmentIdsTeam1)],
                        'model_status' => ModelStatus::ACTIVE->value,
                        'phone_1' => '+41'.rand(700000000, 799999999),
                        'slug' => Str::slug($firstName.'-'.$lastName.'-'.$currentEmployeeIndex),
                        'created_by' => $owner->id, 'joined_at' => $joinedDate,
                        'created_at' => $currentTime, 'updated_at' => $currentTime,
                    ];

                    // Temporäre User ID für spätere Zuweisung (wird nach Insert ersetzt)
                    $tempUserId = 'user_'.$currentEmployeeIndex;

                    $employeesToInsert[$tempUserId] = [ // Verwende temp ID als Key
                        'profession_id' => $professionIdsTeam1[array_rand($professionIdsTeam1)],
                        'stage_id' => $stageIdsTeam1[array_rand($stageIdsTeam1)],
                        'personal_number' => 'PN'.str_pad($currentEmployeeIndex, 8, '0', STR_PAD_LEFT),
                        'supervisor_id' => null, // Supervisor später setzen
                        'employee_status' => $this->getRandomEmployeeStatus()->value,
                        'created_at' => $currentTime, 'updated_at' => $currentTime,
                    ];

                    $roleId = ($managersCreatedTeam1 < $managerCount) ? $managerRoleId : $nonManagerRoleIds[array_rand($nonManagerRoleIds)];
                    if($roleId == $managerRoleId) $managersCreatedTeam1++;

                    $roleAssignments[$tempUserId] = [ // Verwende temp ID als Key
                        'role_id' => $roleId,
                        'model_type' => User::class,
                    ];

                    $teamAssignments[$tempUserId] = [ // Verwende temp ID als Key
                        'team_id' => $team1->id,
                        'role' => 'editor', // Standardrolle im Team
                        'created_at' => $currentTime, 'updated_at' => $currentTime,
                    ];
                    $this->command->getOutput()->progressAdvance();
                }

                // Bulk Insert Users und IDs holen
                DB::table('users')->insert($usersToInsert);
                $insertedUserIds = DB::table('users')->whereIn('email', array_column($usersToInsert, 'email'))->pluck('id', 'email');

                // User IDs in Employee, Role und Team Zuweisungen ersetzen
                $finalEmployees = []; $finalRoles = []; $finalTeams = [];
                foreach ($usersToInsert as $idx => $userData) {
                    $tempKey = 'user_'.($i + $idx + 1);
                    $realUserId = $insertedUserIds[$userData['email']];

                    $employeesToInsert[$tempKey]['user_id'] = $realUserId;
                    $finalEmployees[] = $employeesToInsert[$tempKey];

                    $roleAssignments[$tempKey]['model_id'] = $realUserId;
                    $finalRoles[] = $roleAssignments[$tempKey];

                    $teamAssignments[$tempKey]['user_id'] = $realUserId;
                    $finalTeams[] = $teamAssignments[$tempKey];
                }

                // Bulk Inserts für den Rest
                if (!empty($finalEmployees)) DB::table('employees')->insert($finalEmployees);
                if (!empty($finalRoles)) DB::table('model_has_roles')->insert($finalRoles);
                if (!empty($finalTeams)) DB::table('team_user')->insert($finalTeams);

                DB::commit();
                unset($usersToInsert, $employeesToInsert, $roleAssignments, $teamAssignments, $insertedUserIds, $finalEmployees, $finalRoles, $finalTeams);
                if (function_exists('gc_collect_cycles')) gc_collect_cycles();
            }
            $this->command->getOutput()->progressFinish(); $this->command->newLine();


            // 10. Erstelle Mitarbeiter für Team 2 (Betrieb 55)
            $this->command->info('Erstelle '.$team2EmployeeCount.' Mitarbeiter für Betrieb 55...');
            $this->command->getOutput()->progressStart($team2EmployeeCount);
            $managersCreatedTeam2 = 0; // Reset für Team 2

            for ($i = 0; $i < $team2EmployeeCount; $i += $chunkSize) {
                DB::beginTransaction();
                $usersToInsert = []; $employeesToInsert = []; $roleAssignments = []; $teamAssignments = [];
                $currentTime = now();
                $limit = min($chunkSize, $team2EmployeeCount - $i);

                for ($j = 0; $j < $limit; $j++) {
                    $currentEmployeeIndex++; // Globalen Index weiterzählen
                    $firstName = $faker->firstName; $lastName = $faker->lastName;
                    $email = strtolower(Str::slug($firstName)).'.'.strtolower(Str::slug($lastName)).'.b55.'.$currentEmployeeIndex.'@firma.ch'; // Team-Kennung
                    $joinedDate = Carbon::now()->subDays(rand(0, 365 * 3));

                    $usersToInsert[] = [
                        'name' => $firstName, 'last_name' => $lastName, 'email' => $email,
                        'email_verified_at' => $currentTime, 'password' => $passwordHash,
                        'remember_token' => Str::random(10), 'company_id' => $company->id,
                        'user_type' => UserType::Employee->value,
                        'department_id' => $departmentIdsTeam2[array_rand($departmentIdsTeam2)],
                        'model_status' => ModelStatus::ACTIVE->value,
                        'phone_1' => '+41'.rand(700000000, 799999999),
                        'slug' => Str::slug($firstName.'-'.$lastName.'-b55-'.$currentEmployeeIndex), // Team-Kennung
                        'created_by' => $owner->id, 'joined_at' => $joinedDate,
                        'created_at' => $currentTime, 'updated_at' => $currentTime,
                    ];

                    $tempUserId = 'user_'.$currentEmployeeIndex;
                    $employeesToInsert[$tempUserId] = [
                        'profession_id' => $professionIdsTeam2[array_rand($professionIdsTeam2)],
                        'stage_id' => $stageIdsTeam2[array_rand($stageIdsTeam2)],
                        'personal_number' => 'B55-'.str_pad($currentEmployeeIndex, 8, '0', STR_PAD_LEFT), // Team-Kennung
                        'supervisor_id' => null,
                        'employee_status' => $this->getRandomEmployeeStatus()->value,
                        'created_at' => $currentTime, 'updated_at' => $currentTime,
                    ];

                    $roleId = ($managersCreatedTeam2 < $team2ManagerCount) ? $managerRoleId : $nonManagerRoleIds[array_rand($nonManagerRoleIds)];
                    if($roleId == $managerRoleId) $managersCreatedTeam2++;

                    $roleAssignments[$tempUserId] = [
                        'role_id' => $roleId, 'model_type' => User::class,
                    ];
                    $teamAssignments[$tempUserId] = [
                        'team_id' => $team2->id, 'role' => 'editor',
                        'created_at' => $currentTime, 'updated_at' => $currentTime,
                    ];
                    $this->command->getOutput()->progressAdvance();
                }

                // Bulk Insert Users und IDs holen
                DB::table('users')->insert($usersToInsert);
                $insertedUserIds = DB::table('users')->whereIn('email', array_column($usersToInsert, 'email'))->pluck('id', 'email');

                // IDs ersetzen
                $finalEmployees = []; $finalRoles = []; $finalTeams = [];
                foreach ($usersToInsert as $idx => $userData) {
                    $localIndex = $i + $j + 1; // Lokaler Index innerhalb des Chunks
                    $globalIndexForTempId = $employeeCount + $localIndex; // Globaler Index für temp ID Berechnung
                    $tempKey = 'user_'.$globalIndexForTempId;

                    // Korrigierter Index für den Zugriff auf die temporären Arrays
                    $accessIndex = $employeeCount + $i + $idx + 1; // Korrekter globaler Index
                    $tempKeyCorrect = 'user_'.$accessIndex; // Korrekter Temp-Key

                    $realUserId = $insertedUserIds[$userData['email']];


                    if (isset($employeesToInsert[$tempKeyCorrect])) {
                        $employeesToInsert[$tempKeyCorrect]['user_id'] = $realUserId;
                        $finalEmployees[] = $employeesToInsert[$tempKeyCorrect];
                    }

                    if (isset($roleAssignments[$tempKeyCorrect])) {
                        $roleAssignments[$tempKeyCorrect]['model_id'] = $realUserId;
                        $finalRoles[] = $roleAssignments[$tempKeyCorrect];
                    }

                    if (isset($teamAssignments[$tempKeyCorrect])) {
                        $teamAssignments[$tempKeyCorrect]['user_id'] = $realUserId;
                        $finalTeams[] = $teamAssignments[$tempKeyCorrect];
                    }
                }

                if (!empty($finalEmployees)) DB::table('employees')->insert($finalEmployees);
                if (!empty($finalRoles)) DB::table('model_has_roles')->insert($finalRoles);
                if (!empty($finalTeams)) DB::table('team_user')->insert($finalTeams);

                DB::commit();
                unset($usersToInsert, $employeesToInsert, $roleAssignments, $teamAssignments, $insertedUserIds, $finalEmployees, $finalRoles, $finalTeams);
                if (function_exists('gc_collect_cycles')) gc_collect_cycles();
            }
            $this->command->getOutput()->progressFinish(); $this->command->newLine();


            // *** NEU: Erstelle Mitarbeiter für Team 3 (Betrieb 56) ***
            $this->command->info('Erstelle '.$team3EmployeeCount.' Mitarbeiter für Betrieb 56...');
            $this->command->getOutput()->progressStart($team3EmployeeCount);
            $managersCreatedTeam3 = 0; // Reset für Team 3

            for ($i = 0; $i < $team3EmployeeCount; $i += $chunkSize) {
                DB::beginTransaction();
                $usersToInsert = []; $employeesToInsert = []; $roleAssignments = []; $teamAssignments = [];
                $currentTime = now();
                $limit = min($chunkSize, $team3EmployeeCount - $i);

                for ($j = 0; $j < $limit; $j++) {
                    $currentEmployeeIndex++; // Globalen Index weiterzählen
                    $firstName = $faker->firstName; $lastName = $faker->lastName;
                    $email = strtolower(Str::slug($firstName)).'.'.strtolower(Str::slug($lastName)).'.b56.'.$currentEmployeeIndex.'@firma.ch'; // Team-Kennung
                    $joinedDate = Carbon::now()->subDays(rand(0, 365 * 3));

                    $usersToInsert[] = [
                        'name' => $firstName, 'last_name' => $lastName, 'email' => $email,
                        'email_verified_at' => $currentTime, 'password' => $passwordHash,
                        'remember_token' => Str::random(10), 'company_id' => $company->id,
                        'user_type' => UserType::Employee->value,
                        'department_id' => $departmentIdsTeam3[array_rand($departmentIdsTeam3)],
                        'model_status' => ModelStatus::ACTIVE->value,
                        'phone_1' => '+41'.rand(700000000, 799999999),
                        'slug' => Str::slug($firstName.'-'.$lastName.'-b56-'.$currentEmployeeIndex), // Team-Kennung
                        'created_by' => $owner->id, 'joined_at' => $joinedDate,
                        'created_at' => $currentTime, 'updated_at' => $currentTime,
                    ];

                    $tempUserId = 'user_'.$currentEmployeeIndex;
                    $employeesToInsert[$tempUserId] = [
                        'profession_id' => $professionIdsTeam3[array_rand($professionIdsTeam3)],
                        'stage_id' => $stageIdsTeam3[array_rand($stageIdsTeam3)],
                        'personal_number' => 'B56-'.str_pad($currentEmployeeIndex, 8, '0', STR_PAD_LEFT), // Team-Kennung
                        'supervisor_id' => null,
                        'employee_status' => $this->getRandomEmployeeStatus()->value,
                        'created_at' => $currentTime, 'updated_at' => $currentTime,
                    ];

                    $roleId = ($managersCreatedTeam3 < $team3ManagerCount) ? $managerRoleId : $nonManagerRoleIds[array_rand($nonManagerRoleIds)];
                    if($roleId == $managerRoleId) $managersCreatedTeam3++;

                    $roleAssignments[$tempUserId] = [
                        'role_id' => $roleId, 'model_type' => User::class,
                    ];
                    $teamAssignments[$tempUserId] = [
                        'team_id' => $team3->id, 'role' => 'editor',
                        'created_at' => $currentTime, 'updated_at' => $currentTime,
                    ];
                    $this->command->getOutput()->progressAdvance();
                }

                // Bulk Insert Users und IDs holen
                DB::table('users')->insert($usersToInsert);
                $insertedUserIds = DB::table('users')->whereIn('email', array_column($usersToInsert, 'email'))->pluck('id', 'email');

                // IDs ersetzen
                $finalEmployees = []; $finalRoles = []; $finalTeams = [];
                foreach ($usersToInsert as $idx => $userData) {
                    // Korrekter globaler Index für den Temp-Key
                    $accessIndex = $employeeCount + $team2EmployeeCount + $i + $idx + 1;
                    $tempKeyCorrect = 'user_'.$accessIndex;

                    $realUserId = $insertedUserIds[$userData['email']];

                    if (isset($employeesToInsert[$tempKeyCorrect])) {
                        $employeesToInsert[$tempKeyCorrect]['user_id'] = $realUserId;
                        $finalEmployees[] = $employeesToInsert[$tempKeyCorrect];
                    }

                    if (isset($roleAssignments[$tempKeyCorrect])) {
                        $roleAssignments[$tempKeyCorrect]['model_id'] = $realUserId;
                        $finalRoles[] = $roleAssignments[$tempKeyCorrect];
                    }

                    if (isset($teamAssignments[$tempKeyCorrect])) {
                        $teamAssignments[$tempKeyCorrect]['user_id'] = $realUserId;
                        $finalTeams[] = $teamAssignments[$tempKeyCorrect];
                    }
                }

                if (!empty($finalEmployees)) DB::table('employees')->insert($finalEmployees);
                if (!empty($finalRoles)) DB::table('model_has_roles')->insert($finalRoles);
                if (!empty($finalTeams)) DB::table('team_user')->insert($finalTeams);

                DB::commit();
                unset($usersToInsert, $employeesToInsert, $roleAssignments, $teamAssignments, $insertedUserIds, $finalEmployees, $finalRoles, $finalTeams);
                if (function_exists('gc_collect_cycles')) gc_collect_cycles();
            }
            $this->command->getOutput()->progressFinish(); $this->command->newLine();


            // --- Abschluss ---
            $this->command->info('Setze Vorgesetzte...');
            // Weisen Sie zufällige Vorgesetzte für alle Teams zu (optional, kann Performance beeinträchtigen)
            // Dies sollte idealerweise nach der Erstellung aller Manager erfolgen.
            // Beispiel (vereinfacht - benötigt ggf. Anpassung für große Datenmengen):
            $allManagerIds = User::where('company_id', $company->id)
                ->whereHas('roles', fn($q) => $q->where('name', 'Manager'))
                ->pluck('id')->toArray();

            if (!empty($allManagerIds)) {
                Employee::whereNull('supervisor_id')
                    ->where('user_id', '!=', $owner->id) // Owner kann kein Supervisor sein
                    ->whereHas('user', fn($q) => $q->where('company_id', $company->id)) // Nur Mitarbeiter dieser Firma
                    ->chunkById(500, function ($employeesChunk) use ($allManagerIds) {
                        foreach ($employeesChunk as $employee) {
                            // Stelle sicher, dass der Mitarbeiter nicht sein eigener Vorgesetzter ist
                            $potentialSupervisors = array_diff($allManagerIds, [$employee->user_id]);
                            if (!empty($potentialSupervisors)) {
                                $employee->update(['supervisor_id' => $potentialSupervisors[array_rand($potentialSupervisors)]]);
                            }
                        }
                    });
                $this->command->info('Vorgesetzte zugewiesen.');
            } else {
                $this->command->info('Keine Manager gefunden, um Vorgesetzte zuzuweisen.');
            }


            $this->command->info('Testdaten wurden erfolgreich erstellt!');
            $this->command->info("Erstellt für Betrieb 48 (Team 1): $managersCreatedTeam1 Manager und " . ($employeeCount - $managersCreatedTeam1) . " andere Mitarbeiter");
            $this->command->info("Erstellt für Betrieb 55 (Team 2): $managersCreatedTeam2 Manager und " . ($team2EmployeeCount - $managersCreatedTeam2) . " andere Mitarbeiter");
            $this->command->info("Erstellt für Betrieb 56 (Team 3): $managersCreatedTeam3 Manager und " . ($team3EmployeeCount - $managersCreatedTeam3) . " andere Mitarbeiter"); // *** NEU ***

        } catch (\Exception $e) {
            // Transaktion rückgängig machen, wenn ein Fehler auftritt
            DB::rollBack(); // Stellen Sie sicher, dass Rollback aufgerufen wird

            $this->command->error('Fehler beim Erstellen der Testdaten: '.$e->getMessage());
            $this->command->error("In Datei: " . $e->getFile() . " Zeile: " . $e->getLine());
            $this->command->error($e->getTraceAsString());
            // Optional: Re-throw exception if needed for further handling
            // throw $e;
        } finally {
            // Model Events wieder aktivieren
            Model::reguard();
            DB::enableQueryLog(); // Query Log wieder aktivieren
        }
    }

    /**
     * Gibt einen zufälligen Mitarbeiterstatus zurück, gewichtet.
     * @return EmployeeStatus
     */
    protected function getRandomEmployeeStatus(): EmployeeStatus
    {
        // Alle verfügbaren Status
        $statuses = EmployeeStatus::cases();

        // Gewichtete Auswahl: EMPLOYED und PROBATION häufiger
        // Summe muss 1 ergeben (100%)
        $weights = [
            EmployeeStatus::ONBOARDING->value => 0.05, // 5%
            EmployeeStatus::PROBATION->value => 0.25, // 25%
            EmployeeStatus::EMPLOYED->value => 0.60, // 60%
            EmployeeStatus::ONLEAVE->value => 0.05, // 5%
            EmployeeStatus::LEAVE->value => 0.05, // 5%
        ];

        $rand = (float) mt_rand() / (float) mt_getrandmax(); // Zufallszahl zwischen 0 und 1
        $cumulativeWeight = 0;

        foreach ($statuses as $status) {
            if(isset($weights[$status->value])) {
                $cumulativeWeight += $weights[$status->value];
                if ($rand <= $cumulativeWeight) {
                    return $status;
                }
            }
        }

        // Fallback, sollte theoretisch nicht erreicht werden, wenn Gewichte 1 ergeben
        return EmployeeStatus::EMPLOYED;
    }
}
