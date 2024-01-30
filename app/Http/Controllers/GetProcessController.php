<?php

namespace App\Http\Controllers;

use App\Exceptions\Model\ModelNotFoundException;
use App\Http\Controllers\Abstracts\AbstractController;
use App\Repositories\Contracts\ProcessRepositoryInterface;
use App\Repositories\Contracts\TargetSystemRepositoryInterface;
use App\Traits\Controller\RestrictionsValidationTrait;
use App\Traits\Response\ResponseConstantsTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetProcessController extends AbstractController
{
    use RestrictionsValidationTrait;
    use ResponseConstantsTrait;

    /**
     * Retrieves information about a target system based on the provided token.
     *
     * @param TargetSystemRepositoryInterface $targetSystemRepository The repository for target systems.
     * @param Request $request The HTTP request object.
     * 
     * @return JsonResponse The JSON response containing the target system information.
     * @throws ModelNotFoundException If the target system with the given token is not found.
     */
    public function __invoke(
        TargetSystemRepositoryInterface $targetSystemRepository,
        ProcessRepositoryInterface $processRepository,
        Request $request
    ): JsonResponse {
        // Retrieve the token from the request parameters
        $params = $this->getParamsFromRequest(
            $request,
            [
                'target_system_token' => [
                    self::RESTRICTION_REQUIRED => true
                ],
                'process_token' => [
                    self::RESTRICTION_REQUIRED => true
                ]
            ]
        );

        $targetSystem = $targetSystemRepository->findByToken($params['target_system_token']);
        $process = $processRepository->findByTargetSystemAndToken($targetSystem, $params['process_token']);
        return $this->jsonSuccessResult(
            self::HTTP_OK,
            sprintf('Datos sobre proceso %s', $process->getFullName()),
            $process->getPublicMapeableData()
        );
    }
}