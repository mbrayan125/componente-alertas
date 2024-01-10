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
            'nickname'     => $this->faker->regexify('[A-Za-z0-9]{12}'),
            'process_path' => $this->faker->filePath(),
    	];
    }
}
