<?php

namespace App\UseCases\ModelValidators;

use App\Repositories\Contracts\ModelRepositoryInterface;
use App\Repositories\Contracts\ProcessInstanceHistoryRepositoryInterface;
use App\UseCases\ModelValidators\Abstracts\ModelAttributesValidatorAbstractUseCase as Validator;
use App\UseCases\ModelValidators\Contracts\ProcessInstanceHistoryAttributesValidatorUseCaseInterface;

class ProcessInstanceHistoryAttributesValidatorUseCase extends Validator implements ProcessInstanceHistoryAttributesValidatorUseCaseInterface
{
    public function __construct(
        private readonly ProcessInstanceHistoryRepositoryInterface $processInstanceHistoryRepository
    ) {}

    protected function getAttributesConfig(): array
    {
        return [
            
        ];
    }

    protected function getModelRepository(): ModelRepositoryInterface
    {
        return $this->processInstanceHistoryRepository;
    }

    public function getModelName(): string
    {
        return 'historial de instancia de proceso';
    }
}