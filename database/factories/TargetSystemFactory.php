<?php

namespace Database\Factories;

use App\Models\TargetSystem;
use Illuminate\Database\Eloquent\Factories\Factory;

class TargetSystemFactory extends Factory
{
    protected $model = TargetSystem::class;

    public function definition(): array
    {
    	return [
            'name'         => $this->faker->name,
            'token'     => $this->faker->regexify('[a-f0-9]{32}')
    	];
    }
}
