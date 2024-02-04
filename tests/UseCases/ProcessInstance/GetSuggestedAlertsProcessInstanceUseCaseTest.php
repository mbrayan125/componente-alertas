<?php

namespace Tests;

use App\UseCases\Models\Contracts\CreateProcessInstanceUseCaseInterface;
use App\UseCases\ProcessInstance\Contracts\GetSuggestedAlertsProcessInstanceUseCaseInterface;
use Tests\TestCase;

class GetSuggestedAlertsProcessInstanceUseCaseTest extends TestCase
{
    /**
     * Test case for the process_sequence_simple method.
     *
     * This test case verifies the behavior of the GetSuggestedAlertsProcessInstanceUseCase
     * when processing a simple process sequence. It creates a process instance, updates
     * its next element, and checks the suggested alerts at each step.
     *
     * @return void
     */
    public function test_process_sequence_simple()
    {
        $processInfo = $this->createSequenceProcess([
            'name_verb'            => 'agregar',
            'name_subject'         => 'usuario',
            'name_complement'      => 'a grupo'
        ]);
        $getSuggestedAlertsProcess = app()->make(GetSuggestedAlertsProcessInstanceUseCaseInterface::class);
        $createUpdateProcessInstance = app()->make(CreateProcessInstanceUseCaseInterface::class);

        $process = $processInfo['process'];
        $processInstance = $createUpdateProcessInstance([
            'target_system_id' => $process->targetSystem->id,
            'process_id'       => $process->id
        ]);

        $processInstance = $createUpdateProcessInstance([
            'next_element_id' => $processInfo['processStart']->bpmn_id,
        ], $processInstance);

        $suggestedAlerts = $getSuggestedAlertsProcess($processInstance);
        $this->assertEmpty($suggestedAlerts);

        $processInstance = $createUpdateProcessInstance([
            'next_element_id' => $processInfo['middleActivity']->bpmn_id,
        ], $processInstance);
        $suggestedAlerts = $getSuggestedAlertsProcess($processInstance);
        $this->assertEmpty($suggestedAlerts);

        $processInstance = $createUpdateProcessInstance([
            'next_element_id' => $processInfo['processEnd']->bpmn_id,
        ], $processInstance);
        $suggestedAlerts = $getSuggestedAlertsProcess($processInstance);
        $this->assertEquals($suggestedAlerts, [
            [
                'process_instance_history_id' => 1,
                'type'                        => "success",
                'visual_representation'       => "message-box",
                'color'                       => "green",
                'message'                     => "Usuario agregado a grupo",
                'icon'                        => "fa-check-circle",
                'title'                       => "Terminado",
                'alert_moment'                => "transition-out",
                'actions'                     => []
            ]
        ]);
    }

