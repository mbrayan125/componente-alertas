<?php

namespace Database\Factories;

use App\Models\Process;
use App\Models\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserRoleFactory extends Factory
{
    protected $model = UserRole::class;

    public function definition(): array
    {
    	return [
            'process_id' => ProcessFactory::new()->create(),
            'name'       => $this->faker->unique()->word(),
    	];
    }
}
