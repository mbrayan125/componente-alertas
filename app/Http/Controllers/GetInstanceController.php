<?php

namespace App\Http\Controllers;

use App\Exceptions\Model\ModelNotFoundException;
use App\Http\Controllers\Abstracts\AbstractController;
use App\Repositories\Contracts\ProcessInstanceRepositoryInterface;
use App\Repositories\Contracts\TargetSystemRepositoryInterface;
use App\Traits\Controller\RestrictionsValidationTrait;
use App\Traits\Response\ResponseConstantsTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetInstanceController extends AbstractController
{
    use RestrictionsValidationTrait;
    use ResponseConstantsTrait;

    public function __invoke(
        TargetSystemRepositoryInterface $targetSystemRepository,
        ProcessInstanceRepositoryInterface $processInstanceRepository,
        Request $request
    ): JsonResponse {
        // Retrieve the token from the request parameters
        $params = $this->getParamsFromRequest(
            $request,
            [
                'target_system_token' => [
                    self::RESTRICTION_REQUIRED => true
                ],
                'process_instance_token' => [
                    self::RESTRICTION_REQUIRED => true
                ]
            ]
        );
        
        $targetSystem = $targetSystemRepository->findByToken($params['target_system_token']);
        $processInstance = $processInstanceRepository->findByTargetSystemAndInstanceToken($targetSystem, $params['process_instance_token']);
        return $this->jsonSuccessResult(
            self::HTTP_OK,
            sprintf('Datos sobre instancia', $targetSystem->name),
            $processInstance->getPublicMapeableData()
        );
    }
}