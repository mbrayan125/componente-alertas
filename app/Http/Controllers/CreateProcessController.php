<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Abstracts\AbstractController;
use App\Repositories\Contracts\ProcessRepositoryInterface;
use App\Repositories\Contracts\TargetSystemRepositoryInterface;
use App\Traits\Controller\RestrictionsValidationTrait;
use App\Traits\Response\ResponseConstantsTrait;
use App\UseCases\Models\Contracts\CreateProcessUseCaseInterface;
use Illuminate\Http\Request;

class CreateProcessController extends AbstractController
{
    use RestrictionsValidationTrait;
    use ResponseConstantsTrait;

    /**
     * Creates a new process.
     *
     * @param ProcessRepositoryInterface $processRepository The process repository.
     * @param TargetSystemRepositoryInterface $targetSystemRepository The target system repository.
     * @param CreateProcessUseCaseInterface $createProcessUseCase The create process use case.
     * @param Request $request The request object.
     *
     * @return JsonResponse The JSON response.
     */
    public function __invoke(
        ProcessRepositoryInterface $processRepository,
        TargetSystemRepositoryInterface $targetSystemRepository,
        CreateProcessUseCaseInterface $createProcessUseCase,
        Request $request
    ) {
        $creationRequestParams = $this->getParamsFromRequest(
            $request, 
            [
                'process_name' => [
                    self::RESTRICTION_REQUIRED => true,
                    self::PARAM_TYPE_ARRAY     => ['subject', 'verb', 'complement']
                ],
                'target_system_token' => [
                    self::RESTRICTION_REQUIRED => true
                ],
                'process_token' => [
                    self::RESTRICTION_REQUIRED => false
                ],
                'risky_execution' => [
                    self::RESTRICTION_REQUIRED => true,
                    self::PARAM_TYPE_BOOL      => true
                ],
                'idempotent_execution' => [
                    self::RESTRICTION_REQUIRED => true,
                    self::PARAM_TYPE_BOOL      => true
                ],
                'process_bpmn' => [
                    self::RESTRICTION_REQUIRED => true,
                    self::PARAM_TYPE_FILE      => true
                ]
            ]
        );

        $currentProcess = null;
        $targetSystem = $targetSystemRepository->findByToken($creationRequestParams['target_system_token']);
        if (!is_null($processToken = $creationRequestParams['process_token'])) {
            $currentProcess = $processRepository->findByTargetSystemAndToken($targetSystem, $processToken);
        }

        $createdProcess = $createProcessUseCase([
            'name_subject'         => $creationRequestParams['process_name']['subject'],
            'name_verb'            => $creationRequestParams['process_name']['verb'],
            'name_complement'      => $creationRequestParams['process_name']['complement'],
            'bpmn_filepath'        => $creationRequestParams['process_bpmn'],
            'target_system_id'     => $targetSystem->id,
            'version'              => $currentProcess ? ($currentProcess->version + 1) : 1,
            'risky_execution'      => $creationRequestParams['risky_execution'],
            'idempotent_execution' => $creationRequestParams['idempotent_execution'],
        ]);

        return $this->jsonSuccessResult(self::HTTP_CREATED, 'Se ha creado el proceso correctamente', [
            'process_token' => $createdProcess->token,
            'process_name'  => $createdProcess->getFullName(),
            'created_at'    => $createdProcess->created_at
        ]);
    }
}
