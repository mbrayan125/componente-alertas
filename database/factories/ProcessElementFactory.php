<?php

namespace Database\Factories;

use App\Models\ProcessElement;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProcessElementFactory extends Factory
{
    protected $model = ProcessElement::class;

    public function definition(): array
    {
    	return [
            'process_id'   => ProcessFactory::new()->create()->id,
            'user_role_id' => UserRoleFactory::new()->create()->id,
            'bpmn_id'      => $this->faker->unique()->word(),
            'name'         => $this->faker->unique()->word(),
            'type'         => $this->faker->unique()->word(),
            'subtype'      => $this->faker->unique()->word(),
    	];
    }
}
