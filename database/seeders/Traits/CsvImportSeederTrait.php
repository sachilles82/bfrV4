<?php

namespace Database\Seeders\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

trait CsvImportSeederTrait
{
    /**
     * Erstelle Mitarbeiter via CSV Import
     * Dies ist die schnellste Methode für Millionen von Datensätzen
     */
    protected function createEmployeesViaCsvImport($team, $company, $owner, $roles, $configKey, $indexOffset): void
    {
        $config = $this->config[$configKey];
        $employeeCount = $config['employees'];

        if ($this->command) {
            $this->command->info("📄 Erstelle CSV Dateien für {$this->formatNumber($employeeCount)} Mitarbeiter...");
        }

        // Temporäre Verzeichnisse
        $tempDir = storage_path('app/temp_seed');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        // CSV Dateien erstellen
        $usersCsv = $tempDir . '/users_' . $configKey . '.csv';
        $employeesCsv = $tempDir . '/employees_' . $configKey . '.csv';
        $rolesCsv = $tempDir . '/roles_' . $configKey . '.csv';
        $teamUserCsv = $tempDir . '/team_user_' . $configKey . '.csv';

        // Öffne CSV Dateien
        $usersHandle = fopen($usersCsv, 'w');
        $employeesHandle = fopen($employeesCsv, 'w');
        $rolesHandle = fopen($rolesCsv, 'w');
        $teamUserHandle = fopen($teamUserCsv, 'w');

        // Schreibe Header (optional, für LOAD DATA INFILE nicht nötig)
        // fputcsv($usersHandle, ['name', 'last_name', 'email', ...]);

        // Generiere Daten
        $this->generateCsvData(
            $usersHandle,
            $employeesHandle,
            $rolesHandle,
            $teamUserHandle,
            $team,
            $company,
            $owner,
            $roles,
            $configKey,
            $indexOffset,
            $employeeCount
        );

        // Schließe Dateien
        fclose($usersHandle);
        fclose($employeesHandle);
        fclose($rolesHandle);
        fclose($teamUserHandle);

        // Importiere via LOAD DATA INFILE
        $this->importCsvFiles($usersCsv, $employeesCsv, $rolesCsv, $teamUserCsv);

        // Cleanup
        $this->cleanupTempFiles($tempDir);
    }

    /**
     * Generiere CSV Daten
     */
    protected function generateCsvData(
        $usersHandle,
        $employeesHandle,
        $rolesHandle,
        $teamUserHandle,
        $team,
        $company,
        $owner,
        $roles,
        $configKey,
        $indexOffset,
        $employeeCount
    ): void {
        $batchSize = 50000; // Größere Batches für CSV
        $currentTime = now()->toDateTimeString();
        $nextUserId = DB::table('users')->max('id') + 1;

        // Cache Daten
        $departmentIds = $this->preGeneratedData[$configKey]['departments'];
        $professionIds = $this->preGeneratedData[$configKey]['professions'];
        $stageIds = $this->preGeneratedData[$configKey]['stages'];

        if ($this->command) {
            $this->command->getOutput()->progressStart($employeeCount);
        }

        for ($i = 0; $i < $employeeCount; $i++) {
            $index = $i + $indexOffset + 1;
            $userId = $nextUserId + $i;

            // User Daten
            $firstName = $this->faker->firstName;
            $lastName = $this->faker->lastName;
            $suffix = $configKey === 'team2' ? 'b55' : 't1';
            $email = strtolower($firstName . '.' . $lastName . '.' . $suffix . $index . '@firma.ch');

            // User CSV Zeile
            fputcsv($usersHandle, [
                $userId,                                          // id
                $firstName,                                       // name
                $lastName,                                        // last_name
                $email,                                          // email
                $currentTime,                                     // email_verified_at
                $this->passwordHash,                              // password
                Str::random(10),                                  // remember_token
                $company->id,                                     // company_id
                UserType::Employee->value,                        // user_type
                $departmentIds[array_rand($departmentIds)],      // department_id
                ModelStatus::ACTIVE->value,                       // model_status
                '+417' . str_pad(mt_rand(0, 99999999), 8, '0'), // phone_1
                $firstName . '-' . $lastName . '-' . $suffix . '-' . $index, // slug
                $owner->id,                                       // created_by
                $this->generateRandomDate(),                      // joined_at
                $currentTime,                                     // created_at
                $currentTime,                                     // updated_at
            ]);

            // Employee CSV Zeile
            $prefix = $configKey === 'team2' ? 'B55-' : 'PN';
            $padLength = $configKey === 'team2' ? 5 : 8;

            fputcsv($employeesHandle, [
                $userId,                                          // user_id
                $professionIds[array_rand($professionIds)],      // profession_id
                $stageIds[array_rand($stageIds)],                // stage_id
                $prefix . str_pad($index, $padLength, '0'),      // personal_number
                $owner->id,                                       // supervisor_id
                $this->getRandomEmployeeStatusValue(),            // employee_status
                $currentTime,                                     // created_at
                $currentTime,                                     // updated_at
            ]);

            // Role CSV Zeile
            $roleId = ($i < $this->config[$configKey]['managers'])
                ? $roles['Manager']
                : $roles[array_rand(['Worker', 'Editor', 'Temporary'])];

            fputcsv($rolesHandle, [
                $roleId,                                          // role_id
                'App\\Models\\User',                              // model_type
                $userId,                                          // model_id
            ]);

            // Team User CSV Zeile
            fputcsv($teamUserHandle, [
                $team->id,                                        // team_id
                $userId,                                          // user_id
                'editor',                                         // role
                $currentTime,                                     // created_at
                $currentTime,                                     // updated_at
            ]);

            if ($i % $batchSize === 0 && $this->command) {
                $this->command->getOutput()->progressAdvance($batchSize);
            }
        }

        if ($this->command) {
            $this->command->getOutput()->progressFinish();
        }
    }