    /**
     * Test case for the process_sequence_risky method.
     *
     * This test case verifies the behavior of the process_sequence_risky method in the GetSuggestedAlertsProcessInstanceUseCaseTest class.
     * It tests the suggested alerts generated during the execution of a risky process sequence.
     */
    public function test_process_sequence_risky()
    {
        $processInfo = $this->createSequenceProcess([
            'name_verb'            => 'eliminar',
            'name_subject'         => 'boleto',
            'name_complement'      => 'del juego'
        ], true);
        $getSuggestedAlertsProcess = app()->make(GetSuggestedAlertsProcessInstanceUseCaseInterface::class);
        $createUpdateProcessInstance = app()->make(CreateProcessInstanceUseCaseInterface::class);

        $process = $processInfo['process'];
        $processInstance = $createUpdateProcessInstance([
            'target_system_id' => $process->targetSystem->id,
            'process_id'       => $process->id 
        ]);

        $processInstance = $createUpdateProcessInstance([
            'next_element_id' => $processInfo['processStart']->bpmn_id,
        ], $processInstance);

        $suggestedAlerts = $getSuggestedAlertsProcess($processInstance);
        $this->assertEmpty($suggestedAlerts);

        $processInstance = $createUpdateProcessInstance([
            'next_element_id' => $processInfo['middleActivity']->bpmn_id,
        ], $processInstance);
        $suggestedAlerts = $getSuggestedAlertsProcess($processInstance);
        $this->assertEquals($suggestedAlerts, [
            [
                'process_instance_history_id' => 1,
                'type'                        => "warning",
                'visual_representation'       => "message-box",
                'color'                       => "orange",
                'message'                     => "Revisa los datos antes de continuar",
                'icon'                        => "fa-exclamation-triangle",
                'title'                       => "¿Estás seguro?",
                'alert_moment'                => "transition-out",
                'actions'                     => [
                    "Continuar",
                    "Cancelar"
                ]
            ]
        ]);

        $processInstance = $createUpdateProcessInstance([
            'next_element_id' => $processInfo['processEnd']->bpmn_id,
        ], $processInstance);
        $suggestedAlerts = $getSuggestedAlertsProcess($processInstance);
        $this->assertEquals($suggestedAlerts, [
            [
                'process_instance_history_id' => 1,
                'type'                        => "success",
                'visual_representation'       => "message-box",
                'color'                       => "green",
                'message'                     => "Boleto eliminado del juego",
                'icon'                        => "fa-check-circle",
                'title'                       => "Terminado",
                'alert_moment'                => "transition-out",
                'actions'                     => []
            ]
        ]);
    }

    /**
     * Test case for the process_sequence_no_idempotent method.
     *
     * This test case verifies the behavior of the GetSuggestedAlertsProcessInstanceUseCase
     * when processing a sequence of activities without idempotency.
     *
     * It creates a sequence process with specific names for the verb, subject, and complement.
     * Then it creates a process instance and updates it to go through different elements of the process.
     * Finally, it asserts the suggested alerts returned by the GetSuggestedAlertsProcessInstanceUseCase
     * for each step of the process.
     */
    public function test_process_sequence_no_idempotent()
    {
        $processInfo = $this->createSequenceProcess([
            'name_verb'            => 'agregar',
            'name_subject'         => 'jugador',
            'name_complement'      => 'a favoritos'
        ], false, false);
        $getSuggestedAlertsProcess = app()->make(GetSuggestedAlertsProcessInstanceUseCaseInterface::class);
        $createUpdateProcessInstance = app()->make(CreateProcessInstanceUseCaseInterface::class);

        $process = $processInfo['process'];
        $processInstance = $createUpdateProcessInstance([
            'target_system_id' => $process->targetSystem->id,
            'process_id'       => $process->id
        ]);

        $processInstance = $createUpdateProcessInstance([
            'next_element_id' => $processInfo['processStart']->bpmn_id,
        ], $processInstance);

        $suggestedAlerts = $getSuggestedAlertsProcess($processInstance);
        $this->assertEmpty($suggestedAlerts);

        $processInstance = $createUpdateProcessInstance([
            'next_element_id' => $processInfo['middleActivity']->bpmn_id,
        ], $processInstance);
        $suggestedAlerts = $getSuggestedAlertsProcess($processInstance);
        $this->assertEquals($suggestedAlerts, [
            [
                'process_instance_history_id' => 1,
                'type'                        => "warning",
                'visual_representation'       => "message-box",
                'color'                       => "orange",
                'message'                     => "Revisa los datos antes de continuar",
                'icon'                        => "fa-exclamation-triangle",
                'title'                       => "¿Estás seguro?",
                'alert_moment'                => "transition-out",
                'actions'                     => [
                    "Continuar",
                    "Cancelar"
                ]
            ]
        ]);

        $processInstance = $createUpdateProcessInstance([
            'next_element_id' => $processInfo['processEnd']->bpmn_id,
        ], $processInstance);
        $suggestedAlerts = $getSuggestedAlertsProcess($processInstance);
        $this->assertEquals($suggestedAlerts, [
            [
                'process_instance_history_id' => 1,
                'type'                        => "success",
                'visual_representation'       => "message-box",
                'color'                       => "green",
                'message'                     => "Jugador agregado a favoritos",
                'icon'                        => "fa-check-circle",
                'title'                       => "Terminado",
                'alert_moment'                => "transition-out",
                'actions'                     => []
            ]
        ]);
    }

