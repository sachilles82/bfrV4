<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class SeedEmployeesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $startIndex;
    protected $endIndex;
    protected $teamData;
    protected $companyData;
    protected $ownerData;
    protected $configKey;
    protected $indexOffset;
    protected $seedConfig;

    /**
     * Create a new job instance.
     */
    public function __construct(
        int $startIndex,
        int $endIndex,
        array $teamData,
        array $companyData,
        array $ownerData,
        string $configKey,
        int $indexOffset,
        array $seedConfig
    ) {
        $this->startIndex = $startIndex;
        $this->endIndex = $endIndex;
        $this->teamData = $teamData;
        $this->companyData = $companyData;
        $this->ownerData = $ownerData;
        $this->configKey = $configKey;
        $this->indexOffset = $indexOffset;
        $this->seedConfig = $seedConfig;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $faker = Faker::create('de_DE');
        $employeeCount = $this->endIndex - $this->startIndex;
        $currentTime = now()->toDateTimeString();

        // Hole benötigte IDs
        $departmentIds = DB::table('departments')
            ->where('team_id', $this->teamData['id'])
            ->pluck('id')
            ->toArray();

        $professionIds = DB::table('professions')
            ->where('team_id', $this->teamData['id'])
            ->pluck('id')
            ->toArray();

        $stageIds = DB::table('stages')
            ->where('team_id', $this->teamData['id'])
            ->pluck('id')
            ->toArray();

        $roleIds = DB::table('roles')
            ->where('company_id', $this->companyData['id'])
            ->pluck('id', 'name')
            ->toArray();

        // Batch-Daten vorbereiten
        $userData = [];
        $employeeData = [];
        $roleData = [];
        $teamUserData = [];

        for ($i = $this->startIndex; $i < $this->endIndex; $i++) {
            $index = $i + $this->indexOffset + 1;
            $firstName = $faker->firstName;
            $lastName = $faker->lastName;
            $suffix = $this->configKey === 'team2' ? 'b55' : 't1';
            $email = strtolower($firstName . '.' . $lastName . '.' . $suffix . $index . '@firma.ch');

            // User erstellen
            $userId = DB::table('users')->insertGetId([
                'name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'email_verified_at' => $currentTime,
                'password' => bcrypt('password'),
                'remember_token' => \Str::random(10),
                'company_id' => $this->companyData['id'],
                'user_type' => 'employee',
                'department_id' => $departmentIds[array_rand($departmentIds)],
                'model_status' => 'active',
                'phone_1' => '+417' . str_pad(mt_rand(0, 99999999), 8, '0'),
                'slug' => $firstName . '-' . $lastName . '-' . $suffix . '-' . $index,
                'created_by' => $this->ownerData['id'],
                'joined_at' => now()->subDays(rand(0, 1095)),
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ]);

            // Employee-Daten
            $prefix = $this->configKey === 'team2' ? 'B55-' : 'PN';
            $padLength = $this->configKey === 'team2' ? 5 : 8;

            $employeeData[] = [
                'user_id' => $userId,
                'profession_id' => $professionIds[array_rand($professionIds)],
                'stage_id' => $stageIds[array_rand($stageIds)],
                'personal_number' => $prefix . str_pad($index, $padLength, '0', STR_PAD_LEFT),
                'supervisor_id' => $this->ownerData['id'],
                'employee_status' => $this->getRandomEmployeeStatus(),
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ];

            // Rolle zuweisen
            $isManager = ($i - $this->startIndex) < ($this->seedConfig['managers'] / ($this->seedConfig['employees'] / $employeeCount));
            $roleId = $isManager ? $roleIds['Manager'] : $roleIds[array_rand(['Worker', 'Editor', 'Temporary'])];

            $roleData[] = [
                'role_id' => $roleId,
                'model_type' => \App\Models\User::class,
                'model_id' => $userId,
            ];

            // Team-Zuweisung
            $teamUserData[] = [
                'team_id' => $this->teamData['id'],
                'user_id' => $userId,
                'role' => 'editor',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ];
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
    }

    /**
     * Zufälliger Employee Status
     */
    protected function getRandomEmployeeStatus(): string
    {
        $random = mt_rand(1, 100);

        if ($random <= 10) return 'onboarding';
        if ($random <= 35) return 'probation';
        if ($random <= 90) return 'employed';
        if ($random <= 95) return 'onleave';

        return 'leave';
    }
}
