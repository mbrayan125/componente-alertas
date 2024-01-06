<?php

namespace App\UseCases\ModelValidators;

use App\DataTransferObjects\ModelValidators\AttributesValidatorDTO as Attribute;
use App\Repositories\Contracts\ModelRepositoryInterface;
use App\Repositories\Contracts\ProcessRepositoryInterface;
use App\UseCases\ModelValidators\Abstracts\ModelAttributesValidatorAbstractUseCase as Validator;
use App\UseCases\ModelValidators\Contracts\ProcessAttributesValidatorUseCaseInterface;

class ProcessAttributesValidatorUseCase extends Validator implements ProcessAttributesValidatorUseCaseInterface
{
    public function __construct(
        private readonly ProcessRepositoryInterface $processRepository
    ) {}

    protected function getAttributesConfig(): array
    {
        return [
            'target_system_id' => Attribute::integer()->required()->minValue(1),
            'version'          => Attribute::integer()->required()->minValue(1)
        ];
    }

    protected function getModelRepository(): ModelRepositoryInterface
    {
        return $this->processRepository;
    }

    public function getModelName(): string
    {
        return 'proceso';
    }
}