    /**
     * Test case for the process_exclusive method.
     *
     * This test case verifies the behavior of the process_exclusive method in the GetSuggestedAlertsProcessInstanceUseCase class.
     * It tests the suggested alerts returned at different stages of the process.
     *
     * @return void
     */
    public function test_process_exclusive()
    {
        $processInfo = $this->createExclusiveProcess([
            'name_verb'            => 'subir',
            'name_subject'         => 'video',
            'name_complement'      => 'a tik tok'
        ], false, false);
        $getSuggestedAlertsProcess = app()->make(GetSuggestedAlertsProcessInstanceUseCaseInterface::class);
        $createUpdateProcessInstance = app()->make(CreateProcessInstanceUseCaseInterface::class);

        $process = $processInfo['process'];
        $processInstance = $createUpdateProcessInstance([
            'target_system_id' => $process->targetSystem->id,
            'process_id'       => $process->id
        ]);

        $processInstance = $createUpdateProcessInstance([
            'next_element_id' => $processInfo['processStart']->bpmn_id,
        ], $processInstance);
        $suggestedAlerts = $getSuggestedAlertsProcess($processInstance);
        $this->assertEmpty($suggestedAlerts);

        $processInstance = $createUpdateProcessInstance([
            'next_element_id' => $processInfo['middleActivity']->bpmn_id,
        ], $processInstance);
        $suggestedAlerts = $getSuggestedAlertsProcess($processInstance);
        $this->assertEmpty($suggestedAlerts);

        $processInstance = $createUpdateProcessInstance([
            'next_element_id' => $processInfo['gatewaySplit']->bpmn_id,
        ], $processInstance);
        $suggestedAlerts = $getSuggestedAlertsProcess($processInstance);
        $this->assertEquals($suggestedAlerts, [
            [
                'process_instance_history_id' => 1,
                'type'                        => "feedforward",
                'visual_representation'       => "message-box",
                'color'                       => "blue",
                'message'                     => "Continuar mediante activity1",
                'icon'                        => "fa-question-circle",
                'title'                       => "Confirmar elección",
                'alert_moment'                => "transition-out",
                'actions'                     => [
                    "Continuar",
                    "Cancelar"
                ],
            ],
            [
                'process_instance_history_id' => 1,
                'type'                        => "feedforward",
                'visual_representation'       => "message-box",
                'color'                       => "blue",
                'message'                     => "Continuar mediante activity2",
                'icon'                        => "fa-question-circle",
                'title'                       => "Confirmar elección",
                'alert_moment'                => "transition-out",
                'actions'                     => [
                    "Continuar",
                    "Cancelar"
                ]
            ]
        ]);

        $processInstance = $createUpdateProcessInstance([
            'next_element_id' => $processInfo['gatewayActivity1']->bpmn_id,
        ], $processInstance);
        $suggestedAlerts = $getSuggestedAlertsProcess($processInstance);
        $this->assertEmpty($suggestedAlerts);

        $processInstance = $createUpdateProcessInstance([
            'next_element_id' => $processInfo['gatewayActivity2']->bpmn_id,
        ], $processInstance);
        $suggestedAlerts = $getSuggestedAlertsProcess($processInstance);
        $this->assertEmpty($suggestedAlerts);

        $processInstance = $createUpdateProcessInstance([
            'next_element_id' => $processInfo['gatewayJoin']->bpmn_id,
        ], $processInstance);
        $suggestedAlerts = $getSuggestedAlertsProcess($processInstance);
        $this->assertEmpty($suggestedAlerts);

        $processInstance = $createUpdateProcessInstance([
            'next_element_id' => $processInfo['processEnd']->bpmn_id,
        ], $processInstance);
        $suggestedAlerts = $getSuggestedAlertsProcess($processInstance);
        $this->assertEquals($suggestedAlerts, [
            [
                'process_instance_history_id' => 1,
                'type'                        => "success",
                'visual_representation'       => "message-box",
                'color'                       => "green",
                'message'                     => "Video subido a tik tok",
                'icon'                        => "fa-check-circle",
                'title'                       => "Terminado",
                'alert_moment'                => "transition-out",
                'actions'                     => []
            ]
        ]);
    }

