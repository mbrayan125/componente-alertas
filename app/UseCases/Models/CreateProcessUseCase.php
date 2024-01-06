<?php

namespace App\UseCases\Models;

use App\Models\Abstracts\AbstractModel;
use App\Models\Process;
use App\Repositories\Contracts\ModelRepositoryInterface;
use App\Repositories\Contracts\ProcessRepositoryInterface;
use App\UseCases\Models\Abstracts\CreateModelAbstractUseCase;
use App\UseCases\Models\Contracts\CreateProcessUseCaseInterface;
use App\UseCases\ModelValidators\Contracts\ModelAttributesValidatorUseCaseInterface;
use App\UseCases\ModelValidators\Contracts\ProcessAttributesValidatorUseCaseInterface;

class CreateProcessUseCase extends CreateModelAbstractUseCase implements CreateProcessUseCaseInterface
{
    public function __construct(
        private readonly ProcessRepositoryInterface $processRepository,
        private readonly ProcessAttributesValidatorUseCaseInterface $processAttributesValidator
    ) {}

    protected function getModelAttributesValidator(): ModelAttributesValidatorUseCaseInterface
    {
        return $this->processAttributesValidator;
    }

    protected function createInstance(): AbstractModel
    {
        return new Process();
    }

    protected function getModelRepository(): ModelRepositoryInterface
    {
        return $this->processRepository;
    }
}