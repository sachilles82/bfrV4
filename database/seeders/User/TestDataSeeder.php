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
use App\Models\Alem\Employee;
use App\Models\Alem\Industry;
use App\Models\Alem\QuickCrud\Profession;
use App\Models\Alem\QuickCrud\Stage;
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
     * Command Instanz für Output
     */
    protected $command;

    /**
     * Konfigurierbare Parameter mit Standardwerten
     */
    protected array $config = [
        // Owner Konfiguration
        'owner' => [
            'name' => 'Daniel Srbac',
            'email' => 'daniel@firma.ch',
            'password' => 'password',
        ],

        // Firmen Konfiguration
        'company' => [
            'name' => 'Dani AG',
            'email' => 'info@firma.ch',
            'phone' => '+41 44 401 11 42',
            'type' => CompanyType::AG,
            'size' => CompanySize::OneHundredOneToTwoHundred,
            'industry' => 'IT',
        ],

        // Team 1 (Betrieb 48) Konfiguration
        'team1' => [
            'name' => 'Betrieb 48',
            'employees' => 10,
            'managers' => 2,
            'departments' => 10,
            'professions' => 5,
            'stages' => 16,
        ],

        // Team 2 (Betrieb 55) Konfiguration
        'team2' => [
            'name' => 'Betrieb 55',
            'employees' => 60,
            'managers' => 2,
            'departments' => 4,
            'professions' => 4,
            'stages' => 4,
        ],

        // Performance Konfiguration
        'performance' => [
            'chunk_size' => 5,
            'memory_limit' => '2G',
        ],

        // Sonstige Konfiguration
        'faker_locale' => 'de_DE',
        'delete_existing' => true,
    ];

    protected $faker;
    protected $passwordHash;
    protected $stageNames = [
        'Lehrling', 'Praktikant', 'Angelernt', 'Geselle',
        'Facharbeiter', 'Meister', 'Experte', 'Leiter',
        'Direktor', 'CEO', 'CTO', 'CFO', 'COO', 'CIO', 'CSO', 'CMO'
    ];

    /**
     * Setze Command Instanz
     */
    public function setCommand($command): void
    {
        $this->command = $command;
    }

    /**
     * Setze Konfiguration
     */
    public function setConfiguration(array $config): void
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Falls kein Command gesetzt wurde, verwende den Standard Laravel Seeder Command
        if (!$this->command && property_exists($this, 'command')) {
            $this->command = app('Illuminate\Console\Command');
        }

        // Konfiguration aus Umgebungsvariablen oder Kommandozeilen-Optionen überschreiben
        $this->loadConfiguration();

        // Initialisierung
        $this->initialize();

        try {
            DB::beginTransaction();

            // Alte Daten löschen (optional)
            if ($this->config['delete_existing']) {
                $this->deleteExistingData();
            }

            // Hauptbenutzer (Owner) erstellen
            $owner = $this->createOwner();

            // Unternehmen erstellen
            $company = $this->createCompany($owner);

            // Teams erstellen
            $teams = $this->createTeams($owner, $company);

            // Rollen erstellen
            $roles = $this->createRoles($owner, $company);

            // Master-Daten für beide Teams erstellen
            $this->createMasterDataForTeam($teams['team1'], $company, $owner, 'team1');
            $this->createMasterDataForTeam($teams['team2'], $company, $owner, 'team2');

            // Mitarbeiter erstellen
            $this->createEmployeesForTeam($teams['team1'], $company, $owner, $roles, 'team1');
            $this->createEmployeesForTeam($teams['team2'], $company, $owner, $roles, 'team2');

            DB::commit();

            $this->outputSummary();

        } catch (\Exception $e) {
            DB::rollBack();
            if ($this->command) {
                $this->command->error('Fehler beim Erstellen der Testdaten: '.$e->getMessage());
                $this->command->error($e->getTraceAsString());
            }
            throw $e;
        } finally {
            Model::reguard();
        }
    }

    /**
     * Lade Konfiguration aus verschiedenen Quellen
     */
    protected function loadConfiguration(): void
    {
        // Aus config/seeder.php laden (falls vorhanden)
        if (config('seeder.test_data')) {
            $this->config = array_merge($this->config, config('seeder.test_data'));
        }

        // Aus Umgebungsvariablen laden
        $this->loadFromEnvironment();

        // Aus Kommandozeilen-Optionen laden (für Artisan Commands)
        $this->loadFromCommandOptions();
    }

    /**
     * Lade Konfiguration aus Umgebungsvariablen
     */
    protected function loadFromEnvironment(): void
    {
        // Beispiel: SEEDER_TEAM1_EMPLOYEES=100
        if ($employees = env('SEEDER_TEAM1_EMPLOYEES')) {
            $this->config['team1']['employees'] = (int) $employees;
        }

        if ($employees = env('SEEDER_TEAM2_EMPLOYEES')) {
            $this->config['team2']['employees'] = (int) $employees;
        }

        if ($chunkSize = env('SEEDER_CHUNK_SIZE')) {
            $this->config['performance']['chunk_size'] = (int) $chunkSize;
        }
    }

    /**
     * Lade Konfiguration aus Kommandozeilen-Optionen
     */
    protected function loadFromCommandOptions(): void
    {
        // Nur versuchen, Optionen zu laden, wenn wir über unseren Custom Command aufgerufen werden
        if (!$this->command || !method_exists($this->command, 'option')) {
            return;
        }

        // Prüfe, ob es unser Custom Command ist
        try {
            // Versuche eine Option zu lesen, die nur in unserem Command existiert
            if (!$this->command->hasOption('team1-employees')) {
                return;
            }

            // Lade alle Optionen
            if ($employees = $this->command->option('team1-employees')) {
                $this->config['team1']['employees'] = (int) $employees;
            }

            if ($employees = $this->command->option('team2-employees')) {
                $this->config['team2']['employees'] = (int) $employees;
            }

            if ($chunkSize = $this->command->option('chunk-size')) {
                $this->config['performance']['chunk_size'] = (int) $chunkSize;
            }

            // Weitere Optionen laden...
            if ($this->command->hasOption('owner-name') && $name = $this->command->option('owner-name')) {
                $this->config['owner']['name'] = $name;
            }

            if ($this->command->hasOption('owner-email') && $email = $this->command->option('owner-email')) {
                $this->config['owner']['email'] = $email;
            }

            if ($this->command->hasOption('company-name') && $company = $this->command->option('company-name')) {
                $this->config['company']['name'] = $company;
            }

            if ($this->command->hasOption('no-delete')) {
                $this->config['delete_existing'] = !$this->command->option('no-delete');
            }
        } catch (\Exception $e) {
            // Ignoriere Fehler beim Optionen-Laden
            return;
        }
    }

    /**
     * Initialisiere Seeder
     */
    protected function initialize(): void
    {
        $this->faker = Faker::create($this->config['faker_locale']);
        ini_set('memory_limit', $this->config['performance']['memory_limit']);
        Model::unguard();
        DB::disableQueryLog();
        $this->passwordHash = Hash::make($this->config['owner']['password']);

        // Nur Output zeigen, wenn Command verfügbar ist
        if ($this->command) {
            $this->command->info('Starte Erstellung der Testdaten mit folgender Konfiguration:');
            $this->command->table(
                ['Parameter', 'Wert'],
                [
                    ['Team 1 Mitarbeiter', $this->config['team1']['employees']],
                    ['Team 1 Manager', $this->config['team1']['managers']],
                    ['Team 2 Mitarbeiter', $this->config['team2']['employees']],
                    ['Team 2 Manager', $this->config['team2']['managers']],
                    ['Chunk Size', $this->config['performance']['chunk_size']],
                ]
            );
        }
    }

    /**
     * Lösche existierende Testdaten
     */
    protected function deleteExistingData(): void
    {
        if ($this->command) {
            $this->command->info('Entferne alte Testdaten...');
        }

        $ownerUser = User::where('email', $this->config['owner']['email'])->first();
        if (!$ownerUser) {
            return;
        }

        $companyId = $ownerUser->company_id;

        // Lösche abhängige Datensätze
        Employee::whereHas('user', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })->delete();

        User::where('company_id', $companyId)
            ->where('id', '!=', $ownerUser->id)
            ->delete();

        Profession::where('company_id', $companyId)->delete();
        Stage::where('company_id', $companyId)->delete();
        Department::where('company_id', $companyId)->delete();
        Team::where('user_id', $ownerUser->id)->delete();
        Company::where('id', $companyId)->delete();
        $ownerUser->delete();
    }

    /**
     * Erstelle Owner
     */
    protected function createOwner(): User
    {
        if ($this->command) {
            $this->command->info('Erstelle Owner-Benutzer...');
        }

        $owner = User::create([
            'name' => $this->config['owner']['name'],
            'email' => $this->config['owner']['email'],
            'email_verified_at' => now(),
            'password' => $this->passwordHash,
            'remember_token' => Str::random(10),
            'user_type' => UserType::Owner,
            'model_status' => ModelStatus::ACTIVE,
            'url_slug' => Str::slug($this->config['owner']['name']) . '-' . Str::random(3),
            ]);

        $owner->assignRole('owner');

        return $owner;
    }

    /**
     * Erstelle Unternehmen
     */
    protected function createCompany(User $owner): Company
    {
        if ($this->command) {
            $this->command->info('Erstelle Unternehmen...');
        }

        $industry = Industry::firstOrCreate(['name' => $this->config['company']['industry']]);

        $company = Company::create([
            'company_name' => $this->config['company']['name'],
            'email' => $this->config['company']['email'],
            'phone_1' => $this->config['company']['phone'],
            'is_active' => true,
            'owner_id' => $owner->id,
            'created_by' => $owner->id,
            'company_type' => $this->config['company']['type'],
            'company_size' => $this->config['company']['size'],
            'registration_type' => CompanyRegistrationType::SELF_REGISTERED,
            'industry_id' => $industry->id,
        ]);

        $owner->update(['company_id' => $company->id]);

        return $company;
    }

    /**
     * Erstelle Teams
     */
    protected function createTeams(User $owner, Company $company): array
    {
        if ($this->command) {
            $this->command->info('Erstelle Teams...');
        }

        $team1 = Team::create([
            'name' => $this->config['team1']['name'],
            'user_id' => $owner->id,
            'company_id' => $company->id,
            'personal_team' => true,
        ]);

        $team2 = Team::create([
            'name' => $this->config['team2']['name'],
            'user_id' => $owner->id,
            'company_id' => $company->id,
            'personal_team' => false,
        ]);

        $owner->update(['current_team_id' => $team1->id]);

        return compact('team1', 'team2');
    }

    /**
     * Erstelle Rollen
     */
    protected function createRoles(User $owner, Company $company): array
    {
        if ($this->command) {
            $this->command->info('Erstelle Rollen...');
        }

        $visibleValue = \App\Enums\Role\RoleVisibility::Visible->value;
        $employeePanelValue = \App\Enums\Role\RoleHasAccessTo::EmployeePanel->value;

        $roleData = [
            'Worker' => ['is_manager' => false],
            'Manager' => ['is_manager' => true],
            'Editor' => ['is_manager' => false],
            'Temporary' => ['is_manager' => false],
        ];

        $roles = [];
        foreach ($roleData as $roleName => $data) {
            $role = Role::firstOrCreate(
                ['name' => $roleName],
                array_merge($data, [
                    'guard_name' => 'web',
                    'created_by' => $owner->id,
                    'company_id' => $company->id,
                    'access' => $employeePanelValue,
                    'visible' => $visibleValue,
                ])
            );
            $roles[$roleName] = $role->id;
        }

        return $roles;
    }

    /**
     * Erstelle Master-Daten für ein Team
     */
    protected function createMasterDataForTeam($team, $company, $owner, $configKey): array
    {
        $config = $this->config[$configKey];
        $prefix = $configKey === 'team2' ? 'B55 ' : '';

        // Abteilungen erstellen
        $departments = $this->createDepartments($team, $company, $owner, $config['departments'], $prefix);

        // Berufe erstellen
        $professions = $this->createProfessions($team, $company, $owner, $config['professions'], $prefix);

        // Stufen erstellen
        $stages = $this->createStages($team, $company, $owner, $config['stages'], $prefix);

        return compact('departments', 'professions', 'stages');
    }

    /**
     * Erstelle Abteilungen
     */
    protected function createDepartments($team, $company, $owner, $count, $prefix = ''): array
    {
        if ($this->command) {
            $this->command->info("Erstelle $count Abteilungen für {$team->name}...");
            $this->command->getOutput()->progressStart($count);
        }

        $departments = [];
        for ($i = 1; $i <= $count; $i++) {
            $departments[] = [
                'name' => $prefix . 'Abteilung ' . $i,
                'description' => 'Beschreibung für ' . $prefix . 'Abteilung ' . $i,
                'company_id' => $company->id,
                'team_id' => $team->id,
                'created_by' => $owner->id,
                'model_status' => ModelStatus::ACTIVE->value,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if ($this->command) {
                $this->command->getOutput()->progressAdvance();
            }
        }

        Department::insert($departments);

        if ($this->command) {
            $this->command->getOutput()->progressFinish();
        }

        return Department::where('team_id', $team->id)->pluck('id')->toArray();
    }

    /**
     * Erstelle Berufe
     */
    protected function createProfessions($team, $company, $owner, $count, $prefix = ''): array
    {
        if ($this->command) {
            $this->command->info("Erstelle $count Berufe für {$team->name}...");
            $this->command->getOutput()->progressStart($count);
        }

        $professions = [];
        for ($i = 1; $i <= $count; $i++) {
            $professions[] = [
                'name' => $prefix . 'Beruf' . $i,
                'company_id' => $company->id,
                'team_id' => $team->id,
                'created_by' => $owner->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if ($this->command) {
                $this->command->getOutput()->progressAdvance();
            }
        }

        Profession::insert($professions);

        if ($this->command) {
            $this->command->getOutput()->progressFinish();
        }

        return Profession::where('team_id', $team->id)->pluck('id')->toArray();
    }

    /**
     * Erstelle Stufen
     */
    protected function createStages($team, $company, $owner, $count, $prefix = ''): array
    {
        if ($this->command) {
            $this->command->info("Erstelle $count Stufen für {$team->name}...");
            $this->command->getOutput()->progressStart($count);
        }

        $stagesToUse = array_slice($this->stageNames, 0, min($count, count($this->stageNames)));
        $stages = [];

        foreach ($stagesToUse as $stageName) {
            $stages[] = [
                'name' => $prefix . $stageName,
                'company_id' => $company->id,
                'team_id' => $team->id,
                'created_by' => $owner->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if ($this->command) {
                $this->command->getOutput()->progressAdvance();
            }
        }

        Stage::insert($stages);

        if ($this->command) {
            $this->command->getOutput()->progressFinish();
        }

        return Stage::where('team_id', $team->id)->pluck('id')->toArray();
    }

    /**
     * Erstelle Mitarbeiter für ein Team
     */
    protected function createEmployeesForTeam($team, $company, $owner, $roles, $configKey): void
    {
        $config = $this->config[$configKey];
        $employeeCount = $config['employees'];
        $managerCount = $config['managers'];
        $chunkSize = $this->config['performance']['chunk_size'];

        if ($this->command) {
            $this->command->info("Erstelle $employeeCount Mitarbeiter für {$team->name}...");
            $this->command->getOutput()->progressStart($employeeCount);
        }

        // Lade IDs für Beziehungen
        $departmentIds = Department::where('team_id', $team->id)->pluck('id')->toArray();
        $professionIds = Profession::where('team_id', $team->id)->pluck('id')->toArray();
        $stageIds = Stage::where('team_id', $team->id)->pluck('id')->toArray();

        $nonManagerRoleIds = [$roles['Worker'], $roles['Editor'], $roles['Temporary']];
        $managersCreated = 0;
        $employeeIndexOffset = $configKey === 'team2' ? $this->config['team1']['employees'] : 0;

        // Erstelle Mitarbeiter in Chunks
        for ($i = 0; $i < $employeeCount; $i += $chunkSize) {
            $chunkEmployees = min($chunkSize, $employeeCount - $i);
            $this->createEmployeeChunk(
                $team,
                $company,
                $owner,
                $roles,
                $i,
                $chunkEmployees,
                $employeeIndexOffset,
                $departmentIds,
                $professionIds,
                $stageIds,
                $nonManagerRoleIds,
                $managersCreated,
                $managerCount,
                $configKey
            );
        }

        if ($this->command) {
            $this->command->getOutput()->progressFinish();
        }
    }

    /**
     * Erstelle einen Chunk von Mitarbeitern
     */
    protected function createEmployeeChunk(
        $team,
        $company,
        $owner,
        $roles,
        $startIndex,
        $chunkSize,
        $indexOffset,
        $departmentIds,
        $professionIds,
        $stageIds,
        $nonManagerRoleIds,
        &$managersCreated,
        $managerCount,
        $configKey
    ): void {
        DB::beginTransaction();

        $employees = [];
        $roleAssignments = [];
        $teamAssignments = [];
        $currentTime = now();

        for ($j = 0; $j < $chunkSize; $j++) {
            $index = $startIndex + $j + $indexOffset + 1;

            // Generiere eindeutige E-Mail
            $email = $this->generateUniqueEmail($index, $configKey);

            // Erstelle Benutzer
            $userId = $this->createUser(
                $email,
                $company->id,
                $departmentIds[array_rand($departmentIds)],
                $owner->id,
                $index,
                $configKey
            );

            // Erstelle Mitarbeiter
            $employees[] = $this->createEmployeeData(
                $userId,
                $professionIds[array_rand($professionIds)],
                $stageIds[array_rand($stageIds)],
                $owner->id,
                $index,
                $configKey
            );

            // Weise Rolle zu
            $roleId = $this->assignRole($managersCreated, $managerCount, $roles['Manager'], $nonManagerRoleIds);
            $roleAssignments[] = [
                'role_id' => $roleId,
                'model_type' => 'App\\Models\\User',
                'model_id' => $userId,
            ];

            // Team-Zuweisung
            $teamAssignments[] = [
                'team_id' => $team->id,
                'user_id' => $userId,
                'role' => 'editor',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ];

            if ($this->command) {
                $this->command->getOutput()->progressAdvance();
            }
        }

        // Bulk-Insert
        DB::table('employees')->insert($employees);
        DB::table('model_has_roles')->insert($roleAssignments);
        DB::table('team_user')->insert($teamAssignments);

        DB::commit();

        // Speicher freigeben
        unset($employees, $roleAssignments, $teamAssignments);
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }

    /**
     * Generiere eindeutige E-Mail
     */
    protected function generateUniqueEmail($index, $configKey): string
    {
        $firstName = $this->faker->firstName;
        $suffix = $configKey === 'team2' ? '.b55' : '';

        return strtolower(
            Str::slug($firstName).
            $suffix . '.' .
            $index . '@firma.ch'
        );
    }

    /**
     * Erstelle Benutzer
     */
    protected function createUser($email, $companyId, $departmentId, $createdBy, $index, $configKey): int
    {
        $firstName = $this->faker->firstName;
        $suffix = $configKey === 'team2' ? '-b55' : '';

        return DB::table('users')->insertGetId([
            'name' => $firstName,
            'email' => $email,
            'email_verified_at' => now(),
            'password' => $this->passwordHash,
            'remember_token' => Str::random(10),
            'company_id' => $companyId,
            'user_type' => UserType::Employee->value,
            'department_id' => $departmentId,
            'model_status' => ModelStatus::ACTIVE->value,
            'phone_1' => $this->generatePhoneNumber(),
            'url_slug' => Str::slug($firstName . $suffix . '-' . $index),
            'created_by' => $createdBy,
            'joined_at' => Carbon::now()->subDays(rand(0, 365 * 3)),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Erstelle Mitarbeiter-Daten
     */
    protected function createEmployeeData($userId, $professionId, $stageId, $supervisorId, $index, $configKey): array
    {
        $prefix = $configKey === 'team2' ? 'B55-' : 'PN';
        $padLength = $configKey === 'team2' ? 5 : 8;

        return [
            'user_id' => $userId,
            'profession_id' => $professionId,
            'stage_id' => $stageId,
            'personal_number' => $prefix . str_pad($index, $padLength, '0', STR_PAD_LEFT),
            'supervisor_id' => $supervisorId,
            'status' => $this->getRandomEmployeeStatus()->value,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Generiere Telefonnummer
     */
    protected function generatePhoneNumber(): string
    {
        return '+41' . rand(700000000, 799999999);
    }

    /**
     * Weise Rolle zu
     */
    protected function assignRole(&$managersCreated, $managerCount, $managerRoleId, $nonManagerRoleIds): int
    {
        if ($managersCreated < $managerCount) {
            $managersCreated++;
            return $managerRoleId;
        }

        return $nonManagerRoleIds[array_rand($nonManagerRoleIds)];
    }

    /**
     * Zufälliger Mitarbeiterstatus mit Gewichtung
     */
    protected function getRandomEmployeeStatus(): EmployeeStatus
    {
        $statuses = [
            EmployeeStatus::ONBOARDING,
            EmployeeStatus::PROBATION,
            EmployeeStatus::EMPLOYED,
            EmployeeStatus::ONLEAVE,
            EmployeeStatus::LEAVE,
        ];

        $weights = [0.10, 0.25, 0.55, 0.05, 0.05];
        $randomNumber = mt_rand(1, 100) / 100;
        $cumulativeWeight = 0;

        foreach ($weights as $key => $weight) {
            $cumulativeWeight += $weight;
            if ($randomNumber <= $cumulativeWeight) {
                return $statuses[$key];
            }
        }

        return EmployeeStatus::EMPLOYED;
    }

    /**
     * Ausgabe der Zusammenfassung
     */
    protected function outputSummary(): void
    {
        if (!$this->command) {
            return;
        }

        $this->command->newLine();
        $this->command->info('✅ Testdaten wurden erfolgreich erstellt!');
        $this->command->newLine();

        $team1Config = $this->config['team1'];
        $team2Config = $this->config['team2'];

        $this->command->table(
            ['Team', 'Manager', 'Mitarbeiter', 'Abteilungen', 'Berufe', 'Stufen'],
            [
                [
                    $team1Config['name'],
                    $team1Config['managers'],
                    $team1Config['employees'] - $team1Config['managers'],
                    $team1Config['departments'],
                    $team1Config['professions'],
                    $team1Config['stages'],
                ],
                [
                    $team2Config['name'],
                    $team2Config['managers'],
                    $team2Config['employees'] - $team2Config['managers'],
                    $team2Config['departments'],
                    $team2Config['professions'],
                    $team2Config['stages'],
                ],
            ]
        );
    }
}
