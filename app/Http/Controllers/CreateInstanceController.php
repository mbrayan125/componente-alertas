<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Abstracts\AbstractController;
use App\Repositories\Contracts\ProcessRepositoryInterface;
use App\Repositories\Contracts\TargetSystemRepositoryInterface;
use App\Traits\Controller\RestrictionsValidationTrait;
use App\Traits\Response\ResponseConstantsTrait;
use App\UseCases\Models\Contracts\CreateProcessInstanceUseCaseInterface;
use App\UseCases\Models\Contracts\CreateProcessUseCaseInterface;
use Illuminate\Http\Request;

class CreateInstanceController extends AbstractController
{
    use RestrictionsValidationTrait;
    use ResponseConstantsTrait;
    
    /**
     * Invokes the CreateInstanceController.
     *
     * @param ProcessRepositoryInterface $processRepository The process repository.
     * @param TargetSystemRepositoryInterface $targetSystemRepository The target system repository.
     * @param CreateProcessInstanceUseCaseInterface $createInstanceUseCase The create instance use case.
     * @param Request $request The request object.
     * 
     * @return JsonResponse The JSON response.
     */
    public function __invoke(
        ProcessRepositoryInterface $processRepository,
        TargetSystemRepositoryInterface $targetSystemRepository,
        CreateProcessInstanceUseCaseInterface $createInstanceUseCase,
        Request $request
    ) {
        $creationRequestParams = $this->getParamsFromRequest(
            $request, 
            [
                'target_system_token' => [
                    self::RESTRICTION_REQUIRED => true
                ],
                'process_token' => [
                    self::RESTRICTION_REQUIRED => true
                ],
            ]
        );

        $targetSystem = $targetSystemRepository->findByToken($creationRequestParams['target_system_token']);
        $process = $processRepository->findByTargetSystemAndToken($targetSystem, $creationRequestParams['process_token']);

        $createdInstance = $createInstanceUseCase([
            'target_system_id' => $targetSystem->id,
            'process_id'       => $process->id
        ]);

        return $this->jsonSuccessResult(self::HTTP_CREATED, 'Se ha creado la instancia correctamente', [
            'process_instance_token' => $createdInstance->token,
            'created_at'             => $createdInstance->created_at
        ]);
    }
}
