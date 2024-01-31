<?php

namespace App\UseCases\ModelValidators;

use App\DataTransferObjects\ModelValidators\AttributesValidatorDTO as Attribute;
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
            'process_instance_id' => Attribute::integer()->required(),
            'process_element_id'  => Attribute::integer()->required(),
            'history_previous_id' => Attribute::integer()
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