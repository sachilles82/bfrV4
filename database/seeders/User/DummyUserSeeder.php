<?php

namespace Database\Seeders\User;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DummyUserSeeder extends Seeder
{
    /**
     * Dummy User wird erstellt damit die Role beim seeden die User ID 1 hat
     */

    public function run(): void
    {
        DB::table('users')->insert([
            'name'       => 'Wir durch Super Admin ersetzt im AdminSeeder',
            'email'      => 'system@example.com',
            'password'   => Hash::make('secret'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

}
