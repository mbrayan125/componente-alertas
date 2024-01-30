<?php

namespace App\UseCases\Models;

use App\Models\Abstracts\AbstractModel;
use App\Models\ProcessElement;
use App\Repositories\Contracts\ModelRepositoryInterface;
use App\Repositories\Contracts\ProcessElementRepositoryInterface;
use App\UseCases\Models\Abstracts\CreateUpdateModelAbstractUseCase;
use App\UseCases\Models\Contracts\CreateProcessElementUseCaseInterface;
use App\UseCases\ModelValidators\Contracts\ModelAttributesValidatorUseCaseInterface;
use App\UseCases\ModelValidators\Contracts\ProcessElementAttributesValidatorUseCaseInterface;

class CreateProcessElementUseCase extends CreateUpdateModelAbstractUseCase implements CreateProcessElementUseCaseInterface
{
    public function __construct(
        private readonly ProcessElementRepositoryInterface $processElementRepository,
        private readonly ProcessElementAttributesValidatorUseCaseInterface $processElementAttributesValidator
    ) {}

    protected function getModelAttributesValidator(): ModelAttributesValidatorUseCaseInterface
    {
        return $this->processElementAttributesValidator;
    }

    protected function createInstance(): AbstractModel
    {
        return new ProcessElement();
    }

    protected function getModelRepository(): ModelRepositoryInterface
    {
        return $this->processElementRepository;
    }
}