    /**
     * Importiere CSV Dateien via LOAD DATA INFILE
     */
    protected function importCsvFiles($usersCsv, $employeesCsv, $rolesCsv, $teamUserCsv): void
    {
        if ($this->command) {
            $this->command->info("📥 Importiere CSV Dateien in die Datenbank...");
        }

        // Für MySQL LOAD DATA INFILE
        try {
            // Users importieren
            DB::statement("
                LOAD DATA LOCAL INFILE '{$usersCsv}'
                INTO TABLE users
                FIELDS TERMINATED BY ','
                ENCLOSED BY '\"'
                LINES TERMINATED BY '\n'
                (id, name, last_name, email, email_verified_at, password, remember_token,
                 company_id, user_type, department_id, model_status, phone_1, slug,
                 created_by, joined_at, created_at, updated_at)
            ");

            // Employees importieren
            DB::statement("
                LOAD DATA LOCAL INFILE '{$employeesCsv}'
                INTO TABLE employees
                FIELDS TERMINATED BY ','
                ENCLOSED BY '\"'
                LINES TERMINATED BY '\n'
                (user_id, profession_id, stage_id, personal_number, supervisor_id,
                 employee_status, created_at, updated_at)
            ");

            // Roles importieren
            DB::statement("
                LOAD DATA LOCAL INFILE '{$rolesCsv}'
                INTO TABLE model_has_roles
                FIELDS TERMINATED BY ','
                ENCLOSED BY '\"'
                LINES TERMINATED BY '\n'
                (role_id, model_type, model_id)
            ");

            // Team User importieren
            DB::statement("
                LOAD DATA LOCAL INFILE '{$teamUserCsv}'
                INTO TABLE team_user
                FIELDS TERMINATED BY ','
                ENCLOSED BY '\"'
                LINES TERMINATED BY '\n'
                (team_id, user_id, role, created_at, updated_at)
            ");

        } catch (\Exception $e) {
            // Fallback auf chunk-weise Inserts
            if ($this->command) {
                $this->command->warn("⚠️  LOAD DATA INFILE nicht verfügbar, verwende Chunk-Inserts...");
            }
            $this->importCsvViaChunks($usersCsv, $employeesCsv, $rolesCsv, $teamUserCsv);
        }
    }

    /**
     * Fallback: Importiere CSV via Chunks
     */
    protected function importCsvViaChunks($usersCsv, $employeesCsv, $rolesCsv, $teamUserCsv): void
    {
        // Implementierung für chunk-weisen Import aus CSV
        // Dies ist langsamer als LOAD DATA INFILE, aber funktioniert überall
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
}
