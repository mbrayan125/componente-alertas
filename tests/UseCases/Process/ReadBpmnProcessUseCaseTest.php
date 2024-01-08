<?php

namespace Tests\UseCases\Process;

use App\DataResultObjects\Process\Composed\ReadBpmnUseCaseDRO;
use App\UseCases\Process\Contracts\ReadBpmnProcessUseCaseInterface;
use Tests\TestCase;

class ReadBpmnProcessUseCaseTest extends TestCase
{
    private $useCase;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->useCase = app()->make(ReadBpmnProcessUseCaseInterface::class);
    }

    
    /**
     * Test case to verify that invoking the method with a valid BPMN file returns a success result case 1.
     */
    public function test_invoke_with_valid_bpmn_file_should_return_success_result_case_1()
    {
        $result = ($this->useCase)($this->getResourcePath('Process/valid_process_1.bpmn'));
        $composedResult = $result->data;

        $this->assertTrue($result->success);
        $this->assertEmpty($result->errors);
        $this->assertEmpty($result->warnings);

        $expectedUserRoles = [
            'Usuario 1' => [ 'id' => 'Lane_1y9ntlq' ],
            'Usuario 2' => [ 'id' => 'Lane_1sfxhm2' ],
        ];
        $expectedEvents = [
            'Evento inicio' => [ 
                'id'                => 'StartEvent_1mkovn1',
                'user_role'         => 'Usuario 1',
                'type'              => 'start',
                'outgoing_elements' => [ 'Actividad 1' ]
            ],
            'Evento fin 1'  => [ 
                'id'                => 'Event_0rtnzq4',
                'user_role'         => 'Usuario 1',
                'type'              => 'end',
                'incoming_elements' => [ 'Actividad 2' ]
            ],
            'Evento fin 2'  => [ 
                'id'                => 'Event_0kl9lnl',
                'user_role'         => 'Usuario 2',
                'type'              => 'end',
                'incoming_elements' => [ 'Actividad 4' ]
            ],
            'Evento fin 3'  => [ 
                'id'                => 'Event_0zptuig',
                'user_role'         => 'Usuario 2',
                'type'              => 'end',
                'incoming_elements' => [ 'Opción 2' => 'Gateway 2' ]
            ]
        ];
        $expectedActivities = [
            'Actividad 1' => [ 
                'id'                => 'Activity_12sdyji',
                'user_role'         => 'Usuario 1',
                'incoming_elements' => [ 'Evento inicio' ],
                'outgoing_elements' => [ 'Gateway 1' ]
            ],
            'Actividad 2' => [ 
                'id'                => 'Activity_1tf3qaa',
                'user_role'         => 'Usuario 1',
                'incoming_elements' => [ 'Opcion 1' => 'Gateway 1' ],
                'outgoing_elements' => [ 'Evento fin 1' ]
            ],
            'Actividad 3' => [ 
                'id'                => 'Activity_1rli0xn',
                'user_role'         => 'Usuario 2',
                'incoming_elements' => [ 'Opcion 2' => 'Gateway 1' ],
                'outgoing_elements' => [ 'Gateway 2' ]
            ],
            'Actividad 4' => [ 
                'id'                => 'Activity_0fkngf6',
                'user_role'         => 'Usuario 2',
                'incoming_elements' => [ 'Opción 1' => 'Gateway 2' ],
                'outgoing_elements' => [ 'Evento fin 2' ]
            ]
        ];
        $expectedGateways = [
            'Gateway 1'   => [ 
                'id'                => 'Gateway_188arsl',
                'user_role'         => 'Usuario 1',
                'incoming_elements' => [ 'Actividad 1' ],
                'outgoing_elements' => [ 'Opcion 1' => 'Actividad 2', 'Opcion 2' => 'Actividad 3' ]
            ],
            'Gateway 2'   => [ 
                'id'                => 'Gateway_0bkid1c',
                'user_role'         => 'Usuario 2',
                'incoming_elements' => [ 'Actividad 3' ],
                'outgoing_elements' => [ 'Opción 1' => 'Actividad 4', 'Opción 2' => 'Evento fin 3' ]
            ]
        ];

        $this->assertOverReadedProcess(
            $composedResult, 
            $expectedUserRoles, 
            $expectedEvents, 
            $expectedActivities, 
            $expectedGateways
        );
    }

    /**
     * Test case to verify that invoking the method with a valid BPMN file returns a success result case 2.
     */
    public function test_invoke_with_valid_bpmn_file_should_return_success_result_case_2()
    {
        $result = ($this->useCase)($this->getResourcePath('Process/valid_process_2.bpmn'));
        $composedResult = $result->data;

        $this->assertTrue($result->success);
        $this->assertEmpty($result->errors);
        $this->assertEmpty($result->warnings);

        $expectedUserRoles = [
            'Oogway'   => [ 'id' => 'Lane_112xjbx' ],
            'Shifu'    => [ 'id' => 'Lane_00aaag5' ],
            'Tai Long' => [ 'id' => 'Lane_1xm626k' ],
        ];
        $expectedEvents = [
            'Movie start' => [ 
                'id'                => 'StartEvent_1jljh4d',
                'user_role'         => 'Shifu',
                'type'              => 'start',
                'outgoing_elements' => [ 'Adopt puppy' ]
            ],
            'Movie end 1'  => [ 
                'id'                => 'Event_1j2p7ua',
                'user_role'         => 'Tai Long',
                'type'              => 'end',
                'incoming_elements' => [ 'Become dragon warrior' ]
            ],
            'Movie end 2'  => [ 
                'id'                => 'Event_1q2owev',
                'user_role'         => 'Shifu',
                'type'              => 'end',
                'incoming_elements' => [ 'Search another dragon warrior' ]
            ]
        ];
        $expectedActivities = [
            'Adopt puppy' => [ 
                'id'                => 'Activity_0nvxiwo',
                'user_role'         => 'Shifu',
                'incoming_elements' => [ 'Movie start' ],
                'outgoing_elements' => [ 'Parallel split' ]
            ],
            'Teach all techniques' => [ 
                'id'                => 'Activity_0ijoabm',
                'user_role'         => 'Shifu',
                'incoming_elements' => [ 'Parallel split' ],
                'outgoing_elements' => [ 'Parallel join' ]
            ],
            'Train hardly' => [ 
                'id'                => 'Activity_1c6aadl',
                'user_role'         => 'Tai Long',
                'incoming_elements' => [ 'Parallel split' ],
                'outgoing_elements' => [ 'Parallel join' ]
            ],
            'Become dragon warrior' => [ 
                'id'                => 'Activity_1naavr8',
                'user_role'         => 'Tai Long',
                'incoming_elements' => [ 'Is worthy' => 'Check dragon warrior' ],
                'outgoing_elements' => [ 'Movie end 1' ]
            ],
            'Search another dragon warrior' => [ 
                'id'                => 'Activity_0xz4zs6',
                'user_role'         => 'Shifu',
                'incoming_elements' => [ 'Is not worthy' => 'Check dragon warrior' ],
                'outgoing_elements' => [ 'Movie end 2' ]
            ]
        ];
        $expectedGateways = [
            'Parallel split'   => [ 
                'id'                => 'Gateway_18bgn17',
                'user_role'         => 'Shifu',
                'incoming_elements' => [ 'Adopt puppy' ],
                'outgoing_elements' => [ 'Train hardly', 'Teach all techniques' ]
            ],
            'Parallel join'   => [ 
                'id'                => 'Gateway_0ct7sfa',
                'user_role'         => 'Shifu',
                'incoming_elements' => [ 'Train hardly', 'Teach all techniques' ],
                'outgoing_elements' => [ 'Check dragon warrior' ]
            ],
            'Check dragon warrior'   => [ 
                'id'                => 'Gateway_1cr0fsh',
                'user_role'         => 'Oogway',
                'incoming_elements' => [ 'Parallel join' ],
                'outgoing_elements' => [ 'Is worthy' => 'Become dragon warrior', 'Is not worthy' => 'Search another dragon warrior' ]
            ]
        ];

        $this->assertOverReadedProcess(
            $composedResult, 
            $expectedUserRoles, 
            $expectedEvents, 
            $expectedActivities, 
            $expectedGateways
        );
    }

    /**
     * Asserts that the over-read process matches the expected user roles, activities, and gateways.
     *
     * @param ReadBpmnUseCaseDRO $composedResult The composed result of the read BPMN process use case.
     * @param array $expectedUserRoles The expected user roles.
     * @param array $expectedEvents The expected events.
     * @param array $expectedActivities The expected activities.
     * @param array $expectedGateways The expected gateways.
     * 
     * @return void
     */
    private function assertOverReadedProcess(
        ReadBpmnUseCaseDRO $composedResult, 
        array $expectedUserRoles, 
        array $expectedEvents,
        array $expectedActivities,
        array $expectedGateways
    ) {
        // Get all elements ids
        $allElementsIds = array_column(array_merge($composedResult->events, $composedResult->activities, $composedResult->gateways), 'id', 'name');

        // Test user roles
        $userRolesInfo = array_combine(array_keys($composedResult->lanes), array_map(function ($lane) {
            return [
                'name'     => $lane['name'],
                'elements' => array_column($lane['elements'], 'element_id') 
            ];
        }, $composedResult->lanes));
        $this->assertCount(count($expectedUserRoles), $userRolesInfo);
        foreach ($expectedUserRoles as $expectedUserRoleName => $expectedUSerRoleInfo) {
            $expectedUserRoleId = $expectedUSerRoleInfo['id'];
            $allElementsIds[$expectedUserRoleName] = $expectedUserRoleId;
            $this->assertArrayHasKey($expectedUserRoleId, $userRolesInfo);
            $this->assertEquals($expectedUserRoleName, $userRolesInfo[$expectedUserRoleId]['name']);
        }

        // Test events
        $eventsInfo = array_combine(array_keys($composedResult->events), array_map(function ($event) {
            return [
                'id'                => $event['id'],
                'name'              => $event['name'],
                'type'              => $event['type'],
                'user_role'         => $event['lane'],
                'incoming_elements' => $event['incoming'],
                'outgoing_elements' => $event['outgoing']
            ];
        }, $composedResult->events));
        $this->assertCount(count($expectedEvents), $composedResult->events);
        foreach ($expectedEvents as $expectedEventName => $expectedEventInfo) {
            $expectedId = $expectedEventInfo['id'];
            $allElementsIds[$expectedEventName] = $expectedId;
            $this->assertArrayHasKey($expectedId, $eventsInfo);
            $eventInfo = $eventsInfo[$expectedId];
            $expectedUserRole = $expectedUserRoles[$expectedEventInfo['user_role']]['id'];
            $this->assertEquals($expectedEventName, $eventInfo['name']);
            $this->assertEquals($expectedEventInfo['type'] . 'Event', $eventInfo['type']);
            $this->assertEquals($expectedUserRole, $eventInfo['user_role']);
            $this->assertOverElementConnections(
                $expectedEventInfo['incoming_elements'] ?? [], 
                $expectedEventInfo['outgoing_elements'] ?? [], 
                $eventInfo['incoming_elements'] ?? [],
                $eventInfo['outgoing_elements'] ?? [],
                $allElementsIds
            );
        }

        // Test activities
        $activitiesInfo = array_combine(array_keys($composedResult->activities), array_map(function ($activity) {
            return [
                'id'                => $activity['id'],
                'name'              => $activity['name'],
                'user_role'         => $activity['lane'],
                'incoming_elements' => $activity['incoming'],
                'outgoing_elements' => $activity['outgoing']
            ];
        }, $composedResult->activities));
        $this->assertCount(count($expectedActivities), $composedResult->activities);
        foreach ($expectedActivities as $expectedActivityName => $expectedActivityInfo) {
            $expectedId = $expectedActivityInfo['id'];
            $allElementsIds[$expectedActivityName] = $expectedId;
            $this->assertArrayHasKey($expectedId, $activitiesInfo);
            $activityInfo = $activitiesInfo[$expectedId];
            $expectedUserRole = $expectedUserRoles[$expectedActivityInfo['user_role']]['id'];
            $this->assertEquals($expectedActivityName, $activityInfo['name']);
            $this->assertEquals($expectedUserRole, $activityInfo['user_role']);
            $this->assertOverElementConnections(
                $expectedActivityInfo['incoming_elements'] ?? [], 
                $expectedActivityInfo['outgoing_elements'] ?? [], 
                $activityInfo['incoming_elements'] ?? [],
                $activityInfo['outgoing_elements'] ?? [],
                $allElementsIds
            );
        }

        // Test gateways
        $gatewaysInfo = array_combine(array_keys($composedResult->gateways), array_map(function ($gateway) {
            return [
                'id'                => $gateway['id'],
                'name'              => $gateway['name'],
                'user_role'         => $gateway['lane'],
                'incoming_elements' => $gateway['incoming'],
                'outgoing_elements' => $gateway['outgoing']
            ];
        }, $composedResult->gateways));
        $this->assertCount(count($expectedGateways), $composedResult->gateways);
        foreach ($expectedGateways as $expectedGatewayName => $expectedGatewayInfo) {
            $expectedId = $expectedGatewayInfo['id'];
            $allElementsIds[$expectedGatewayName] = $expectedId;
            $this->assertArrayHasKey($expectedId, $gatewaysInfo);
            $gatewayInfo = $gatewaysInfo[$expectedId];
            $expectedUserRole = $expectedUserRoles[$expectedGatewayInfo['user_role']]['id'];
            $this->assertEquals($expectedGatewayName, $gatewayInfo['name']);
            $this->assertEquals($expectedUserRole, $gatewayInfo['user_role']);
            $this->assertOverElementConnections(
                $expectedGatewayInfo['incoming_elements'] ?? [], 
                $expectedGatewayInfo['outgoing_elements'] ?? [], 
                $gatewayInfo['incoming_elements'] ?? [],
                $gatewayInfo['outgoing_elements'] ?? [],
                $allElementsIds
            );
        }
    }

    /**
     * Asserts the connections over an element in a BPMN process.
     *
     * @param array $expectedIncomingElements The expected incoming elements.
     * @param array $expectedOutgoingElements The expected outgoing elements.
     * @param array $incomingElements The actual incoming elements.
     * @param array $outgoingElements The actual outgoing elements.
     * @param array $allElementsIds The IDs of all elements in the process.
     * 
     * @return void
     */
    private function assertOverElementConnections(
        array $expectedIncomingElements,
        array $expectedOutgoingElements,
        array $incomingElements, 
        array $outgoingElements, 
        array $allElementsIds
    ) {
        foreach ($expectedIncomingElements as $key => $expectedIncomingElementName) {
            $targetSearch = [
                'element_id' => $allElementsIds[$expectedIncomingElementName] ?? null,
                'name'       => is_string($key) ? $key : ''
            ];
            $this->assertContains($targetSearch, $incomingElements);
        }

        foreach ($expectedOutgoingElements as $key => $expectedOutgoingElementName) {
            $targetSearch = [
                'element_id' => $allElementsIds[$expectedOutgoingElementName] ?? null,
                'name'       => is_string($key) ? $key : ''
            ];
            $this->assertContains($targetSearch, $outgoingElements);
        }
    }

    /**
     * Test case to verify that invoking the method with a valid BPMN file returns a success result.
     */
    public function test_invoke_with_file_that_does_not_exist_should_return_failed_result()
    {
        $result = ($this->useCase)($this->getResourcePath('Process/file_that_does_not_exist.bpmn'));
        $this->assertFalse($result->success);
        $this->assertEmpty($result->data);
        $this->assertEmpty($result->warnings);
        $this->assertCount(1, $result->errors);
        $this->assertStringContainsString('El archivo bpmn no existe', $result->errors[0]);
    }

    /**
     * Test case to verify that invoking the method with a file that is not in BPMN format
     * should return a failed result.
     */
    public function test_invoke_with_file_not_bpmn_should_return_failed_result()
    {
        $result = ($this->useCase)($this->getResourcePath('Process/invalid_process_1.txt'));

        $this->assertFalse($result->success);
        $this->assertEmpty($result->data);
        $this->assertEmpty($result->warnings);
        $this->assertCount(1, $result->errors);
        $this->assertStringContainsString('Error en la lectura del proceso BPMN', $result->errors[0]);
    }
}