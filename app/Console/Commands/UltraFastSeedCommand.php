<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Jobs\SeedEmployeesJob;
use App\Models\User;
use App\Models\Alem\Company;
use App\Models\Alem\Department;
use App\Models\Alem\Employee;
use App\Models\Alem\Industry;
use App\Models\Alem\QuickCrud\Profession;
use App\Models\Alem\QuickCrud\Stage;
use App\Models\Team;
use Spatie\Permission\Models\Role;
use App\Enums\Company\CompanyRegistrationType;
use App\Enums\Company\CompanySize;
use App\Enums\Company\CompanyType;
use App\Enums\Model\ModelStatus;
use App\Enums\User\UserType;
use App\Enums\Employee\EmployeeStatus;
use Faker\Factory as Faker;
use Carbon\Carbon;

class UltraFastSeedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:ultra-fast
                            {--employees=1000000 : Gesamtzahl der Mitarbeiter}
                            {--team-split=20:80 : Prozentuale Aufteilung zwischen Team 1 und Team 2}
                            {--method=bulk : Methode: bulk, csv, queue, parallel}
                            {--chunk=50000 : Chunk-Größe}
                            {--workers=4 : Anzahl paralleler Worker (für queue/parallel)}
                            {--truncate : Tabellen vor dem Seeding leeren}
                            {--clean : Lösche alle existierenden Mitarbeiter (behält Owner)}
                            {--owner-email=daniel@firma.ch : E-Mail des Owners}
                            {--no-foreign-keys : Foreign Key Checks deaktivieren}
                            {--no-indexes : Indexes temporär deaktivieren}
                            {--benchmark : Zeige detaillierte Performance-Metriken}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ultra-schnelles Seeding für Millionen von Datensätzen';

    protected $startTime;
    protected $benchmarks = [];
    protected $faker;
    protected $passwordHash;
    protected $preGeneratedData = [];

    protected $stageNames = [
        'Lehrling', 'Praktikant', 'Angelernt', 'Geselle',
        'Facharbeiter', 'Meister', 'Experte', 'Leiter',
        'Direktor', 'CEO', 'CTO', 'CFO', 'COO', 'CIO', 'CSO', 'CMO'
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->startTime = microtime(true);
        $this->faker = Faker::create('de_DE');
        $this->passwordHash = Hash::make('password');

        // Parameter
        $totalEmployees = (int) $this->option('employees');
        $method = $this->option('method');
        $chunkSize = (int) $this->option('chunk');
        $workers = (int) $this->option('workers');

        // Team-Aufteilung berechnen
        $teamSplit = explode(':', $this->option('team-split'));
        $team1Percentage = (int) $teamSplit[0];
        $team2Percentage = (int) $teamSplit[1];

        $team1Employees = (int) ($totalEmployees * $team1Percentage / 100);
        $team2Employees = $totalEmployees - $team1Employees;

        $this->info("🚀 Ultra Fast Seeding");
        $this->info("📊 Ziel: {$this->formatNumber($totalEmployees)} Mitarbeiter");
        $this->info("📈 Team 1: {$this->formatNumber($team1Employees)} ({$team1Percentage}%)");
        $this->info("📈 Team 2: {$this->formatNumber($team2Employees)} ({$team2Percentage}%)");
        $this->info("⚙️  Methode: {$method}");
        $this->info("📦 Chunk Size: {$this->formatNumber($chunkSize)}");

        if (in_array($method, ['queue', 'parallel'])) {
            $this->info("👥 Worker: {$workers}");
        }

        $this->newLine();

        // Bestätigung
        if ($totalEmployees > 100000) {
            if (!$this->confirm("Dies wird {$this->formatNumber($totalEmployees)} Datensätze erstellen. Fortfahren?")) {
                return 0;
            }
        }

        try {
            // Vorbereitung
            $this->prepareDatabase();

            // Konfiguration für Teams MUSS VOR createBaseData() definiert werden
            $config = [
                'owner' => [
                    'name' => 'Daniel',
                    'last_name' => 'Skrbac',
                    'email' => $this->option('owner-email'),
                    'password' => 'password',
                ],
                'company' => [
                    'name' => 'Dani AG',
                    'email' => 'info@firma.ch',
                    'phone' => '+41 44 401 11 42',
                    'type' => CompanyType::AG,
                    'size' => CompanySize::OneHundredOneToTwoHundred,
                    'industry' => 'IT',
                ],
                'team1' => [
                    'name' => 'Betrieb 48',
                    'employees' => $team1Employees,
                    'managers' => max(10, (int) ($team1Employees * 0.02)), // 2% Manager
                    'departments' => min(100, max(10, (int) ($team1Employees / 1000))),
                    'professions' => min(50, max(5, (int) ($team1Employees / 5000))),
                    'stages' => 16,
                ],
                'team2' => [
                    'name' => 'Betrieb 55',
                    'employees' => $team2Employees,
                    'managers' => max(10, (int) ($team2Employees * 0.02)), // 2% Manager
                    'departments' => min(200, max(10, (int) ($team2Employees / 1000))),
                    'professions' => min(100, max(5, (int) ($team2Employees / 5000))),
                    'stages' => 16,
                ],
                'performance' => [
                    'chunk_size' => $chunkSize,
                    'workers' => $workers,
                ],
                'faker_locale' => 'de_DE',
                'delete_existing' => $this->option('truncate') || $this->option('clean'),
            ];

            // Basis-Daten erstellen
            $this->benchmark('base_data_start');
            $baseData = $this->createBaseData($config);
            $this->benchmark('base_data_end');

            // Master-Daten erstellen
            $this->benchmark('master_data_start');
            $this->createMasterData($baseData, $config);
            $this->benchmark('master_data_end');

            // Mitarbeiter erstellen basierend auf Methode
            $this->benchmark('employees_start');

            switch ($method) {
                case 'csv':
                    $this->seedWithCsv($baseData, $config);
                    break;

                case 'queue':
                    $this->seedWithQueues($baseData, $config);
                    break;

                case 'parallel':
                    $this->seedWithParallelProcesses($baseData, $config);
                    break;

                case 'bulk':
                default:
                    $this->seedWithBulkInserts($baseData, $config);
                    break;
            }

            $this->benchmark('employees_end');

            // Nachbereitung
            $this->postProcess();

            // Ergebnisse anzeigen
            $this->showResults($totalEmployees);

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Fehler: ' . $e->getMessage());
            $this->error($e->getTraceAsString());

            // Cleanup bei Fehler
            if ($this->option('no-foreign-keys')) {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }
            DB::statement('SET unique_checks=1');
            DB::statement('SET autocommit=1');

            return 1;
        }
    }

    /**
     * Erstelle Basis-Daten (Owner, Company, Teams, Roles)
     */
    protected function createBaseData($config): array
    {
        $this->info('🏗️  Erstelle Basis-Daten...');

        $ownerEmail = $config['owner']['email'];

        // Prüfe ob Owner bereits existiert
        $owner = User::where('email', $ownerEmail)->first();

        if ($owner) {
            $this->warn("⚠️  Owner mit E-Mail {$ownerEmail} existiert bereits, verwende existierenden Owner...");

            // Hole existierende Company
            $company = Company::where('owner_id', $owner->id)->first();

            if (!$company) {
                // Falls keine Company existiert, erstelle eine neue
                $industry = Industry::firstOrCreate(['name' => $config['company']['industry']]);

                $company = Company::create([
                    'company_name' => $config['company']['name'],
                    'email' => $config['company']['email'],
                    'phone_1' => $config['company']['phone'],
                    'is_active' => true,
                    'owner_id' => $owner->id,
                    'created_by' => $owner->id,
                    'company_type' => $config['company']['type'],
                    'company_size' => $config['company']['size'],
                    'registration_type' => CompanyRegistrationType::SELF_REGISTERED,
                    'industry_id' => $industry->id,
                ]);

                $owner->update(['company_id' => $company->id]);
            }

            // Hole oder erstelle Teams
            $team1 = Team::firstOrCreate(
                [
                    'name' => $config['team1']['name'],
                    'company_id' => $company->id,
                ],
                [
                    'user_id' => $owner->id,
                    'personal_team' => true,
                ]
            );

            $team2 = Team::firstOrCreate(
                [
                    'name' => $config['team2']['name'],
                    'company_id' => $company->id,
                ],
                [
                    'user_id' => $owner->id,
                    'personal_team' => false,
                ]
            );

        } else {
            // Owner erstellen
            $owner = User::create([
                'name' => $config['owner']['name'],
                'last_name' => $config['owner']['last_name'],
                'email' => $ownerEmail,
                'email_verified_at' => now(),
                'password' => $this->passwordHash,
                'remember_token' => Str::random(10),
                'user_type' => UserType::Owner,
                'model_status' => ModelStatus::ACTIVE,
                'slug' => Str::slug($config['owner']['name'] . '-' . $config['owner']['last_name']) . '-' . Str::random(5),
            ]);

            // Owner Rolle zuweisen
            $ownerRole = Role::firstOrCreate(['name' => 'owner']);
            $owner->assignRole($ownerRole);

            // Industry erstellen
            $industry = Industry::firstOrCreate(['name' => $config['company']['industry']]);

            // Company erstellen
            $company = Company::create([
                'company_name' => $config['company']['name'],
                'email' => $config['company']['email'],
                'phone_1' => $config['company']['phone'],
                'is_active' => true,
                'owner_id' => $owner->id,
                'created_by' => $owner->id,
                'company_type' => $config['company']['type'],
                'company_size' => $config['company']['size'],
                'registration_type' => CompanyRegistrationType::SELF_REGISTERED,
                'industry_id' => $industry->id,
            ]);

            $owner->update(['company_id' => $company->id]);

            // Teams erstellen
            $team1 = Team::create([
                'name' => $config['team1']['name'],
                'user_id' => $owner->id,
                'company_id' => $company->id,
                'personal_team' => true,
            ]);

            $team2 = Team::create([
                'name' => $config['team2']['name'],
                'user_id' => $owner->id,
                'company_id' => $company->id,
                'personal_team' => false,
            ]);
        }

        // Update Owner's current team
        if (!$owner->current_team_id) {
            $owner->update(['current_team_id' => $team1->id]);
        }

        // Rollen erstellen
        $roles = $this->createRoles($owner, $company);

        return compact('owner', 'company', 'team1', 'team2', 'roles');
    }

    /**
     * Erstelle Rollen
     */
    protected function createRoles($owner, $company): array
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
     * Erstelle Master-Daten (Departments, Professions, Stages)
     */
    protected function createMasterData($baseData, $config): void
    {
        $this->info('📊 Erstelle Master-Daten...');

        // Lösche existierende Master-Daten für diese Teams
        Department::whereIn('team_id', [$baseData['team1']->id, $baseData['team2']->id])->delete();
        Profession::whereIn('team_id', [$baseData['team1']->id, $baseData['team2']->id])->delete();
        Stage::whereIn('team_id', [$baseData['team1']->id, $baseData['team2']->id])->delete();

        // Team 1 Master-Daten
        $this->preGeneratedData['team1'] = [
            'departments' => $this->createDepartmentsBulk(
                $baseData['team1'],
                $baseData['company'],
                $baseData['owner'],
                $config['team1']['departments']
            ),
            'professions' => $this->createProfessionsBulk(
                $baseData['team1'],
                $baseData['company'],
                $baseData['owner'],
                $config['team1']['professions']
            ),
            'stages' => $this->createStagesBulk(
                $baseData['team1'],
                $baseData['company'],
                $baseData['owner'],
                $config['team1']['stages']
            ),
        ];

        // Team 2 Master-Daten
        $this->preGeneratedData['team2'] = [
            'departments' => $this->createDepartmentsBulk(
                $baseData['team2'],
                $baseData['company'],
                $baseData['owner'],
                $config['team2']['departments'],
                'B55 '
            ),
            'professions' => $this->createProfessionsBulk(
                $baseData['team2'],
                $baseData['company'],
                $baseData['owner'],
                $config['team2']['professions'],
                'B55 '
            ),
            'stages' => $this->createStagesBulk(
                $baseData['team2'],
                $baseData['company'],
                $baseData['owner'],
                $config['team2']['stages'],
                'B55 '
            ),
        ];
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
     * CSV Import Methode
     */
    protected function seedWithCsv($baseData, $config): void
    {
        $this->info('📄 Verwende CSV Import Methode...');

        // Erhöhe Chunk-Size für CSV automatisch
        if ($config['performance']['chunk_size'] < 50000) {
            $this->warn('⚠️  Erhöhe Chunk-Size auf 50000 für bessere CSV Performance...');
            $config['performance']['chunk_size'] = 50000;
        }

        $tempDir = storage_path('app/temp_seed');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        // Erstelle CSV Dateien für beide Teams
        $totalEmployees = $config['team1']['employees'] + $config['team2']['employees'];

        $this->withProgressBar($totalEmployees, function ($bar) use ($baseData, $config, $tempDir) {
            // Team 1
            $this->generateCsvForTeam(
                $baseData['team1'],
                $baseData,
                $config['team1'],
                'team1',
                0,
                $bar,
                $tempDir,
                $config
            );

            // Team 2
            $this->generateCsvForTeam(
                $baseData['team2'],
                $baseData,
                $config['team2'],
                'team2',
                $config['team1']['employees'],
                $bar,
                $tempDir,
                $config
            );
        });

        // Importiere CSV Dateien
        $this->importGeneratedCsvFiles($tempDir);

        // Cleanup
        $this->cleanupTempFiles($tempDir);
    }

    /**
     * Generiere CSV für ein Team
     */
    protected function generateCsvForTeam($team, $baseData, $teamConfig, $configKey, $indexOffset, $progressBar, $tempDir, $config): void
    {
        $employeeCount = $teamConfig['employees'];
        $managerCount = $teamConfig['managers'];

        // CSV Dateien
        $usersCsv = fopen($tempDir . '/users_' . $configKey . '.csv', 'w');
        $employeesCsv = fopen($tempDir . '/employees_' . $configKey . '.csv', 'w');
        $rolesCsv = fopen($tempDir . '/roles_' . $configKey . '.csv', 'w');
        $teamUserCsv = fopen($tempDir . '/team_user_' . $configKey . '.csv', 'w');

        $currentTime = now()->toDateTimeString();
        $departmentIds = $this->preGeneratedData[$configKey]['departments'];
        $professionIds = $this->preGeneratedData[$configKey]['professions'];
        $stageIds = $this->preGeneratedData[$configKey]['stages'];

        $managersCreated = 0;

        for ($i = 0; $i < $employeeCount; $i++) {
            $index = $i + $indexOffset + 1;

            $firstName = $this->faker->firstName;
            $lastName = $this->faker->lastName;
            $suffix = $configKey === 'team2' ? 'b55' : 't1';
            $email = strtolower($firstName . '.' . $lastName . '.' . $suffix . $index . '@firma.ch');

            // User CSV
            fputcsv($usersCsv, [
                $firstName,
                $lastName,
                $email,
                $currentTime,
                $this->passwordHash,
                Str::random(10),
                $baseData['company']->id,
                UserType::Employee->value,
                $departmentIds[array_rand($departmentIds)],
                ModelStatus::ACTIVE->value,
                '+417' . str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT),
                $firstName . '-' . $lastName . '-' . $suffix . '-' . $index,
                $baseData['owner']->id,
                Carbon::now()->subDays(rand(0, 1095))->toDateTimeString(),
                $currentTime,
                $currentTime,
            ]);

            // Employee CSV
            $prefix = $configKey === 'team2' ? 'B55-' : 'PN';
            $padLength = $configKey === 'team2' ? 5 : 8;

            fputcsv($employeesCsv, [
                $email, // Verwende Email als temporäre Referenz
                $professionIds[array_rand($professionIds)],
                $stageIds[array_rand($stageIds)],
                $prefix . str_pad($index, $padLength, '0', STR_PAD_LEFT),
                $baseData['owner']->id,
                $this->getRandomEmployeeStatus(),
                $currentTime,
                $currentTime,
            ]);

            // Role CSV
            if ($managersCreated < $managerCount) {
                $roleId = $baseData['roles']['Manager'];
                $managersCreated++;
            } else {
                // Wähle zufällige Nicht-Manager Rolle
                $nonManagerRoles = ['Worker', 'Editor', 'Temporary'];
                $randomRole = $nonManagerRoles[array_rand($nonManagerRoles)];
                $roleId = $baseData['roles'][$randomRole];
            }

            fputcsv($rolesCsv, [
                $roleId,
                'App\\Models\\User',
                $email, // Verwende Email als temporäre Referenz
            ]);

            // Team User CSV
            fputcsv($teamUserCsv, [
                $team->id,
                $email, // Verwende Email als temporäre Referenz
                'editor',
                $currentTime,
                $currentTime,
            ]);

            if ($i % 10000 === 0) {
                $progressBar->advance(10000);

                // Flush die Buffer alle 100k Einträge für bessere Performance
                if ($i % 100000 === 0) {
                    fflush($usersCsv);
                    fflush($employeesCsv);
                    fflush($rolesCsv);
                    fflush($teamUserCsv);
                }
            }
        }

        // Stelle sicher, dass die Progress Bar komplett ist
        $remaining = $employeeCount - ($progressBar->getProgress() % $employeeCount);
        if ($remaining > 0 && $remaining < $employeeCount) {
            $progressBar->advance($remaining);
        }

        fclose($usersCsv);
        fclose($employeesCsv);
        fclose($rolesCsv);
        fclose($teamUserCsv);
    }

    /**
     * Importiere generierte CSV Dateien
     */
    protected function importGeneratedCsvFiles($tempDir): void
    {
        $this->info('📥 Importiere CSV Dateien...');

        $csvFiles = glob($tempDir . '/*.csv');

        // Phase 1: Users importieren
        $this->info('👤 Importiere Users...');
        foreach ($csvFiles as $csvFile) {
            $filename = basename($csvFile);
            if (strpos($filename, 'users_') === 0) {
                $this->importUsersCsv($csvFile);
            }
        }

        // Phase 2: Employees importieren
        $this->info('👥 Importiere Employees...');
        foreach ($csvFiles as $csvFile) {
            $filename = basename($csvFile);
            if (strpos($filename, 'employees_') === 0) {
                $this->importEmployeesCsv($csvFile);
            }
        }

        // Phase 3: Roles importieren
        $this->info('🎭 Importiere Roles...');
        foreach ($csvFiles as $csvFile) {
            $filename = basename($csvFile);
            if (strpos($filename, 'roles_') === 0) {
                $this->importRolesCsv($csvFile);
            }
        }

        // Phase 4: Team-Zuweisungen importieren
        $this->info('👥 Importiere Team-Zuweisungen...');
        foreach ($csvFiles as $csvFile) {
            $filename = basename($csvFile);
            if (strpos($filename, 'team_user_') === 0) {
                $this->importTeamUserCsv($csvFile);
            }
        }
    }

    /**
     * Importiere Users CSV
     */
    protected function importUsersCsv($csvFile): void
    {
        $handle = fopen($csvFile, 'r');
        $userData = [];
        $batchSize = 10000; // Größere Batches für bessere Performance

        while (($data = fgetcsv($handle)) !== false) {
            $userData[] = [
                'name' => $data[0],
                'last_name' => $data[1],
                'email' => $data[2],
                'email_verified_at' => $data[3],
                'password' => $data[4],
                'remember_token' => $data[5],
                'company_id' => $data[6],
                'user_type' => $data[7],
                'department_id' => $data[8],
                'model_status' => $data[9],
                'phone_1' => $data[10],
                'slug' => $data[11],
                'created_by' => $data[12],
                'joined_at' => $data[13],
                'created_at' => $data[14],
                'updated_at' => $data[15],
            ];

            if (count($userData) >= $batchSize) {
                DB::table('users')->insert($userData);
                $userData = [];
            }
        }

        if (!empty($userData)) {
            DB::table('users')->insert($userData);
        }

        fclose($handle);
    }

    /**
     * Importiere Employees CSV
     */
    protected function importEmployeesCsv($csvFile): void
    {
        $handle = fopen($csvFile, 'r');
        $employeeData = [];
        $batchSize = 10000; // Größere Batches

        // Lade alle User IDs in Memory für bessere Performance
        $this->info('📊 Lade User IDs...');
        $userIds = DB::table('users')->pluck('id', 'email')->toArray();

        while (($data = fgetcsv($handle)) !== false) {
            // Hole User ID basierend auf Email
            $userId = $userIds[$data[0]] ?? null;

            if ($userId) {
                $employeeData[] = [
                    'user_id' => $userId,
                    'profession_id' => $data[1],
                    'stage_id' => $data[2],
                    'personal_number' => $data[3],
                    'supervisor_id' => $data[4],
                    'employee_status' => $data[5],
                    'created_at' => $data[6],
                    'updated_at' => $data[7],
                ];

                if (count($employeeData) >= $batchSize) {
                    DB::table('employees')->insert($employeeData);
                    $employeeData = [];
                }
            }
        }

        if (!empty($employeeData)) {
            DB::table('employees')->insert($employeeData);
        }

        fclose($handle);
    }

    /**
     * Importiere Roles CSV
     */
    protected function importRolesCsv($csvFile): void
    {
        $handle = fopen($csvFile, 'r');
        $roleData = [];
        $batchSize = 10000; // Größere Batches

        // Lade alle User IDs in Memory für bessere Performance
        $userIds = DB::table('users')->pluck('id', 'email')->toArray();

        while (($data = fgetcsv($handle)) !== false) {
            $userId = $userIds[$data[2]] ?? null;

            if ($userId) {
                $roleData[] = [
                    'role_id' => $data[0],
                    'model_type' => $data[1],
                    'model_id' => $userId,
                ];

                if (count($roleData) >= $batchSize) {
                    DB::table('model_has_roles')->insert($roleData);
                    $roleData = [];
                }
            }
        }

        if (!empty($roleData)) {
            DB::table('model_has_roles')->insert($roleData);
        }

        fclose($handle);
    }

    /**
     * Importiere Team User CSV
     */
    protected function importTeamUserCsv($csvFile): void
    {
        $handle = fopen($csvFile, 'r');
        $teamUserData = [];
        $batchSize = 10000; // Größere Batches

        // Lade alle User IDs in Memory für bessere Performance
        $userIds = DB::table('users')->pluck('id', 'email')->toArray();

        while (($data = fgetcsv($handle)) !== false) {
            $userId = $userIds[$data[1]] ?? null;

            if ($userId) {
                $teamUserData[] = [
                    'team_id' => $data[0],
                    'user_id' => $userId,
                    'role' => $data[2],
                    'created_at' => $data[3],
                    'updated_at' => $data[4],
                ];

                if (count($teamUserData) >= $batchSize) {
                    DB::table('team_user')->insert($teamUserData);
                    $teamUserData = [];
                }
            }
        }

        if (!empty($teamUserData)) {
            DB::table('team_user')->insert($teamUserData);
        }

        fclose($handle);
    }

    /**
     * Cleanup temporäre Dateien
     */
    protected function cleanupTempFiles($tempDir): void
    {
        $files = glob($tempDir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        rmdir($tempDir);
    }

    /**
     * Queue-basierte Methode
     */
    protected function seedWithQueues($baseData, $config): void
    {
        $this->info('📬 Verwende Queue-basierte Methode...');

        $workers = $config['performance']['workers'];
        $chunkSize = $config['performance']['chunk_size'];

        // Jobs für Team 1
        $team1Jobs = ceil($config['team1']['employees'] / $chunkSize);
        for ($i = 0; $i < $team1Jobs; $i++) {
            $start = $i * $chunkSize;
            $end = min(($i + 1) * $chunkSize, $config['team1']['employees']);

            SeedEmployeesJob::dispatch(
                $start,
                $end,
                ['id' => $baseData['team1']->id],
                ['id' => $baseData['company']->id],
                ['id' => $baseData['owner']->id],
                'team1',
                0,
                $config['team1']
            )->onQueue('seeding');
        }

        // Jobs für Team 2
        $team2Jobs = ceil($config['team2']['employees'] / $chunkSize);
        for ($i = 0; $i < $team2Jobs; $i++) {
            $start = $i * $chunkSize;
            $end = min(($i + 1) * $chunkSize, $config['team2']['employees']);

            SeedEmployeesJob::dispatch(
                $start,
                $end,
                ['id' => $baseData['team2']->id],
                ['id' => $baseData['company']->id],
                ['id' => $baseData['owner']->id],
                'team2',
                $config['team1']['employees'],
                $config['team2']
            )->onQueue('seeding');
        }

        $totalJobs = $team1Jobs + $team2Jobs;
        $this->info("✅ {$totalJobs} Jobs in Queue dispatched!");
        $this->info("Starte Worker mit: php artisan queue:work --queue=seeding --max-jobs={$totalJobs}");
    }

    /**
     * Parallele Prozesse mit Symfony Process
     */
    protected function seedWithParallelProcesses($baseData, $config): void
    {
        $this->info('🔄 Verwende parallele Prozesse...');

        $workers = $config['performance']['workers'];
        $totalEmployees = $config['team1']['employees'] + $config['team2']['employees'];
        $employeesPerWorker = ceil($totalEmployees / $workers);

        $processes = [];

        for ($i = 0; $i < $workers; $i++) {
            $start = $i * $employeesPerWorker;
            $end = min(($i + 1) * $employeesPerWorker, $totalEmployees);

            $command = sprintf(
                'php artisan seed:worker %d %d %s',
                $start,
                $end,
                base64_encode(json_encode([
                    'baseData' => [
                        'team1' => ['id' => $baseData['team1']->id],
                        'team2' => ['id' => $baseData['team2']->id],
                        'company' => ['id' => $baseData['company']->id],
                        'owner' => ['id' => $baseData['owner']->id],
                        'roles' => $baseData['roles'],
                    ],
                    'config' => $config,
                ]))
            );

            $process = \Symfony\Component\Process\Process::fromShellCommandline($command);
            $process->start();

            $processes[] = $process;

            $this->info("Worker {$i} gestartet: {$start} - {$end}");
        }

        // Warte auf alle Prozesse
        $this->info('⏳ Warte auf Worker...');

        foreach ($processes as $i => $process) {
            $process->wait();
            $this->info("Worker {$i} abgeschlossen!");
        }
    }

    /**
     * Standard Bulk Insert Methode
     */
    protected function seedWithBulkInserts($baseData, $config): void
    {
        $this->info('📦 Verwende optimierte Bulk Inserts...');

        // Die Bereinigung wurde bereits in prepareDatabase() durchgeführt
        // wenn --clean oder --truncate gesetzt wurde

        // Team 1
        $this->createEmployeesForTeam(
            $baseData['team1'],
            $baseData['company'],
            $baseData['owner'],
            $baseData['roles'],
            $config['team1'],
            'team1',
            0
        );

        // Team 2
        $this->createEmployeesForTeam(
            $baseData['team2'],
            $baseData['company'],
            $baseData['owner'],
            $baseData['roles'],
            $config['team2'],
            'team2',
            $config['team1']['employees']
        );
    }

    /**
     * Erstelle Mitarbeiter für ein Team
     */
    protected function createEmployeesForTeam($team, $company, $owner, $roles, $config, $configKey, $indexOffset): void
    {
        $employeeCount = $config['employees'];
        $managerCount = $config['managers'];
        $chunkSize = $this->option('chunk');

        $this->info("👥 Erstelle {$this->formatNumber($employeeCount)} Mitarbeiter für {$team->name}...");

        $progressBar = $this->output->createProgressBar($employeeCount);
        $progressBar->start();

        $departmentIds = $this->preGeneratedData[$configKey]['departments'];
        $professionIds = $this->preGeneratedData[$configKey]['professions'];
        $stageIds = $this->preGeneratedData[$configKey]['stages'];

        $nonManagerRoleIds = [$roles['Worker'], $roles['Editor'], $roles['Temporary']];
        $managersCreated = 0;

        for ($i = 0; $i < $employeeCount; $i += $chunkSize) {
            $currentChunkSize = min($chunkSize, $employeeCount - $i);

            $this->createEmployeeChunk(
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
                $configKey
            );

            $progressBar->advance($currentChunkSize);

            // Garbage Collection
            if (($i / $chunkSize) % 10 == 0) {
                gc_collect_cycles();
            }
        }

        $progressBar->finish();
        $this->newLine();
    }

    /**
     * Erstelle Employee Chunk
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
        $currentTime = now()->toDateTimeString();
        $companyId = $company->id;
        $teamId = $team->id;
        $ownerId = $owner->id;
        $managerRoleId = $roles['Manager'];

        $userData = [];
        $userEmails = [];

        for ($j = 0; $j < $chunkSize; $j++) {
            $index = $startIndex + $j + $indexOffset + 1;

            $firstName = $this->faker->firstName;
            $lastName = $this->faker->lastName;
            $suffix = $configKey === 'team2' ? 'b55' : 't1';

            $email = strtolower($firstName . '.' . $lastName . '.' . $suffix . $index . '@firma.ch');
            $userEmails[] = $email;

            $userData[] = [
                'name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'email_verified_at' => $currentTime,
                'password' => $this->passwordHash,
                'remember_token' => Str::random(10),
                'company_id' => $companyId,
                'user_type' => UserType::Employee->value,
                'department_id' => $departmentIds[array_rand($departmentIds)],
                'model_status' => ModelStatus::ACTIVE->value,
                'phone_1' => '+417' . str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT),
                'slug' => $firstName . '-' . $lastName . '-' . $suffix . '-' . $index,
                'created_by' => $ownerId,
                'joined_at' => Carbon::now()->subDays(rand(0, 1095))->toDateTimeString(),
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ];
        }

        // Bulk Insert Users
        DB::table('users')->insert($userData);

        // Hole User IDs
        $userIds = DB::table('users')
            ->whereIn('email', $userEmails)
            ->pluck('id', 'email')
            ->toArray();

        // Erstelle Employee, Role und Team-Zuweisungen
        $employeeData = [];
        $roleData = [];
        $teamUserData = [];

        $j = 0;
        foreach ($userEmails as $email) {
            if (!isset($userIds[$email])) continue;

            $userId = $userIds[$email];
            $index = $startIndex + $j + $indexOffset + 1;

            // Employee
            $prefix = $configKey === 'team2' ? 'B55-' : 'PN';
            $padLength = $configKey === 'team2' ? 5 : 8;

            $employeeData[] = [
                'user_id' => $userId,
                'profession_id' => $professionIds[array_rand($professionIds)],
                'stage_id' => $stageIds[array_rand($stageIds)],
                'personal_number' => $prefix . str_pad($index, $padLength, '0', STR_PAD_LEFT),
                'supervisor_id' => $ownerId,
                'employee_status' => $this->getRandomEmployeeStatus(),
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ];

            // Role
            if ($managersCreated < $managerCount) {
                $roleId = $managerRoleId;
                $managersCreated++;
            } else {
                $roleId = $nonManagerRoleIds[array_rand($nonManagerRoleIds)];
            }

            $roleData[] = [
                'role_id' => $roleId,
                'model_type' => 'App\\Models\\User',
                'model_id' => $userId,
            ];

            // Team User
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
            DB::table('employees')->insert($employeeData);
        }

        if (!empty($roleData)) {
            DB::table('model_has_roles')->insert($roleData);
        }

        if (!empty($teamUserData)) {
            DB::table('team_user')->insert($teamUserData);
        }

        // Speicher freigeben
        unset($userData, $userEmails, $employeeData, $roleData, $teamUserData, $userIds);
    }

    /**
     * Hole zufälligen Employee Status
     */
    protected function getRandomEmployeeStatus(): string
    {
        $random = mt_rand(1, 100);

        if ($random <= 10) return EmployeeStatus::ONBOARDING->value;
        if ($random <= 35) return EmployeeStatus::PROBATION->value;
        if ($random <= 90) return EmployeeStatus::EMPLOYED->value;
        if ($random <= 95) return EmployeeStatus::ONLEAVE->value;

        return EmployeeStatus::LEAVE->value;
    }

    /**
     * Datenbank vorbereiten
     */
    protected function prepareDatabase(): void
    {
        $this->info('🔧 Bereite Datenbank vor...');

        // Cache leeren
        Artisan::call('optimize:clear');

        // Truncate wenn gewünscht
        if ($this->option('truncate')) {
            $this->warn('🗑️  Leere Tabellen...');
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Lösche speziell den Owner und seine Daten
            $ownerEmail = $this->option('owner-email');
            $owner = User::where('email', $ownerEmail)->first();
            if ($owner && $owner->company_id) {
                $companyId = $owner->company_id;

                // Lösche alle Mitarbeiter der Company
                Employee::whereHas('user', function ($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                })->delete();

                // Lösche alle User der Company (außer Owner)
                User::where('company_id', $companyId)
                    ->where('id', '!=', $owner->id)
                    ->delete();

                // Lösche Master-Daten
                Profession::where('company_id', $companyId)->delete();
                Stage::where('company_id', $companyId)->delete();
                Department::where('company_id', $companyId)->delete();

                // Lösche Teams
                Team::where('company_id', $companyId)->delete();

                // Lösche Company
                Company::where('id', $companyId)->delete();

                // Lösche Owner
                $owner->delete();
            }

            // Truncate alle anderen Tabellen
            $tables = ['employees', 'model_has_roles', 'team_user'];

            foreach ($tables as $table) {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    DB::table($table)->truncate();
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } elseif ($this->option('clean')) {
            // Bei --clean nur Mitarbeiter löschen
            $this->warn('🧹 Lösche existierende Mitarbeiter...');

            $ownerEmail = $this->option('owner-email');
            $owner = User::where('email', $ownerEmail)->first();

            if ($owner && $owner->company_id) {
                $companyId = $owner->company_id;
                $ownerId = $owner->id;

                DB::statement('SET FOREIGN_KEY_CHECKS=0');

                // Lösche Employees
                Employee::whereHas('user', function ($query) use ($companyId, $ownerId) {
                    $query->where('company_id', $companyId)->where('id', '!=', $ownerId);
                })->delete();

                // Lösche Team-Zuweisungen (außer Owner)
                DB::table('team_user')
                    ->where('user_id', '!=', $ownerId)
                    ->whereIn('team_id', function($query) use ($companyId) {
                        $query->select('id')
                            ->from('teams')
                            ->where('company_id', $companyId);
                    })
                    ->delete();

                // Lösche Rollen-Zuweisungen (außer Owner)
                DB::table('model_has_roles')
                    ->where('model_type', 'App\\Models\\User')
                    ->whereIn('model_id', function ($query) use ($companyId, $ownerId) {
                        $query->select('id')
                            ->from('users')
                            ->where('company_id', $companyId)
                            ->where('id', '!=', $ownerId);
                    })
                    ->delete();

                // Lösche Users (außer Owner)
                User::where('company_id', $companyId)
                    ->where('id', '!=', $ownerId)
                    ->delete();

                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }
        }

        // Performance-Optimierungen
        if ($this->option('no-foreign-keys')) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }

        DB::statement('SET unique_checks=0');
        DB::statement('SET autocommit=0');

        // MySQL spezifische Optimierungen
        try {
            DB::statement('SET SESSION bulk_insert_buffer_size = 1073741824');
            DB::statement('SET SESSION myisam_sort_buffer_size = 1073741824');
            DB::statement('SET SESSION read_buffer_size = 1073741824');
            DB::statement('SET SESSION sort_buffer_size = 1073741824');
        } catch (\Exception $e) {
            // Ignoriere wenn keine Berechtigung
        }
    }

    /**
     * Nachbereitung
     */
    protected function postProcess(): void
    {
        $this->info('🔨 Nachbereitung...');

        // Foreign Keys wieder aktivieren
        if ($this->option('no-foreign-keys')) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        DB::statement('SET unique_checks=1');
        DB::statement('SET autocommit=1');

        // Indexes neu aufbauen wenn nötig
        if ($this->option('no-indexes')) {
            $this->info('🔧 Baue Indexes neu auf...');
            // Implementiere Index-Neuaufbau
        }

        // Statistiken aktualisieren
        $this->info('📊 Aktualisiere Statistiken...');
        try {
            DB::statement('ANALYZE TABLE users');
            DB::statement('ANALYZE TABLE employees');
        } catch (\Exception $e) {
            // Ignoriere wenn nicht MySQL
        }
    }

    /**
     * Zeige Ergebnisse
     */
    protected function showResults($totalEmployees): void
    {
        $executionTime = round(microtime(true) - $this->startTime, 2);
        $employeesPerSecond = round($totalEmployees / $executionTime);
        $memoryPeak = round(memory_get_peak_usage() / 1024 / 1024, 2);

        $this->newLine();
        $this->info('✅ Seeding erfolgreich abgeschlossen!');
        $this->newLine();

        $this->table(
            ['Metrik', 'Wert'],
            [
                ['Owner E-Mail', $this->option('owner-email')],
                ['Gesamte Mitarbeiter', $this->formatNumber($totalEmployees)],
                ['Ausführungszeit', $executionTime . ' Sekunden'],
                ['Mitarbeiter pro Sekunde', $this->formatNumber($employeesPerSecond)],
                ['Memory Peak Usage', $memoryPeak . ' MB'],
            ]
        );

        // Benchmarks anzeigen wenn aktiviert
        if ($this->option('benchmark')) {
            $this->showBenchmarks();
        }
    }

    /**
     * Benchmark speichern
     */
    protected function benchmark($name): void
    {
        $this->benchmarks[$name] = microtime(true);
    }

    /**
     * Benchmarks anzeigen
     */
    protected function showBenchmarks(): void
    {
        $this->newLine();
        $this->info('⏱️  Benchmarks:');

        $benchmarkPairs = [
            'Basis-Daten' => ['base_data_start', 'base_data_end'],
            'Master-Daten' => ['master_data_start', 'master_data_end'],
            'Mitarbeiter' => ['employees_start', 'employees_end'],
        ];

        $data = [];
        foreach ($benchmarkPairs as $name => [$start, $end]) {
            if (isset($this->benchmarks[$start]) && isset($this->benchmarks[$end])) {
                $duration = round($this->benchmarks[$end] - $this->benchmarks[$start], 2);
                $data[] = [$name, $duration . ' Sekunden'];
            }
        }

        $this->table(['Phase', 'Dauer'], $data);
    }

    /**
     * Formatiere Zahlen
     */
    protected function formatNumber($number): string
    {
        return number_format($number, 0, ',', '.');
    }
}
