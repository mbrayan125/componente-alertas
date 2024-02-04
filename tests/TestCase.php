<?php

namespace Tests;

use App\Traits\Process\ElementsTypesConstantsTrait;
use App\Traits\Process\ProcessFlowPatternsConstantsTrait;
use Database\Factories\ProcessElementFactory;
use Database\Factories\ProcessFactory;
use Database\Factories\UserRoleFactory;
use Faker\Factory;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use DatabaseMigrations;
    use ElementsTypesConstantsTrait;
    use ProcessFlowPatternsConstantsTrait;

    protected $faker;

    protected function setUp(): void
    {
        parent::setUp();
        $faker = Factory::create();
        $this->faker = $faker;
    }

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    /**
     * Asserts that an array contains the expected values.
     *
     * @param array $expected The expected values.
     * @param array $actual The actual array to check.
     * 
     * @return void
     */
    protected function assertArrayContains(array $expected, array $actual): void
    {
        foreach ($expected as $key => $value) {
            $this->assertArrayHasKey($key, $actual, sprintf('Error asserting that the key "%s" exists in the array.', $key));
            $this->assertEquals($value, $actual[$key], sprintf('Error asserting that the key "%s" has the value "%s".', $key, $value));
        }
    }

    /**
     * Returns the relative resource path for a given resource path.
     *
     * @param string $resourcePath The resource path.
     * 
     * @return string The relative resource path.
     */
    protected function getResourcePath(string $resourcePath): string
    {
        return '/app/user-alerts-component/tests/Resources/' . $resourcePath;
    }

    /**
     * Creates a sequence process with the necessary elements.
     *
     * @return array An array containing the created process, user role, process start, middle activity, and process end.
     */
    protected function createSequenceProcess(array $names = [], bool $risky = false, bool $idempotent = true): array
    {
        $process = ProcessFactory::new()->create([
            'risky_execution'      => $risky,
            'idempotent_execution' => $idempotent,
            'name_subject'         => $names['name_subject'] ?? $this->faker->name,
            'name_verb'            => $names['name_verb'] ?? $this->faker->name,
            'name_complement'      => $names['name_complement'] ?? $this->faker->name,
        ]);
        $userRole = UserRoleFactory::new()->create([
            'process_id' => $process->id
        ]);
        $processStart = ProcessElementFactory::new()->create([
            'process_id'   => $process->id,
            'user_role_id' => $userRole->id,
            'type'         => self::EVENT,
            'subtype'      => 'startEvent',
        ]);
        $middleActivity = ProcessElementFactory::new()->create([
            'process_id'   => $process->id,
            'user_role_id' => $userRole->id,
            'type'         => self::ACTIVITY,
            'subtype'      => 'task',
        ]);
        $processEnd = ProcessElementFactory::new()->create([
            'process_id'   => $process->id,
            'user_role_id' => $userRole->id,
            'type'         => self::EVENT,
            'subtype'      => 'endEvent',
        ]);

        $processStart->addOutgoing($middleActivity);
        $middleActivity->addIncoming($processStart);
        $middleActivity->addOutgoing($processEnd);
        $processEnd->addIncoming($middleActivity);

        return [
            'process'        => $process,
            'userRole'       => $userRole,
            'processStart'   => $processStart,
            'middleActivity' => $middleActivity,
            'processEnd'     => $processEnd,
        ];
    }

    /**
     * Creates an exclusive process with various process elements.
     *
     * @return array An array containing the created process elements.
     */
    protected function createExclusiveProcess(array $names = []): array
    {
        $process = ProcessFactory::new()->create([
            'name_subject'    => $names['name_subject'] ?? $this->faker->name,
            'name_verb'       => $names['name_verb'] ?? $this->faker->name,
            'name_complement' => $names['name_complement'] ?? $this->faker->name,
        ]);
        $userRole = UserRoleFactory::new()->create([
            'process_id' => $process->id,
        ]);

        $processStart = ProcessElementFactory::new()->create([
            'process_id'   => $process->id,
            'user_role_id' => $userRole->id,
            'type'         => self::EVENT,
            'subtype'      => 'startEvent',
        ]);
        $middleActivity = ProcessElementFactory::new()->create([
            'process_id'   => $process->id,
            'user_role_id' => $userRole->id,
            'type'         => self::ACTIVITY,
            'subtype'      => 'task',
        ]);
        $gatewayActivity1 = ProcessElementFactory::new()->create([
            'process_id'   => $process->id,
            'user_role_id' => $userRole->id,
            'type'         => self::ACTIVITY,
            'subtype'      => 'task',
        ]);
        $gatewayActivity2 = ProcessElementFactory::new()->create([
            'process_id'   => $process->id,
            'user_role_id' => $userRole->id,
            'type'         => self::ACTIVITY,
            'subtype'      => 'task',
        ]);
        $gatewaySplit = ProcessElementFactory::new()->create([
            'process_id'   => $process->id,
            'user_role_id' => $userRole->id,
            'type'         => self::GATEWAY,
            'subtype'      => 'exclusiveGateway',
        ]);
        $gatewayJoin = ProcessElementFactory::new()->create([
            'process_id'   => $process->id,
            'user_role_id' => $userRole->id,
            'type'         => self::GATEWAY,
            'subtype'      => 'exclusiveGateway',
        ]);
        $processEnd = ProcessElementFactory::new()->create([
            'process_id'   => $process->id,
            'user_role_id' => $userRole->id,
            'type'         => self::EVENT,
            'subtype'      => 'endEvent',
        ]);

        $processStart->addOutgoing($middleActivity);
        $middleActivity->addIncoming($processStart);

        $middleActivity->addOutgoing($gatewaySplit);
        $gatewaySplit->addIncoming($middleActivity);

        $gatewaySplit->addOutgoing($gatewayActivity1, 'activity1');
        $gatewaySplit->addOutgoing($gatewayActivity2, 'activity2');
        $gatewayActivity1->addIncoming($gatewaySplit, 'activity1');
        $gatewayActivity2->addIncoming($gatewaySplit, 'activity2');

        $gatewayActivity1->addOutgoing($gatewayJoin);
        $gatewayActivity2->addOutgoing($gatewayJoin);
        $gatewayJoin->addIncoming($gatewayActivity1);
        $gatewayJoin->addIncoming($gatewayActivity2);

        $gatewayJoin->addOutgoing($processEnd);
        $processEnd->addIncoming($gatewayJoin);

        return [
            'process'          => $process,
            'userRole'         => $userRole,
            'processStart'     => $processStart,
            'middleActivity'   => $middleActivity,
            'gatewayActivity1' => $gatewayActivity1,
            'gatewayActivity2' => $gatewayActivity2,
            'gatewaySplit'     => $gatewaySplit,
            'gatewayJoin'      => $gatewayJoin,
            'processEnd'       => $processEnd,
        ];
    }

    /**
     * Creates an parallel process with various process elements.
     *
     * @return array An array containing the created process elements.
     */
    protected function createParallelProcess(array $names = []): array
    {
        $process = ProcessFactory::new()->create([
            'name_subject'    => $names['name_subject'] ?? $this->faker->name,
            'name_verb'       => $names['name_verb'] ?? $this->faker->name,
            'name_complement' => $names['name_complement'] ?? $this->faker->name,
        ]);
        $userRole = UserRoleFactory::new()->create([
            'process_id' => $process->id,
        ]);

        $processStart = ProcessElementFactory::new()->create([
            'process_id'   => $process->id,
            'user_role_id' => $userRole->id,
            'type'         => self::EVENT,
            'subtype'      => 'startEvent',
        ]);
        $middleActivity = ProcessElementFactory::new()->create([
            'process_id'   => $process->id,
            'user_role_id' => $userRole->id,
            'type'         => self::ACTIVITY,
            'subtype'      => 'task',
        ]);
        $gatewayActivity1 = ProcessElementFactory::new()->create([
            'process_id'   => $process->id,
            'user_role_id' => $userRole->id,
            'type'         => self::ACTIVITY,
            'subtype'      => 'task',
        ]);
        $gatewayActivity2 = ProcessElementFactory::new()->create([
            'process_id'   => $process->id,
            'user_role_id' => $userRole->id,
            'type'         => self::ACTIVITY,
            'subtype'      => 'task',
        ]);
        $gatewaySplit = ProcessElementFactory::new()->create([
            'process_id'   => $process->id,
            'user_role_id' => $userRole->id,
            'type'         => self::GATEWAY,
            'subtype'      => 'parallelGateway',
        ]);
        $gatewayJoin = ProcessElementFactory::new()->create([
            'process_id'   => $process->id,
            'user_role_id' => $userRole->id,
            'type'         => self::GATEWAY,
            'subtype'      => 'parallelGateway',
        ]);
        $processEnd = ProcessElementFactory::new()->create([
            'process_id'   => $process->id,
            'user_role_id' => $userRole->id,
            'type'         => self::EVENT,
            'subtype'      => 'endEvent',
        ]);

        $processStart->addOutgoing($middleActivity);
        $middleActivity->addIncoming($processStart);

        $middleActivity->addOutgoing($gatewaySplit);
        $gatewaySplit->addIncoming($middleActivity);

        $gatewaySplit->addOutgoing($gatewayActivity1);
        $gatewaySplit->addOutgoing($gatewayActivity2);
        $gatewayActivity1->addIncoming($gatewaySplit);
        $gatewayActivity2->addIncoming($gatewaySplit);

        $gatewayActivity1->addOutgoing($gatewayJoin);
        $gatewayActivity2->addOutgoing($gatewayJoin);
        $gatewayJoin->addIncoming($gatewayActivity1);
        $gatewayJoin->addIncoming($gatewayActivity2);

        $gatewayJoin->addOutgoing($processEnd);
        $processEnd->addIncoming($gatewayJoin);

        return [
            'process'          => $process,
            'userRole'         => $userRole,
            'processStart'     => $processStart,
            'middleActivity'   => $middleActivity,
            'gatewayActivity1' => $gatewayActivity1,
            'gatewayActivity2' => $gatewayActivity2,
            'gatewaySplit'     => $gatewaySplit,
            'gatewayJoin'      => $gatewayJoin,
            'processEnd'       => $processEnd,
        ];
    }
}
