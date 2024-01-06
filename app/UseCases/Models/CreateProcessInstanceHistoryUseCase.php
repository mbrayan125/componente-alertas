<?php

namespace App\UseCases\Models;

use App\Models\Abstracts\AbstractModel;
use App\Models\ProcessInstanceHistory;
use App\Repositories\Contracts\ModelRepositoryInterface;
use App\Repositories\Contracts\ProcessInstanceHistoryRepositoryInterface;
use App\UseCases\Models\Abstracts\CreateModelAbstractUseCase;
use App\UseCases\Models\Contracts\CreateProcessInstanceHistoryUseCaseInterface;
use App\UseCases\ModelValidators\Contracts\ModelAttributesValidatorUseCaseInterface;
use App\UseCases\ModelValidators\Contracts\ProcessInstanceHistoryAttributesValidatorUseCaseInterface;

class CreateProcessInstanceHistoryUseCase extends CreateModelAbstractUseCase implements CreateProcessInstanceHistoryUseCaseInterface
{
    public function __construct(
        private readonly ProcessInstanceHistoryRepositoryInterface $processInstanceHistoryRepository,
        private readonly ProcessInstanceHistoryAttributesValidatorUseCaseInterface $processInstanceHistoryAttributesValidator
    ) {}

    protected function getModelAttributesValidator(): ModelAttributesValidatorUseCaseInterface
    {
        return $this->processInstanceHistoryAttributesValidator;
    }

    protected function createInstance(): AbstractModel
    {
        return new ProcessInstanceHistory();
    }

    protected function getModelRepository(): ModelRepositoryInterface
    {
        return $this->processInstanceHistoryRepository;
    }
}