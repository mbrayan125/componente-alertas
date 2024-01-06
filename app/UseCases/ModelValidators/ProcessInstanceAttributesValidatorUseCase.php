<?php

namespace App\UseCases\ModelValidators;

use App\Repositories\Contracts\ModelRepositoryInterface;
use App\Repositories\Contracts\ProcessInstanceRepositoryInterface;
use App\UseCases\ModelValidators\Abstracts\ModelAttributesValidatorAbstractUseCase as Validator;
use App\UseCases\ModelValidators\Contracts\ProcessInstanceAttributesValidatorUseCaseInterface;

class ProcessInstanceAttributesValidatorUseCase extends Validator implements ProcessInstanceAttributesValidatorUseCaseInterface
{
    public function __construct(
        private readonly ProcessInstanceRepositoryInterface $processInstanceRepository
    ) {}

    protected function getAttributesConfig(): array
    {
        return [
            
        ];
    }

    protected function getModelRepository(): ModelRepositoryInterface
    {
        return $this->processInstanceRepository;
    }

    public function getModelName(): string
    {
        return 'instancia de proceso';
    }
}