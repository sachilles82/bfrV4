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
            $employeeCount = 100000; // Anzahl der zu erstellenden Mitarbeiter für Team 1 (Betrieb 48)
            $managerCount = 10;    // Anzahl der Manager für Team 1
            $chunkSize = 5000;      // Größere Chunks für bessere Performance

            // Parameter für Betrieb 55 (Team 2)
            $team2EmployeeCount = 6500; // Anzahl der Mitarbeiter für Team 2 (Betrieb 55)
            $team2ManagerCount = 4;   // Anzahl der Manager für Team 2
            $team2DepartmentCount = 4; // Anzahl der Abteilungen für Team 2
            $team2ProfessionCount = 10; // Anzahl der Berufe für Team 2
            $team2StageCount = 10;     // Anzahl der Stufen für Team 2

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

                User::where('company_id', $companyId)
                    ->where('id', '!=', $ownerUser->id)
                    ->delete();

                // Lösche vorhandene Berufe, Stufen und Abteilungen
                $this->command->info('Lösche vorhandene Berufe, Stufen und Abteilungen...');
                Profession::where('company_id', $companyId)->delete();
                Stage::where('company_id', $companyId)->delete();
                Department::where('company_id', $companyId)->delete();

                // Lösche den Owner und sein Unternehmen
                Team::where('user_id', $ownerUser->id)->delete();
                Company::where('id', $companyId)->delete();
                $ownerUser->delete();

                // Bestätige die Löschung
                DB::commit();
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
                    'slug' => 'daniel-'.Str::random(5), // Eindeutiger Slug mit Zufallszeichen
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
                'personal_team' => true,
            ]);

            // Team 2 - Betrieb 55
            $team2 = Team::create([
                'name' => 'Betrieb 55',
                'user_id' => $owner->id,
                'company_id' => $company->id,
                'personal_team' => false,
            ]);

            // Enum für Role Visibility importieren
            $visibleValue = \App\Enums\Role\RoleVisibility::Visible->value;

            // Enum für Role Access importieren
            $employeePanelValue = \App\Enums\Role\RoleHasAccessTo::EmployeePanel->value;

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
            ];

            // Rollen erstellen oder aktualisieren
            $roles = [];
            foreach ($roleData as $roleName => $data) {
                $role = Role::firstOrCreate(['name' => $roleName], $data);
                $roles[$roleName] = $role->id;
            }

            // 5. Weise die Rolle zu
            $this->command->info('Weise Rolle zu...');
            $owner->assignRole('owner');

            // Setze das Team als current_team_id für den Owner
            $owner->current_team_id = $team1->id;
            $owner->save();

            // Commit die Transaktion für Basis-Entitäten
            DB::commit();

            // 6. Erstelle Abteilungen für Team 1 (Betrieb 48)
            $this->command->info('Erstelle 100 Abteilungen für Betrieb 48...');
            $departmentChunks = array_chunk(range(1, 100), min(100, $chunkSize));
            $this->command->getOutput()->progressStart(100);

            foreach ($departmentChunks as $chunk) {
                DB::beginTransaction();
                $departments = [];

                foreach ($chunk as $i) {
                    $departments[] = [
                        'name' => 'Abteilung '.$i,
                        'description' => 'Beschreibung für Abteilung '.$i,
                        'company_id' => $company->id,
                        'team_id' => $team1->id,
                        'created_by' => $owner->id,
                        'model_status' => ModelStatus::ACTIVE->value,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $this->command->getOutput()->progressAdvance();
                }

                Department::insert($departments);
                DB::commit();
            }

            $this->command->getOutput()->progressFinish();
            $this->command->newLine();

            // Erstelle Abteilungen für Team 2 (Betrieb 55)
            $this->command->info('Erstelle '.$team2DepartmentCount.' Abteilungen für Betrieb 55...');
            $departmentsTeam2 = [];
            $this->command->getOutput()->progressStart($team2DepartmentCount);

            DB::beginTransaction();
            for ($i = 1; $i <= $team2DepartmentCount; $i++) {
                $departmentsTeam2[] = [
                    'name' => 'B55 Abteilung '.$i,
                    'description' => 'Beschreibung für B55 Abteilung '.$i,
                    'company_id' => $company->id,
                    'team_id' => $team2->id,
                    'created_by' => $owner->id,
                    'model_status' => ModelStatus::ACTIVE->value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $this->command->getOutput()->progressAdvance();
            }

            Department::insert($departmentsTeam2);
            DB::commit();

            $this->command->getOutput()->progressFinish();
            $this->command->newLine();

            // 7. Erstelle Berufe für Team 1 (Betrieb 48)
            $this->command->info('Erstelle 50 Berufe für Betrieb 48...');
            $professionChunks = array_chunk(range(1, 50), min(50, $chunkSize));
            $this->command->getOutput()->progressStart(50);

            foreach ($professionChunks as $chunk) {
                DB::beginTransaction();
                $professions = [];

                foreach ($chunk as $index) {
                    $professions[] = [
                        'name' => 'Beruf'.$index,
                        'company_id' => $company->id,
                        'team_id' => $team1->id,
                        'created_by' => $owner->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $this->command->getOutput()->progressAdvance();
                }
                // Ensure unique constraint for faker job titles if faker runs out of unique titles
                $uniqueProfessions = collect($professions)->unique('name')->toArray();
                Profession::insert($uniqueProfessions);
                DB::commit();
            }

            $this->command->getOutput()->progressFinish();
            $this->command->newLine();

            // Erstelle Berufe für Team 2 (Betrieb 55)
            $this->command->info('Erstelle '.$team2ProfessionCount.' Berufe für Betrieb 55...');
            $professionsTeam2 = [];
            $this->command->getOutput()->progressStart($team2ProfessionCount);

            DB::beginTransaction();
            for ($i = 1; $i <= $team2ProfessionCount; $i++) {
                $professionsTeam2[] = [
                    'name' => 'B55 Beruf'.$i,
                    'company_id' => $company->id,
                    'team_id' => $team2->id,
                    'created_by' => $owner->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $this->command->getOutput()->progressAdvance();
            }

            Profession::insert($professionsTeam2);
            DB::commit();

            $this->command->getOutput()->progressFinish();
            $this->command->newLine();

            // 8. Erstelle definierte Stufen für Team 1 (Betrieb 48)
            $stageNames = ['Lehrling', 'Praktikant', 'Angelernt', 'Geselle', 'Facharbeiter', 'Meister', 'Experte', 'Leiter', 'Direktor', 'CEO', 'CTO', 'CFO', 'COO', 'CIO', 'CSO', 'CMO'];
            $this->command->info('Erstelle '.count($stageNames).' definierte Stufen für Betrieb 48...');
            $this->command->getOutput()->progressStart(count($stageNames));

            DB::beginTransaction();
            $stages = [];
            foreach ($stageNames as $stageName) {
                $stages[] = [
                    'name' => $stageName,
                    'company_id' => $company->id,
                    'team_id' => $team1->id,
                    'created_by' => $owner->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $this->command->getOutput()->progressAdvance();
            }
            Stage::insert($stages);
            DB::commit();

            $this->command->getOutput()->progressFinish();
            $this->command->newLine();

            // Erstelle Stufen für Team 2 (Betrieb 55)
            $stageNamesTeam2 = array_slice($stageNames, 0, $team2StageCount);
            $this->command->info('Erstelle '.$team2StageCount.' Stufen für Betrieb 55...');
            $this->command->getOutput()->progressStart($team2StageCount);

            DB::beginTransaction();
            $stagesTeam2 = [];
            foreach ($stageNamesTeam2 as $stageName) {
                $stagesTeam2[] = [
                    'name' => 'B55 '.$stageName,
                    'company_id' => $company->id,
                    'team_id' => $team2->id,
                    'created_by' => $owner->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $this->command->getOutput()->progressAdvance();
            }
            Stage::insert($stagesTeam2);
            DB::commit();

            $this->command->getOutput()->progressFinish();
            $this->command->newLine();

            // Lade IDs für Beziehungen für Team 1 (Betrieb 48)
            $this->command->info('Lade IDs für Beziehungen...');
            $departmentIds = Department::where('company_id', $company->id)
                ->where('team_id', $team1->id)
                ->pluck('id')
                ->toArray();
            $professionIds = Profession::where('company_id', $company->id)
                ->where('team_id', $team1->id)
                ->pluck('id')
                ->toArray();
            $stageIds = Stage::where('company_id', $company->id)
                ->where('team_id', $team1->id)
                ->pluck('id')
                ->toArray();

            // Lade IDs für Beziehungen für Team 2 (Betrieb 55)
            $departmentIdsTeam2 = Department::where('company_id', $company->id)
                ->where('team_id', $team2->id)
                ->pluck('id')
                ->toArray();
            $professionIdsTeam2 = Profession::where('company_id', $company->id)
                ->where('team_id', $team2->id)
                ->pluck('id')
                ->toArray();
            $stageIdsTeam2 = Stage::where('company_id', $company->id)
                ->where('team_id', $team2->id)
                ->pluck('id')
                ->toArray();

            // Hole die Rollen-IDs
            $workerRoleId = $roles['Worker'];
            $managerRoleId = $roles['Manager'];
            $editorRoleId = $roles['Editor'];
            $temporaryRoleId = $roles['Temporary'];

            // Rolle-IDs für Nicht-Manager
            $nonManagerRoleIds = [$workerRoleId, $editorRoleId, $temporaryRoleId];

            // 9. Erstelle Mitarbeiter für Team 1 (Betrieb 48)
            $this->command->info('Erstelle '.$employeeCount.' Mitarbeiter für Betrieb 48 in Chunks von '.$chunkSize.'...');
            $this->command->getOutput()->progressStart($employeeCount);

            // Vorgenerierter Passwort-Hash für bessere Performance
            $passwordHash = Hash::make('password');
            $teamId = $team1->id;

            // Zähle die erstellten Manager
            $managersCreated = 0;

            // Mitarbeiter in Chunks erstellen
            for ($i = 0; $i < $employeeCount; $i += $chunkSize) {
                // Neue Transaktion für jeden Chunk
                DB::beginTransaction();

                $employees = [];
                $roleAssignments = [];
                $teamAssignments = [];

                $currentTime = now();

                for ($j = 0; $j < $chunkSize && ($i + $j) < $employeeCount; $j++) {
                    $index = $i + $j + 1;

                    // Ensure unique email
                    $email = strtolower(Str::slug($faker->firstName)).'.'.strtolower(Str::slug($faker->lastName)).'.'.$index.'@firma.ch';

                    // Zufälliges Eintrittsdatum in den letzten 3 Jahren
                    $joinedDate = Carbon::now()->subDays(rand(0, 365 * 3));

                    // Namen für den Benutzer
                    $firstName = $faker->firstName;
                    $lastName = $faker->lastName;

                    // Erstelle Benutzer-Daten direkt mit DB
                    $userId = DB::table('users')->insertGetId([
                        'name' => $firstName,
                        'last_name' => $lastName,
                        'email' => $email,
                        'email_verified_at' => $currentTime,
                        'password' => $passwordHash,
                        'remember_token' => Str::random(10),
                        'company_id' => $company->id,
                        'user_type' => UserType::Employee->value,
                        'department_id' => $departmentIds[array_rand($departmentIds)],
                        'model_status' => ModelStatus::ACTIVE->value,
                        'phone_1' => '+41'.rand(700000000, 799999999),
                        // Slug aus Vorname und Nachname mit Index für Eindeutigkeit
                        'slug' => Str::slug($firstName.'-'.$lastName.'-'.$index),
                        'created_by' => $owner->id,
                        'joined_at' => $joinedDate,
                        'created_at' => $currentTime,
                        'updated_at' => $currentTime,
                    ]);

                    // Erstelle Mitarbeiter-Daten
                    $randomStatus = $this->getRandomEmployeeStatus();
                    $employees[] = [
                        'user_id' => $userId,
                        'profession_id' => $professionIds[array_rand($professionIds)],
                        'stage_id' => $stageIds[array_rand($stageIds)],
                        'personal_number' => 'PN'.str_pad($index, 8, '0', STR_PAD_LEFT),
                        'supervisor_id' => $owner->id,
                        'employee_status' => $randomStatus->value,
                        'created_at' => $currentTime,
                        'updated_at' => $currentTime,
                    ];

                    // Rollenauswahl: Manager-Rolle nur für die ersten 100 Benutzer
                    if ($managersCreated < $managerCount) {
                        $roleId = $managerRoleId;
                        $managersCreated++;
                    } else {
                        // Für alle anderen: zufällige Nicht-Manager-Rolle
                        $roleId = $nonManagerRoleIds[array_rand($nonManagerRoleIds)];
                    }

                    $roleAssignments[] = [
                        'role_id' => $roleId,
                        'model_type' => 'App\\Models\\User',
                        'model_id' => $userId,
                    ];

                    // Team-Zuweisungen für Massen-Zuweisung
                    $teamAssignments[] = [
                        'team_id' => $teamId,
                        'user_id' => $userId,
                        'role' => 'editor',
                        'created_at' => $currentTime,
                        'updated_at' => $currentTime,
                    ];

                    $this->command->getOutput()->progressAdvance();
                }

                // Bulk-Insert für Mitarbeiter
                if (! empty($employees)) {
                    DB::table('employees')->insert($employees);
                }

                // Bulk-Insert für Rollen-Zuweisungen
                if (! empty($roleAssignments)) {
                    DB::table('model_has_roles')->insert($roleAssignments);
                }

                // Bulk-Insert für Team-Zuweisungen
                if (! empty($teamAssignments)) {
                    DB::table('team_user')->insert($teamAssignments);
                }

                // Commit den Chunk
                DB::commit();

                // Speicher freigeben
                unset($employees, $roleAssignments, $teamAssignments);

                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }
            }

            $this->command->getOutput()->progressFinish();
            $this->command->newLine();

            // 10. Erstelle Mitarbeiter für Team 2 (Betrieb 55)
            $this->command->info('Erstelle '.$team2EmployeeCount.' Mitarbeiter für Betrieb 55...');
            $this->command->getOutput()->progressStart($team2EmployeeCount);

            // Zähle die erstellten Manager für Team 2
            $managersCreatedTeam2 = 0;

            DB::beginTransaction();

            $employeesTeam2 = [];
            $roleAssignmentsTeam2 = [];
            $teamAssignmentsTeam2 = [];

            $currentTime = now();

            for ($i = 0; $i < $team2EmployeeCount; $i++) {
                $index = $employeeCount + $i + 1; // Fortlaufende Nummerierung nach Team 1

                // Eindeutige E-Mail
                $email = strtolower(Str::slug($faker->firstName)).'.'.strtolower(Str::slug($faker->lastName)).'.b55.'.$i.'@firma.ch';

                // Zufälliges Eintrittsdatum in den letzten 3 Jahren
                $joinedDate = Carbon::now()->subDays(rand(0, 365 * 3));

                // Namen für den Benutzer
                $firstName = $faker->firstName;
                $lastName = $faker->lastName;

                // Erstelle Benutzer-Daten
                $userId = DB::table('users')->insertGetId([
                    'name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'email_verified_at' => $currentTime,
                    'password' => $passwordHash,
                    'remember_token' => Str::random(10),
                    'company_id' => $company->id,
                    'user_type' => UserType::Employee->value,
                    'department_id' => $departmentIdsTeam2[array_rand($departmentIdsTeam2)],
                    'model_status' => ModelStatus::ACTIVE->value,
                    'phone_1' => '+41'.rand(700000000, 799999999),
                    'slug' => Str::slug($firstName.'-'.$lastName.'-b55-'.$i),
                    'created_by' => $owner->id,
                    'joined_at' => $joinedDate,
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime,
                ]);

                // Erstelle Mitarbeiter-Daten
                $randomStatus = $this->getRandomEmployeeStatus();
                $employeesTeam2[] = [
                    'user_id' => $userId,
                    'profession_id' => $professionIdsTeam2[array_rand($professionIdsTeam2)],
                    'stage_id' => $stageIdsTeam2[array_rand($stageIdsTeam2)],
                    'personal_number' => 'B55-'.str_pad($i, 5, '0', STR_PAD_LEFT),
                    'supervisor_id' => $owner->id,
                    'employee_status' => $randomStatus->value,
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime,
                ];

                // Rollenauswahl: Manager-Rolle nur für die ersten N Benutzer
                if ($managersCreatedTeam2 < $team2ManagerCount) {
                    $roleId = $managerRoleId;
                    $managersCreatedTeam2++;
                } else {
                    // Für alle anderen: zufällige Nicht-Manager-Rolle
                    $roleId = $nonManagerRoleIds[array_rand($nonManagerRoleIds)];
                }

                $roleAssignmentsTeam2[] = [
                    'role_id' => $roleId,
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $userId,
                ];

                // Team-Zuweisungen für Team 2
                $teamAssignmentsTeam2[] = [
                    'team_id' => $team2->id,
                    'user_id' => $userId,
                    'role' => 'editor',
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime,
                ];

                $this->command->getOutput()->progressAdvance();
            }

            // Bulk-Insert für Team 2
            if (! empty($employeesTeam2)) {
                DB::table('employees')->insert($employeesTeam2);
            }

            if (! empty($roleAssignmentsTeam2)) {
                DB::table('model_has_roles')->insert($roleAssignmentsTeam2);
            }

            if (! empty($teamAssignmentsTeam2)) {
                DB::table('team_user')->insert($teamAssignmentsTeam2);
            }

            DB::commit();

            $this->command->getOutput()->progressFinish();
            $this->command->info('Testdaten wurden erfolgreich erstellt!');
            $this->command->info("Erstellt für Betrieb 48: $managersCreated Manager und " . ($employeeCount - $managersCreated) . " andere Mitarbeiter");
            $this->command->info("Erstellt für Betrieb 55: $managersCreatedTeam2 Manager und " . ($team2EmployeeCount - $managersCreatedTeam2) . " andere Mitarbeiter");

        } catch (\Exception $e) {
            // Transaktion rückgängig machen, wenn ein Fehler auftritt
            DB::rollBack();

            $this->command->error('Fehler beim Erstellen der Testdaten: '.$e->getMessage());
            $this->command->error($e->getTraceAsString());
            throw $e;
        } finally {
            // Model Events wieder aktivieren
            Model::reguard();
        }
    }

    protected function getRandomEmployeeStatus()
    {
        // Alle verfügbaren Status
        $statuses = [
            EmployeeStatus::ONBOARDING,
            EmployeeStatus::PROBATION,
            EmployeeStatus::EMPLOYED,
            EmployeeStatus::ONLEAVE,
            EmployeeStatus::LEAVE,
        ];

        // Gewichtete Auswahl: EMPLOYED und PROBATION häufiger
        $weights = [0.10, 0.25, 0.55, 0.05, 0.05]; // 10%, 25%, 55%, 5%, 5%

        $randomNumber = mt_rand(1, 100) / 100;
        $cumulativeWeight = 0;

        foreach ($weights as $key => $weight) {
            $cumulativeWeight += $weight;
            if ($randomNumber <= $cumulativeWeight) {
                return $statuses[$key];
            }
        }

        // Fallback
        return EmployeeStatus::EMPLOYED;
    }
}
