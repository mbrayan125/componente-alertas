<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Abstracts\AbstractController;
use App\UseCases\Models\Contracts\CreateTargetSystemUseCaseInterface;
use Illuminate\Http\Request;

class CreateTargetSystemController extends AbstractController
{
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
            ['name', 'nickname'], 
            ['process_path']
        );
        $targetSystem = $createTargetSystem($creationRequestParams);

        return $this->jsonSuccessResult('Sistema registrado correctamente', [
            'id'       => $targetSystem->id,
            'name'     => $targetSystem->name,
            'nickname' => $targetSystem->nickname,
            'process'  => $targetSystem->process_path
        ]);
    }
}
