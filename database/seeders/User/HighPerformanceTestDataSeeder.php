<?php

namespace Database\Seeders\User;

use App\Enums\Company\CompanyRegistrationType;
use App\Enums\Company\CompanySize;
use App\Enums\Company\CompanyType;
use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\User\UserType;
use App\Models\Alem\Company;
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

class HighPerformanceTestDataSeeder extends Seeder
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
            'name' => 'Daniel Skrbac',
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
            'employees' => 2500,  //  Standard
            'managers' => 100,
            'departments' => 100,
            'professions' => 100,
            'stages' => 100,
        ],

        // Team 2 (Betrieb 55) Konfiguration
        'team2' => [
            'name' => 'Betrieb 55',
            'employees' => 1500,  //  als Standard
            'managers' => 100,
            'departments' => 100,
            'professions' => 100,
            'stages' => 100,
        ],

        // Performance Konfiguration
        'performance' => [
            'chunk_size' => 7500,
            'memory_limit' => '4G',
            'use_raw_sql' => true,
            'disable_foreign_keys' => true,
            'disable_indexes' => true,
            'use_csv_import' => false,
            'parallel_workers' => 4,
            'user_insert_chunk' => 2000,  // Speziell für User-Tabelle
            'other_insert_chunk' => 30, // Für andere Tabellen
        ],

        // Sonstige Konfiguration
        'faker_locale' => 'de_DE',
        'delete_existing' => true,
    ];

    protected $faker;
    protected $passwordHash;
    protected $startTime;
    protected $preGeneratedData = [];

    protected $stageNames = [
        'Lehrling', 'Praktikant', 'Angelernt', 'Geselle',
        'Facharbeiter', 'Meister', 'Experte', 'Leiter',
        'Direktor', 'CEO', 'CTO', 'CFO', 'COO', 'CIO', 'CSO', 'CMO'
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->startTime = microtime(true);

        // Initialisierung
        $this->initialize();

        try {
            // Performance-Optimierungen aktivieren
            $this->enablePerformanceMode();

            DB::beginTransaction();

            // Alte Daten löschen (optional)
            if ($this->config['delete_existing']) {
                $this->deleteExistingData();
            }

            // Basis-Daten erstellen (Owner, Company, Teams, Roles)
            $baseData = $this->createBaseData();

            // Master-Daten vorab generieren für bessere Performance
            $this->preGenerateMasterData($baseData);

            // Mitarbeiter mit optimierter Methode erstellen
            $this->createEmployeesOptimized($baseData);

            DB::commit();

            // Performance-Modus deaktivieren
            $this->disablePerformanceMode();

            $this->outputSummary();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->disablePerformanceMode();

            if ($this->command) {
                $this->command->error('Fehler: ' . $e->getMessage());
            }
            throw $e;
        }
    }

    /**
     * Aktiviere Performance-Optimierungen
     */
    protected function enablePerformanceMode(): void
    {
        if ($this->command) {
            $this->command->info('🚀 Aktiviere Performance-Optimierungen...');
        }

        // Deaktiviere Foreign Key Checks
        if ($this->config['performance']['disable_foreign_keys']) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }

        // Setze optimale MySQL Einstellungen
        DB::statement('SET unique_checks=0');
        DB::statement('SET autocommit=0');

        // Erhöhe Bulk Insert Buffer
        try {
            DB::statement('SET SESSION bulk_insert_buffer_size = 1073741824'); // 1GB
            DB::statement('SET SESSION myisam_sort_buffer_size = 1073741824'); // 1GB
        } catch (\Exception $e) {
            // Ignoriere, falls keine Berechtigung
        }
    }

    /**
     * Deaktiviere Performance-Optimierungen
     */
    protected function disablePerformanceMode(): void
    {
        // Reaktiviere Foreign Key Checks
        if ($this->config['performance']['disable_foreign_keys']) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        DB::statement('SET unique_checks=1');
        DB::statement('SET autocommit=1');
    }

    /**
     * Initialisierung
     */
    protected function initialize(): void
    {
        $this->faker = Faker::create($this->config['faker_locale']);
        ini_set('memory_limit', $this->config['performance']['memory_limit']);
        Model::unguard();
        DB::disableQueryLog();
        $this->passwordHash = Hash::make($this->config['owner']['password']);

        if ($this->command) {
            $totalEmployees = $this->config['team1']['employees'] + $this->config['team2']['employees'];
            $this->command->info("🎯 Ziel: Erstelle {$this->formatNumber($totalEmployees)} Mitarbeiter");
            $this->command->info("⚙️  Chunk Size: {$this->config['performance']['chunk_size']}");
            $this->command->info("💾 Memory Limit: {$this->config['performance']['memory_limit']}");
            $this->command->newLine();
        }
    }

    /**
     * Erstelle Basis-Daten (Owner, Company, Teams, Roles)
     */
    protected function createBaseData(): array
    {
        // Erstelle Owner
        $owner = $this->createOwner();

        // Erstelle Company
        $company = $this->createCompany($owner);

        // Erstelle Teams
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

        // Erstelle Rollen
        $roles = $this->createRoles($owner, $company);

        return compact('owner', 'company', 'team1', 'team2', 'roles');
    }

    /**
     * Generiere Master-Daten vorab für bessere Performance
     */
    protected function preGenerateMasterData($baseData): void
    {
        if ($this->command) {
            $this->command->info('📊 Generiere Master-Daten...');
        }

        // Team 1 Master-Daten
        $this->preGeneratedData['team1'] = [
            'departments' => $this->createDepartmentsBulk(
                $baseData['team1'],
                $baseData['company'],
                $baseData['owner'],
                $this->config['team1']['departments']
            ),
            'professions' => $this->createProfessionsBulk(
                $baseData['team1'],
                $baseData['company'],
                $baseData['owner'],
                $this->config['team1']['professions']
            ),
            'stages' => $this->createStagesBulk(
                $baseData['team1'],
                $baseData['company'],
                $baseData['owner'],
                $this->config['team1']['stages']
            ),
        ];

        // Team 2 Master-Daten
        $this->preGeneratedData['team2'] = [
            'departments' => $this->createDepartmentsBulk(
                $baseData['team2'],
                $baseData['company'],
                $baseData['owner'],
                $this->config['team2']['departments'],
                'B55 '
            ),
            'professions' => $this->createProfessionsBulk(
                $baseData['team2'],
                $baseData['company'],
                $baseData['owner'],
                $this->config['team2']['professions'],
                'B55 '
            ),
            'stages' => $this->createStagesBulk(
                $baseData['team2'],
                $baseData['company'],
                $baseData['owner'],
                $this->config['team2']['stages'],
                'B55 '
            ),
        ];
    }

    /**
     * Erstelle Mitarbeiter mit optimierter Methode
     */
    protected function createEmployeesOptimized($baseData): void
    {
        // Team 1
        $this->createTeamEmployeesOptimized(
            $baseData['team1'],
            $baseData['company'],
            $baseData['owner'],
            $baseData['roles'],
            'team1',
            0
        );

        // Team 2
        $this->createTeamEmployeesOptimized(
            $baseData['team2'],
            $baseData['company'],
            $baseData['owner'],
            $baseData['roles'],
            'team2',
            $this->config['team1']['employees']
        );
    }

    /**
     * Erstelle Mitarbeiter für ein Team optimiert
     */
    protected function createTeamEmployeesOptimized($team, $company, $owner, $roles, $configKey, $indexOffset): void
    {
        $config = $this->config[$configKey];
        $employeeCount = $config['employees'];
        $managerCount = $config['managers'];
        $chunkSize = $this->config['performance']['chunk_size'];

        if ($this->command) {
            $this->command->info("👥 Erstelle {$this->formatNumber($employeeCount)} Mitarbeiter für {$team->name}...");
            $this->command->getOutput()->progressStart($employeeCount);
        }

        // Vorgenerierte Daten
        $departmentIds = $this->preGeneratedData[$configKey]['departments'];
        $professionIds = $this->preGeneratedData[$configKey]['professions'];
        $stageIds = $this->preGeneratedData[$configKey]['stages'];

        $nonManagerRoleIds = [$roles['Worker'], $roles['Editor'], $roles['Temporary']];
        $managersCreated = 0;

        // Erstelle Mitarbeiter in großen Chunks
        for ($i = 0; $i < $employeeCount; $i += $chunkSize) {
            $currentChunkSize = min($chunkSize, $employeeCount - $i);

            // Generiere Namen für diesen Chunk mit ausreichend Reserve
            $namesNeeded = max($currentChunkSize * 3, 1000);
            $firstNames = $this->preGenerateNames($namesNeeded, 'firstName');
            $lastNames = $this->preGenerateNames($namesNeeded, 'lastName');

            $this->createEmployeeChunkOptimized(
                $team,
                $company,
                $owner,
                $roles,
                $i,
                $currentChunkSize,
                $indexOffset,
                $departmentIds,
                $professionIds,
                $stageIds,
                $nonManagerRoleIds,
                $managersCreated,
                $managerCount,
                $configKey,
                $firstNames,
                $lastNames
            );

            // Garbage Collection alle 10 Chunks
            if (($i / $chunkSize) % 10 == 0) {
                gc_collect_cycles();
            }
        }

        if ($this->command) {
            $this->command->getOutput()->progressFinish();
        }
    }

    /**
     * Erstelle optimierten Employee Chunk
     */
    protected function createEmployeeChunkOptimized(
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
        $configKey,
        $firstNames,
        $lastNames
    ): void
    {
        $currentTime = now()->toDateTimeString();
        $companyId = $company->id;
        $teamId = $team->id;
        $ownerId = $owner->id;
        $managerRoleId = $roles['Manager'];

        // Vorbereitung der Batch-Daten
        $userData = [];
        $userEmails = [];
        $employeeData = [];
        $roleData = [];
        $teamUserData = [];

        // Cache häufig verwendete Werte
        $departmentCount = count($departmentIds);
        $professionCount = count($professionIds);
        $stageCount = count($stageIds);
        $nonManagerCount = count($nonManagerRoleIds);

        // Erstelle Supervisor-Pool für realistischere Hierarchie
        $supervisorIds = [$ownerId]; // Owner ist immer ein möglicher Supervisor
        // Speichere die Role ID für jeden User
        $userRoleIds = [];

        for ($j = 0; $j < $chunkSize; $j++) {
            $index = $startIndex + $j + $indexOffset + 1;

            // Verwende vorgenerierte Namen mit Sicherheitsprüfung
            $firstNameIndex = $j % count($firstNames);
            $lastNameIndex = $j % count($lastNames);

            // Sicherheitsprüfung für Array-Zugriff
            $firstName = isset($firstNames[$firstNameIndex]) ? $firstNames[$firstNameIndex] : $this->faker->firstName;
            $lastName = isset($lastNames[$lastNameIndex]) ? $lastNames[$lastNameIndex] : $this->faker->lastName;

            // Erstelle vollständigen Namen
            $fullName = $firstName . ' ' . $lastName;

            $suffix = $configKey === 'team2' ? 'b55' : 't1';

            // Generiere Email basierend auf Vor- und Nachnamen
            $emailName = strtolower($firstName . '.' . $lastName);
            $email = $emailName . $suffix . $index . '@firma.ch';
            $userEmails[] = $email;

            // Bestimme Supervisor
            // Erste 10% haben den Owner als Supervisor, Rest hat zufälligen Supervisor aus Pool
            $supervisorId = ($j < $chunkSize * 0.1) ? $ownerId : $supervisorIds[array_rand($supervisorIds)];

            // Bestimme welche Role dieser User bekommt
            if ($managersCreated < $managerCount) {
                $userRoleId = $managerRoleId;
                $isManager = true;  // Manager Role hat is_manager = true
                $managersCreated++;
            } else {
                // Zufällige Non-Manager Role
                $userRoleId = $nonManagerRoleIds[$j % $nonManagerCount];
                $isManager = false;  // Diese Rollen haben is_manager = false
            }

// Speichere die Role ID für späteren Gebrauch
            $userRoleIds[$j] = $userRoleId;

            // User-Daten MIT den neuen Feldern
            $userData[] = [
                'name' => $fullName,  // Vollständiger Name
                'email' => $email,
                'email_verified_at' => $currentTime,
                'password' => $this->passwordHash,
                'remember_token' => Str::random(10),
                'company_id' => $companyId,
                'user_type' => UserType::Employee->value,
                'department_id' => $departmentIds[$j % $departmentCount],
                'profession_id' => $professionIds[$j % $professionCount],
                'stage_id' => $stageIds[$j % $stageCount],
                'supervisor_id' => $supervisorId,
                'manager' => $isManager,  // NEU: Setze Manager Feld direkt
                'model_status' => ModelStatus::ACTIVE->value,
                'status' => $this->getRandomEmployeeStatusValue(),
                'phone_1' => '+417' . str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT),
                'url_slug' => Str::slug($fullName) . '-' . $index,
                'created_by' => $ownerId,
                'joined_at' => $this->generateRandomDate(),
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ];
        }

        // Bulk Insert Users und hole IDs
        $this->insertInChunks('users', $userData, 2000);

        // Hole die eingefügten User IDs basierend auf den Emails
        $userIds = DB::table('users')
            ->whereIn('email', $userEmails)
            ->pluck('id', 'email')
            ->toArray();

        // Erweitere den Supervisor-Pool mit den neu erstellten Manager-IDs
        $managerEmails = array_slice($userEmails, 0, min($managerCount - ($managersCreated - $chunkSize), $chunkSize));
        foreach ($managerEmails as $managerEmail) {
            if (isset($userIds[$managerEmail])) {
                $supervisorIds[] = $userIds[$managerEmail];
            }
        }

        // Erstelle Employee, Role und Team-Zuweisungen
        $j = 0;
        $managersInThisChunk = 0;
        foreach ($userEmails as $email) {
            if (!isset($userIds[$email])) continue;

            $userId = $userIds[$email];
            $index = $startIndex + $j + $indexOffset + 1;

            // Employee-Daten
            $prefix = $configKey === 'team2' ? 'B55-' : 'PN';
            $padLength = $configKey === 'team2' ? 5 : 8;

            $employeeData[] = [
                'user_id' => $userId,
                'personal_number' => $prefix . str_pad($index, $padLength, '0', STR_PAD_LEFT),
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ];

            // Rolle - verwende die vorher bestimmte Role ID
            $roleId = $userRoleIds[$j];

            $roleData[] = [
                'role_id' => $roleId,
                'model_type' => 'App\\Models\\User',
                'model_id' => $userId,
            ];

            // Team-Zuweisung
            $teamUserData[] = [
                'team_id' => $teamId,
                'user_id' => $userId,
                'role' => 'editor',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ];

            $j++;
        }

        // Bulk Inserts
        if (!empty($employeeData)) {
            $this->insertInChunks('employees', $employeeData, 5000);
        }

        if (!empty($roleData)) {
            $this->insertInChunks('model_has_roles', $roleData, 5000);
        }

        if (!empty($teamUserData)) {
            $this->insertInChunks('team_user', $teamUserData, 5000);
        }

        if ($this->command) {
            $this->command->getOutput()->progressAdvance($chunkSize);
        }

        // Speicher freigeben
        unset($userData, $userEmails, $employeeData, $roleData, $teamUserData, $userIds);
    }

    /**
     * Insert Daten in kleineren Chunks für bessere Performance
     */
    protected function insertInChunks($table, $data, $chunkSize = 5000): void
    {
        $chunks = array_chunk($data, $chunkSize);
        foreach ($chunks as $chunk) {
            DB::table($table)->insert($chunk);
        }
    }

    /**
     * Generiere Namen vorab
     */
    protected function preGenerateNames($count, $type): array
    {
        $names = [];
        for ($i = 0; $i < $count; $i++) {
            $names[] = $this->faker->$type;
        }
        // WICHTIG: array_values() verwenden, um die Array-Keys wieder fortlaufend zu machen
        return array_values(array_unique($names));
    }

    /**
     * Generiere zufälliges Datum (optimiert)
     */
    protected function generateRandomDate(): string
    {
        return Carbon::now()->subDays(mt_rand(0, 1095))->toDateTimeString();
    }

    /**
     * Hole zufälligen Employee Status Wert (optimiert)
     */
    protected function getRandomEmployeeStatusValue(): string
    {
        $random = mt_rand(1, 100);

        if ($random <= 10) return EmployeeStatus::ONBOARDING->value;
        if ($random <= 35) return EmployeeStatus::PROBATION->value;
        if ($random <= 90) return EmployeeStatus::EMPLOYED->value;
        if ($random <= 95) return EmployeeStatus::ONLEAVE->value;

        return EmployeeStatus::LEAVE->value;
    }

    /**
     * Erstelle Departments in Bulk
     */
    protected function createDepartmentsBulk($team, $company, $owner, $count, $prefix = ''): array
    {
        $departments = [];
        $currentTime = now()->toDateTimeString();

        for ($i = 1; $i <= $count; $i++) {
            $departments[] = [
                'name' => $prefix . 'Abteilung ' . $i,
                'description' => 'Beschreibung für ' . $prefix . 'Abteilung ' . $i,
                'company_id' => $company->id,
                'team_id' => $team->id,
                'created_by' => $owner->id,
                'model_status' => ModelStatus::ACTIVE->value,
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ];
        }

        DB::table('departments')->insert($departments);

        return DB::table('departments')
            ->where('team_id', $team->id)
            ->pluck('id')
            ->toArray();
    }

    /**
     * Erstelle Professions in Bulk
     */
    protected function createProfessionsBulk($team, $company, $owner, $count, $prefix = ''): array
    {
        $professions = [];
        $currentTime = now()->toDateTimeString();

        for ($i = 1; $i <= $count; $i++) {
            $professions[] = [
                'name' => $prefix . 'Beruf' . $i,
                'company_id' => $company->id,
                'team_id' => $team->id,
                'created_by' => $owner->id,
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ];
        }

        DB::table('professions')->insert($professions);

        return DB::table('professions')
            ->where('team_id', $team->id)
            ->pluck('id')
            ->toArray();
    }

    /**
     * Erstelle Stages in Bulk
     */
    protected function createStagesBulk($team, $company, $owner, $count, $prefix = ''): array
    {
        $stages = [];
        $currentTime = now()->toDateTimeString();
        $stagesToUse = array_slice($this->stageNames, 0, min($count, count($this->stageNames)));

        foreach ($stagesToUse as $stageName) {
            $stages[] = [
                'name' => $prefix . $stageName,
                'company_id' => $company->id,
                'team_id' => $team->id,
                'created_by' => $owner->id,
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ];
        }

        DB::table('stages')->insert($stages);

        return DB::table('stages')
            ->where('team_id', $team->id)
            ->pluck('id')
            ->toArray();
    }

    /**
     * Erstelle Owner
     */
    protected function createOwner(): User
    {
        $owner = User::create([
            'name' => $this->config['owner']['name'],
            'email' => $this->config['owner']['email'],
            'email_verified_at' => now(),
            'password' => $this->passwordHash,
            'remember_token' => Str::random(10),
            'user_type' => UserType::Owner,
            'model_status' => ModelStatus::ACTIVE,
            'url_slug' => Str::slug($this->config['owner']['name']) . '-' . Str::random(3),
            'manager' => true,  // Owner ist immer ein Manager
            'profession_id' => null,
            'stage_id' => null,
            'supervisor_id' => null,
        ]);

        $owner->assignRole('owner');
        return $owner;
    }

    /**
     * Erstelle Company
     */
    protected function createCompany(User $owner): Company
    {
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
     * Erstelle Rollen
     */
    protected function createRoles(User $owner, Company $company): array
    {
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
     * Lösche existierende Daten
     */
    protected function deleteExistingData(): void
    {
        if ($this->command) {
            $this->command->info('🗑️  Lösche existierende Daten...');
        }

        $ownerUser = User::where('email', $this->config['owner']['email'])->first();
        if (!$ownerUser) {
            return;
        }

        $companyId = $ownerUser->company_id;

        // Verwende Raw Queries für bessere Performance
        DB::table('employees')->whereIn('user_id', function ($query) use ($companyId) {
            $query->select('id')->from('users')->where('company_id', $companyId);
        })->delete();

        DB::table('users')->where('company_id', $companyId)->where('id', '!=', $ownerUser->id)->delete();
        DB::table('professions')->where('company_id', $companyId)->delete();
        DB::table('stages')->where('company_id', $companyId)->delete();
        DB::table('departments')->where('company_id', $companyId)->delete();
        DB::table('teams')->where('user_id', $ownerUser->id)->delete();
        DB::table('companies')->where('id', $companyId)->delete();

        $ownerUser->delete();
    }

    /**
     * Formatiere Zahlen für bessere Lesbarkeit
     */
    protected function formatNumber($number): string
    {
        return number_format($number, 0, ',', '.');
    }

    /**
     * Ausgabe der Zusammenfassung
     */
    protected function outputSummary(): void
    {
        if (!$this->command) {
            return;
        }

        $executionTime = round(microtime(true) - $this->startTime, 2);
        $totalEmployees = $this->config['team1']['employees'] + $this->config['team2']['employees'];
        $employeesPerSecond = round($totalEmployees / $executionTime);

        $this->command->newLine();
        $this->command->info('✅ Testdaten wurden erfolgreich erstellt!');
        $this->command->newLine();

        $this->command->table(
            ['Metrik', 'Wert'],
            [
                ['Gesamte Mitarbeiter', $this->formatNumber($totalEmployees)],
                ['Ausführungszeit', $executionTime . ' Sekunden'],
                ['Mitarbeiter pro Sekunde', $this->formatNumber($employeesPerSecond)],
                ['Memory Peak Usage', round(memory_get_peak_usage() / 1024 / 1024, 2) . ' MB'],
            ]
        );

        $this->command->newLine();

        $team1Config = $this->config['team1'];
        $team2Config = $this->config['team2'];

        $this->command->table(
            ['Team', 'Manager', 'Mitarbeiter', 'Abteilungen', 'Berufe', 'Stufen'],
            [
                [
                    $team1Config['name'],
                    $this->formatNumber($team1Config['managers']),
                    $this->formatNumber($team1Config['employees'] - $team1Config['managers']),
                    $team1Config['departments'],
                    $team1Config['professions'],
                    $team1Config['stages'],
                ],
                [
                    $team2Config['name'],
                    $this->formatNumber($team2Config['managers']),
                    $this->formatNumber($team2Config['employees'] - $team2Config['managers']),
                    $team2Config['departments'],
                    $team2Config['professions'],
                    $team2Config['stages'],
                ],
            ]
        );
    }
}
