<?php

namespace App\UseCases\Models;

use App\Models\Abstracts\AbstractModel;
use App\Models\ProcessInstance;
use App\Repositories\Contracts\ModelRepositoryInterface;
use App\Repositories\Contracts\ProcessInstanceRepositoryInterface;
use App\UseCases\Models\Abstracts\CreateModelAbstractUseCase;
use App\UseCases\Models\Contracts\CreateProcessInstanceUseCaseInterface;
use App\UseCases\ModelValidators\Contracts\ModelAttributesValidatorUseCaseInterface;
use App\UseCases\ModelValidators\Contracts\ProcessInstanceAttributesValidatorUseCaseInterface;

class CreateProcessInstanceUseCase extends CreateModelAbstractUseCase implements CreateProcessInstanceUseCaseInterface
{
    public function __construct(
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
}