    /**
     * Test case for the process_parallel method.
     *
     * This test case verifies the behavior of the process_parallel method in the GetSuggestedAlertsProcessInstanceUseCaseTest class.
     * It tests the creation and update of a process instance, as well as the retrieval of suggested alerts at different stages of the process.
     * The expected result is an empty array of suggested alerts until the process reaches the processEnd element, where a specific suggested alert is expected.
     *
     * @return void
     */
    public function test_process_parallel()
    {
        $processInfo = $this->createParallelProcess([
            'name_verb'            => 'iniciar',
            'name_subject'         => 'live stream',
            'name_complement'      => ''
        ], false, false);
        $getSuggestedAlertsProcess = app()->make(GetSuggestedAlertsProcessInstanceUseCaseInterface::class);
        $createUpdateProcessInstance = app()->make(CreateProcessInstanceUseCaseInterface::class);

        $process = $processInfo['process'];
        $processInstance = $createUpdateProcessInstance([
            'target_system_id' => $process->targetSystem->id,
            'process_id'       => $process->id
        ]);

        $processInstance = $createUpdateProcessInstance([
            'next_element_id' => $processInfo['processStart']->bpmn_id,
        ], $processInstance);
        $suggestedAlerts = $getSuggestedAlertsProcess($processInstance);
        $this->assertEmpty($suggestedAlerts);

        $processInstance = $createUpdateProcessInstance([
            'next_element_id' => $processInfo['middleActivity']->bpmn_id,
        ], $processInstance);
        $suggestedAlerts = $getSuggestedAlertsProcess($processInstance);
        $this->assertEmpty($suggestedAlerts);

        $processInstance = $createUpdateProcessInstance([
            'next_element_id' => $processInfo['gatewaySplit']->bpmn_id,
        ], $processInstance);
        $suggestedAlerts = $getSuggestedAlertsProcess($processInstance);
        $this->assertEmpty($suggestedAlerts);

        $processInstance = $createUpdateProcessInstance([
            'next_element_id' => $processInfo['gatewayActivity1']->bpmn_id,
        ], $processInstance);
        $suggestedAlerts = $getSuggestedAlertsProcess($processInstance);
        $this->assertEmpty($suggestedAlerts);

        $processInstance = $createUpdateProcessInstance([
            'next_element_id' => $processInfo['gatewayActivity2']->bpmn_id,
        ], $processInstance);
        $suggestedAlerts = $getSuggestedAlertsProcess($processInstance);
        $this->assertEmpty($suggestedAlerts);

        $processInstance = $createUpdateProcessInstance([
            'next_element_id' => $processInfo['gatewayJoin']->bpmn_id,
        ], $processInstance);
        $suggestedAlerts = $getSuggestedAlertsProcess($processInstance);
        $this->assertEmpty($suggestedAlerts);

        $processInstance = $createUpdateProcessInstance([
            'next_element_id' => $processInfo['processEnd']->bpmn_id,
        ], $processInstance);
        $suggestedAlerts = $getSuggestedAlertsProcess($processInstance);
        $this->assertEquals($suggestedAlerts, [
            [
                'process_instance_history_id' => 1,
                'type'                        => "success",
                'visual_representation'       => "message-box",
                'color'                       => "green",
                'message'                     => "Live stream iniciado ",
                'icon'                        => "fa-check-circle",
                'title'                       => "Terminado",
                'alert_moment'                => "transition-out",
                'actions'                     => []
            ]
        ]);
    }
}