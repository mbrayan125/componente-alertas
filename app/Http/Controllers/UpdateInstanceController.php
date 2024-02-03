<?php

namespace App\Http\Controllers;

use App\Exceptions\Model\ModelNotFoundException;
use App\Http\Controllers\Abstracts\AbstractController;
use App\Repositories\Contracts\ProcessInstanceRepositoryInterface;
use App\Repositories\Contracts\TargetSystemRepositoryInterface;
use App\Traits\Controller\RestrictionsValidationTrait;
use App\Traits\Response\ResponseConstantsTrait;
use App\UseCases\Models\Contracts\CreateProcessInstanceUseCaseInterface;
use App\UseCases\ProcessInstance\Contracts\GetSuggestedAlertsProcessInstanceUseCaseInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateInstanceController extends AbstractController
{
    use RestrictionsValidationTrait;
    use ResponseConstantsTrait;

    public function __invoke(
        CreateProcessInstanceUseCaseInterface $createProcessInstanceUseCase,
        GetSuggestedAlertsProcessInstanceUseCaseInterface $getSuggestedAlertsProcessInstanceUseCase,
        ProcessInstanceRepositoryInterface $processInstanceRepository,
        TargetSystemRepositoryInterface $targetSystemRepository,
        Request $request
    ): JsonResponse {
        
        $params = $this->getParamsFromRequest(
            $request,
            [
                'target_system_token' => [
                    self::RESTRICTION_REQUIRED => true
                ],
                'process_instance_token' => [
                    self::RESTRICTION_REQUIRED => true
                ],
                'next_element_id' => [
                    self::RESTRICTION_REQUIRED => false
                ],
                'force_generate' => [
                    self::RESTRICTION_REQUIRED => false,
                    self::PARAM_TYPE_BOOL      => true
                ]
            ]
        );
        
        $targetSystem = $targetSystemRepository->findByToken($params['target_system_token']);
        $processInstance = $processInstanceRepository->findByTargetSystemAndInstanceToken($targetSystem, $params['process_instance_token']);
        $currentElementId = $processInstance->currentElement()?->bpmn_id;
        $nextElementId = $params['next_element_id'] ?? null;
        $createAlerts = false;

        if ($nextElementId && $currentElementId !== $nextElementId) {
            $createAlerts = true;
            $processInstance = $createProcessInstanceUseCase([
                'next_element_id' => $nextElementId,
            ], $processInstance);
        }


        $suggestedAlerts = [];
        if ($createAlerts || $params['force_generate'] ?? false) {
            $suggestedAlerts = ($getSuggestedAlertsProcessInstanceUseCase)($processInstance);
        }

        $processData = $processInstance->getPublicMapeableData();
        $processData['suggested_alerts'] = $suggestedAlerts;
        return $this->jsonSuccessResult(
            self::HTTP_OK,
            sprintf('Datos sobre instancia', $targetSystem->name),
            $processData
        );
    }
}