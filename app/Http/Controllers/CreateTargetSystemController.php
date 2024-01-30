<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Abstracts\AbstractController;
use App\Traits\Controller\RestrictionsValidationTrait;
use App\Traits\Response\ResponseConstantsTrait;
use App\UseCases\Models\Contracts\CreateTargetSystemUseCaseInterface;
use Illuminate\Http\Request;

class CreateTargetSystemController extends AbstractController
{
    use RestrictionsValidationTrait;
    use ResponseConstantsTrait;

    /**
     * Invokes the CreateTargetSystemController.
     *
     * @param CreateTargetSystemUseCaseInterface $createTargetSystem The use case for creating a target system.
     * @param Request $request The HTTP request object.
     * 
     * @return JsonResponse The JSON response containing the registered target system information.
     */
    public function __invoke(
        CreateTargetSystemUseCaseInterface $createTargetSystem,
        Request $request
    ) {
        $creationRequestParams = $this->getParamsFromRequest(
            $request, 
            [
                'name' => [
                    self::RESTRICTION_REQUIRED => true
                ],
            ]
        );
        $targetSystem = $createTargetSystem($creationRequestParams);

        return $this->jsonSuccessResult(self::HTTP_CREATED, 'Se ha creado el sistema objetivo correctamente', [
            'target_system_token' => $targetSystem->token,
            'created_at'          => $targetSystem->created_at
        ]);
    }
}
