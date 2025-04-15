<?php

namespace Database\Seeders\User;

use App\Enums\Role\UserHasRole;
use App\Enums\User\UserType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::find(1); // Dummy User wird erstellt damit die Role beim seeden die User ID 1 hat

        // Super Admin überschreibt Dummy User
        $adminUser->update([
            'name' => 'Super Admin',
            'created_by' => null,
            'company_id' => null,
            'user_type' => UserType::Admin,
            'email' => 'kina98@gmx.ch',
            'email_verified_at' => now(),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => Str::random(10),
            'password' => Hash::make('Navlak1982'),
        ]);
        $adminUser->assignRole(UserHasRole::SuperAdmin);

        User::factory(2)->create([
            'user_type' => UserType::Admin,
            'created_by' => 1,
            'company_id' => null,
        ])->each(function ($user) {
            $user->syncRoles([UserHasRole::Support]);
        });

        User::factory(2)->create([
            'user_type' => UserType::Admin,
            'created_by' => 1,
            'company_id' => null,
        ])->each(function ($user) {
            $user->syncRoles([UserHasRole::Sales]);
        });

        User::factory(2)->create([
            'user_type' => UserType::Admin,
            'created_by' => 1,
            'company_id' => null,
        ])->each(function ($user) {
            $user->syncRoles([UserHasRole::Marketing]);
        });
    }
}
