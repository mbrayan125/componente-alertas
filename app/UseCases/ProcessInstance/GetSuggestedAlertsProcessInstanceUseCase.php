<?php

namespace App\UseCases\ProcessInstance;

use App\Models\ProcessInstance;
use App\Traits\Process\ProcessFlowPatternsConstantsTrait;
use App\Traits\Process\ProcessStatusConstantsTrait;
use App\Traits\ProcessElement\ProcessElementAssertsTrait;
use App\Traits\UserAlert\UserAlertConstantsTrait;
use App\UseCases\Generic\Contracts\VerbFromInfinitiveToParticipleUseCaseInterface;
use App\UseCases\Models\Contracts\CreateUserAlertUseCaseInterface;
use App\UseCases\Process\Contracts\CheckFlowPatternBpmnProcessUseCaseInterface;
use App\UseCases\ProcessInstance\Contracts\GetCurrentStatusPointProcessInstanceUseCaseInterface;
use App\UseCases\ProcessInstance\Contracts\GetSuggestedAlertsProcessInstanceUseCaseInterface;
use DomainException;

class GetSuggestedAlertsProcessInstanceUseCase implements GetSuggestedAlertsProcessInstanceUseCaseInterface
{
    use ProcessFlowPatternsConstantsTrait;
    use ProcessStatusConstantsTrait;
    use UserAlertConstantsTrait;
    use ProcessElementAssertsTrait;

    public function __construct(
        private readonly CreateUserAlertUseCaseInterface $createUserAlertUseCaseInterface,
        private readonly CheckFlowPatternBpmnProcessUseCaseInterface $checkFlowPatternBpmnProcessUseCase,
        private readonly GetCurrentStatusPointProcessInstanceUseCaseInterface $getCurrentStatusPointProcessInstanceUseCase,
        private readonly VerbFromInfinitiveToParticipleUseCaseInterface $verbFromInfinitiveToParticipleUseCase
        
    ) { }

    public function __invoke(ProcessInstance $processInstance): array
    {
        $flowPatterns = ($this->checkFlowPatternBpmnProcessUseCase)($processInstance->process);
        $currentStatus = ($this->getCurrentStatusPointProcessInstanceUseCase)($processInstance, $flowPatterns);
        if ($currentStatus === self::PROCESS_END) {
            $data = $this->createAlertsForPattern1($processInstance);
        }

        if (in_array(self::SEQUENCE_PATTERN, $flowPatterns) && $currentStatus === self::CENTRAL_ACTIVITY) {
            $data = $this->createAlertsForPattern2($processInstance);
        }

        if (in_array(self::PARALLEL_SPLIT_PATTERN, $flowPatterns) && $currentStatus === self::SPLIT_GATEWAY) {
            $data = $this->createAlertsForPattern3($processInstance);
        }

        if (in_array(self::EXCLUSIVE_SPLIT_PATTERN, $flowPatterns) && $currentStatus === self::SPLIT_GATEWAY) {
            $data = $this->createAlertsForPattern4($processInstance);
        }

        return $data ?? [];
    }

    private function createAlertsForPattern1(ProcessInstance $processInstance): array
    {
        $process = $processInstance->process;
        $message = sprintf('%s %s %s', 
            $process->name_subject, 
            ($this->verbFromInfinitiveToParticipleUseCase)($process->name_verb), 
            $process->name_complement
        );
        $alert = [
            'process_instance_history_id' => $processInstance->id,
            'type'                        => self::ALERT_TYPE_SUCCESS,
            'visual_representation'       => self::ALERT_VISUAL_REPRESENTATION_MESSAGE_BOX,
            'color'                       => self::ALERT_COLOR_GREEN,
            'message'                     => ucfirst($message),
            'icon'                        => self::ALERT_ICON_CHECK_CIRCLE,
            'title'                       => 'Terminado',
            'alert_moment'                => self::ALERT_MOMENT_TRANSITION_OUT,
            'actions'                     => []
        ];
        ($this->createUserAlertUseCaseInterface)($alert);
        
        return [$alert];
    }

