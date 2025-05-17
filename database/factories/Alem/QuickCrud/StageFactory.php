<?php

namespace Database\Factories\Alem\QuickCrud;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class StageFactory extends Factory
{
    protected $model = \App\Models\Alem\QuickCrud\Stage::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
