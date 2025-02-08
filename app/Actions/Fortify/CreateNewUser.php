<?php

namespace App\Actions\Fortify;

use App\Enums\Company\CompanyRegistrationType;
use App\Enums\Company\CompanySize;
use App\Enums\Company\CompanyType;
use App\Enums\Role\UserHasRole;
use App\Enums\User\UserType;
use App\Models\HR\Company;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Create a newly registered user.
     *
     * @param array $input
     * @return User
     */
    public function create(array $input): User
    {

//        dd($input);
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'company_type' => ['required', Rule::enum(CompanyType::class)],
            'company_size' => ['required', Rule::enum(CompanySize::class)],
            'industry_id' => ['required', 'exists:industries,id'],


            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ], [
            'name.required' => 'Der Name ist erforderlich.',
            'company_name.required' => 'Der Firmenname ist erforderlich.',
            'email.required' => 'Die E-Mail-Adresse ist erforderlich.',
            'email.unique' => 'Diese E-Mail-Adresse ist bereits registriert.',
            'industry_id.required' => 'Die Unternehmensbranche ist erforderlich.',
            'company_type.required' => 'Der Unternehmenstyp ist erforderlich.',
            'company_size.required' => 'Die Unternehmensgröße ist erforderlich.',
        ])->validate();

        return DB::transaction(function () use ($input) {
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);

            /**
             * Erstelle eine neue Firma und weise dem Benutzer die company_id zu
             */
            $company = Company::create([
                'company_name' => $input['company_name'],
                'owner_id' => $user->id,
                'created_by' => $user->id,
                'company_type' => $input['company_type'],
                'company_size' => $input['company_size'],
                'registration_type' => CompanyRegistrationType::SELF_REGISTERED,
                'industry_id' => $input['industry_id'],
            ]);

            /**
             * Aktualisiere den Benutzer mit der company_id und dem user_type
             */
            $user->update([
                'company_id' => $company->id,
                'user_type' => UserType::Owner, // Das brauche ich nur um die User besser und schneller zu filtern ohne Join etc./Vielleicht kann ich es auch weglassen
                'created_by' => $user->id,
            ]);

            /**
             * Füge dem Benutzer die Rolle "owner" hinzu
             */
            $user->assignRole(UserHasRole::Owner);

            /**
             * Erstelle ein Team für den Benutzer
             */
            $this->createTeam($user, $company->id);

            return $user;
        });
    }

    /**
     * Create a personal team for the user.
     *
     * @param User $user
     * @param int $companyId
     * @return void
     */
    protected function createTeam(User $user, int $companyId): void
    {
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->name, 2)[0] . "'s Team",
            'personal_team' => true,
            'company_id' => $companyId,
        ]));
    }
}