    private function createAlertsForPattern2(ProcessInstance $processInstance): array
    {
        $process = $processInstance->process;
        if ($process->idempotent_execution && !$process->risky_execution) {
            return [];
        }

        $alert = [
            'process_instance_history_id' => $processInstance->id,
            'type'                        => self::ALERT_TYPE_WARNING,
            'visual_representation'       => self::ALERT_VISUAL_REPRESENTATION_MESSAGE_BOX,
            'color'                       => self::ALERT_COLOR_ORANGE,
            'message'                     => 'Revisa los datos antes de continuar',
            'icon'                        => self::ALERT_ICON_EXCLAMATION_TRIANGLE,
            'title'                       => '¿Estás seguro?',
            'alert_moment'                => self::ALERT_MOMENT_TRANSITION_OUT,
            'actions'                     => [ 'Continuar', 'Cancelar' ]
        ];
        ($this->createUserAlertUseCaseInterface)($alert);

        return [$alert];
    }

    private function createAlertsForPattern3(ProcessInstance $processInstance): array
    {
        $currentProcessHistory = $processInstance->currentHistory;
        $currentElement = $currentProcessHistory->processElement;
        $currentUserRole = $currentElement->userRole;

        $reviewedElement = null;
        foreach ($currentElement->outgoingElements as $outgoingElement) {
            if ($this->isActivity($outgoingElement) && $outgoingElement->userRole->id !== $currentUserRole->id) {
                $reviewedElement = $outgoingElement;
                break;
            }
        }

        if (!$reviewedElement) {
            return [];
        }

        $selectedActivitiesNames = [];
        $pengingRolesNames = [];
        $preventInfiniteLoop = 100;
        while (true) {
            if ($preventInfiniteLoop-- === 0) {
                throw new DomainException('Se ha detectado un bucle infinito');
            }
            if (array_key_exists($reviewedElement->id, $selectedActivitiesNames)) {
                break;
            }
            if ($this->isJoinGateway($reviewedElement)) {
                break;
            }
            if ($this->isEndEvent($reviewedElement)) {
                break;
            }
            if ($this->isActivity($reviewedElement) && $reviewedElement->userRole->id !== $currentUserRole->id) {
                $selectedActivitiesNames[$reviewedElement->id] = $reviewedElement->name;
                if (!in_array($reviewedElement->userRole->name, $pengingRolesNames)) {
                    $pengingRolesNames[] = $reviewedElement->userRole->name;
                }
                $reviewedElement = $reviewedElement->nextElement();
            }
        }

        if (count($selectedActivitiesNames) === 0) {
            return [];
        }

        $message = sprintf('El proceso continuará cuando se completen las siguientes actividades: %s por parte de %s', 
            implode(', ', $selectedActivitiesNames),
            implode(', ', $pengingRolesNames)
        );

        $alert = [
            'process_instance_history_id' => $processInstance->id,
            'type'                        => self::ALERT_TYPE_FEEDFORWARD,
            'visual_representation'       => self::ALERT_VISUAL_REPRESENTATION_MESSAGE_BOX,
            'color'                       => '',
            'message'                     => $message,
            'icon'                        => self::ALERT_ICON_SAND_CLOCK,
            'title'                       => 'En espera',
            'alert_moment'                => self::ALERT_MOMENT_TRANSITION_OUT,
            'actions'                     => []
        ];
        ($this->createUserAlertUseCaseInterface)($alert);

        return [$alert];
    }

    private function createAlertsForPattern4(ProcessInstance $processInstance): array
    {
        $currentProcessHistory = $processInstance->currentHistory;
        $currentElement = $currentProcessHistory->processElement;

        $alerts = [];
        foreach ($currentElement->outgoingElements as $outgoingElement) {
            $gatewayName = $outgoingElement->pivot->name;

            $alert = [
                'process_instance_history_id' => $processInstance->id,
                'type'                        => self::ALERT_TYPE_FEEDFORWARD,
                'visual_representation'       => self::ALERT_VISUAL_REPRESENTATION_MESSAGE_BOX,
                'color'                       => self::ALERT_COLOR_BLUE,
                'message'                     => 'Continuar mediante ' . $gatewayName,
                'icon'                        => self::ALERT_ICON_QUESTION_CIRCLE,
                'title'                       => 'Confirmar elección',
                'alert_moment'                => self::ALERT_MOMENT_TRANSITION_OUT,
                'actions'                     => [ 'Continuar', 'Cancelar' ]
            ];
            ($this->createUserAlertUseCaseInterface)($alert);
            $alerts[] = $alert;

        }

        return $alerts;
    }
}