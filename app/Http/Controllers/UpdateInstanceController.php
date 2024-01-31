<?php

namespace App\Http\Controllers;

use App\Exceptions\Model\ModelNotFoundException;
use App\Http\Controllers\Abstracts\AbstractController;
use App\Repositories\Contracts\ProcessInstanceRepositoryInterface;
use App\Repositories\Contracts\TargetSystemRepositoryInterface;
use App\Traits\Controller\RestrictionsValidationTrait;
use App\Traits\Response\ResponseConstantsTrait;
use App\UseCases\Models\Contracts\CreateProcessInstanceUseCaseInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateInstanceController extends AbstractController
{
    use RestrictionsValidationTrait;
    use ResponseConstantsTrait;

    public function __invoke(
        TargetSystemRepositoryInterface $targetSystemRepository,
        ProcessInstanceRepositoryInterface $processInstanceRepository,
        CreateProcessInstanceUseCaseInterface $createProcessInstanceUseCase,
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
                ]
            ]
        );
        
        $targetSystem = $targetSystemRepository->findByToken($params['target_system_token']);
        $processInstance = $processInstanceRepository->findByTargetSystemAndInstanceToken($targetSystem, $params['process_instance_token']);

        if ($nextElementId = $params['next_element_id']) {
            $updatedProcessInstance = $createProcessInstanceUseCase([
                'next_element_id' => $nextElementId,
            ], $processInstance);
        }


        return $this->jsonSuccessResult(
            self::HTTP_OK,
            sprintf('Datos sobre instancia', $targetSystem->name),
            $processInstance->getPublicMapeableData()
        );
    }
}