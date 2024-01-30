<?php

namespace App\UseCases\Models;

use App\Models\Abstracts\AbstractModel;
use App\Models\ProcessInstance;
use App\Repositories\Contracts\ModelRepositoryInterface;
use App\Repositories\Contracts\ProcessElementRepositoryInterface;
use App\Repositories\Contracts\ProcessInstanceRepositoryInterface;
use App\Traits\Model\GenerateTokenTrait;
use App\UseCases\Models\Abstracts\CreateUpdateModelAbstractUseCase;
use App\UseCases\Models\Contracts\CreateProcessInstanceUseCaseInterface;
use App\UseCases\ModelValidators\Contracts\ModelAttributesValidatorUseCaseInterface;
use App\UseCases\ModelValidators\Contracts\ProcessInstanceAttributesValidatorUseCaseInterface;

class CreateProcessInstanceUseCase extends CreateUpdateModelAbstractUseCase implements CreateProcessInstanceUseCaseInterface
{
    use GenerateTokenTrait;

    public function __construct(
        private readonly ProcessElementRepositoryInterface $processElementRepository,
        private readonly ProcessInstanceRepositoryInterface $processInstanceRepository,
        private readonly ProcessInstanceAttributesValidatorUseCaseInterface $processInstanceAttributesValidator
    ) {}

    protected function getModelAttributesValidator(): ModelAttributesValidatorUseCaseInterface
    {
        return $this->processInstanceAttributesValidator;
    }

    protected function createInstance(): AbstractModel
    {
        return new ProcessInstance();
    }

    protected function getModelRepository(): ModelRepositoryInterface
    {
        return $this->processInstanceRepository;
    }

    protected function preFillActions(AbstractModel &$modelInstance, array &$attributes, array &$extraData): void
    {
        if (!$modelInstance->token){
            $modelInstance->token = $this->generateToken();
        }
    }

    protected function postFillActions(AbstractModel &$modelInstance, array &$attributes, array &$extraData): void
    {
        $process = $modelInstance->process;
        $startProcess = $this->processElementRepository->getStartEventByProcess($process);
        $modelInstance->current_element_id = $startProcess->id;
    }
}