<?php

namespace Database\Seeders;

use Database\Seeders\Address\CitySeeder;
use Database\Seeders\Address\CountrySeeder;
use Database\Seeders\Address\StateSeeder;
use Database\Seeders\HR\IndustrySeeder;
use Database\Seeders\Spatie\PermissionSeeder;
use Database\Seeders\Spatie\RoleSeeder;
use Database\Seeders\User\AdminSeeder;
use Database\Seeders\User\DummyUserSeeder;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DummyUserSeeder::class,

            PermissionSeeder::class,
            RoleSeeder::class,

            AdminSeeder::class,

            IndustrySeeder::class,
            CountrySeeder::class,
            StateSeeder::class,
            CitySeeder::class,
        ]);
    }
}
