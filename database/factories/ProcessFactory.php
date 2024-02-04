<?php

namespace Database\Factories;

use App\Models\Process;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProcessFactory extends Factory
{
    protected $model = Process::class;

    public function definition(): array
    {
    	return [
            'target_system_id'    => TargetSystemFactory::new()->create()->id,
            'name_subject'         => $this->faker->name,
            'name_verb'            => $this->faker->name,
            'name_complement'      => $this->faker->name,
            'token'                => $this->faker->regexify('[a-f0-9]{32}'),
            'bpmn_filepath'        => $this->faker->name,
            'version'              => $this->faker->numberBetween(1, 10),
            'risky_execution'      => $this->faker->boolean(),
            'idempotent_execution' => $this->faker->boolean(),
    	];
    }
}
