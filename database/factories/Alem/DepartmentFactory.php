<?php

namespace Database\Factories\Alem;

use App\Enums\Model\ModelStatus;
use App\Models\Alem\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Department::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->companySuffix . ' Department', // Beispiel
            'description' => $this->faker->optional()->sentence,
            // 'company_id' => \App\Models\Alem\Company::factory(), // Falls benötigt und CompanyFactory existiert
            // 'team_id' => \App\Models\Team::factory(), // Falls benötigt und TeamFactory existiert
            // 'created_by' => \App\Models\User::factory(), // Falls benötigt und UserFactory existiert
            'model_status' => ModelStatus::ACTIVE, // Standardstatus setzen
            // Füge hier Standardwerte für alle erforderlichen Felder hinzu
        ];
    }
}
