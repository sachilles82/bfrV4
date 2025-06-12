<?php

namespace Database\Seeders;

use Database\Seeders\Address\CitySeeder;
use Database\Seeders\Address\CountrySeeder;
use Database\Seeders\Address\StateSeeder;
use Database\Seeders\Alem\Company\IndustrySeeder;
use Database\Seeders\Spatie\PermissionSeeder;
use Database\Seeders\Spatie\RoleSeeder;
use Database\Seeders\User\AdminSeeder;
use Database\Seeders\User\DummyUserSeeder;
use Database\Seeders\User\TestDataSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Cache leeren VOR dem Seeding
        $this->clearAllCaches();

        $this->call([
            DummyUserSeeder::class,

            PermissionSeeder::class,
            RoleSeeder::class,

            AdminSeeder::class,

            IndustrySeeder::class,
            CountrySeeder::class,
            StateSeeder::class,
            CitySeeder::class,

            TestDataSeeder::class,
        ]);
    }

    /**
     * Leere alle Caches
     */
    protected function clearAllCaches(): void
    {
        $this->command->info('🧹 Leere alle Caches...');

        // Führe optimize:clear aus
        Artisan::call('optimize:clear');

        // Oder einzelne Cache-Befehle:
        // Artisan::call('cache:clear');
        // Artisan::call('config:clear');
        // Artisan::call('route:clear');
        // Artisan::call('view:clear');
        // Artisan::call('event:clear');

        $this->command->info('✅ Caches wurden geleert!');
        $this->command->newLine();
    }
}
