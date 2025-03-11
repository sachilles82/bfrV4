<?php

namespace Database\Factories\Alem;

use App\Models\Alem\Company;
use App\Models\Alem\Industry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Alem\Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'company_name' => $this->faker->company(),
            'owner_id' => User::factory(),
            'created_by' => User::factory(),
            'industry_id' => Industry::factory(),
            'company_url' => $this->faker->url(),
            'company_size' => $this->faker->randomElement(\App\Enums\Company\CompanySize::cases()),
            'company_type' => $this->faker->randomElement(\App\Enums\Company\CompanyType::cases()),
            'register_number' => $this->faker->bothify('CH-???-#####-#'),
            'email' => $this->faker->companyEmail(),
            'phone_1' => $this->faker->phoneNumber(),
            'phone_2' => $this->faker->optional(0.5)->phoneNumber(),
            'registration_type' => $this->faker->randomElement(\App\Enums\Company\CompanyRegistrationType::cases()),
            'is_active' => true,
        ];
    }
}
