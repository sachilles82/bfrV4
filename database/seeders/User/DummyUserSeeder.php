<?php

namespace Database\Seeders\User;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyUserSeeder extends Seeder
{
    /**
     * Dummy User wird erstellt damit die Role beim seeden die User ID 1 hat
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Wir durch Super Admin ersetzt im AdminSeeder',
            'email' => 'system@example.com',
            'password' => Hash::make('secret'),
            'created_at' => now(),
            'updated_at' => now(),
            'current_team_id' => null,       // current_team_id statt team_id
            'company_id' => null,       // optional, falls erforderlich
            'created_by' => null,       // optional, falls erforderlich
            'slug' => Str::slug('Wir durch Super Admin ersetzt im AdminSeeder'), // Slug erzeugen
        ]);
    }
}
