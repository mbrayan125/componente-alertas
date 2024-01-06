<?php

namespace App\UseCases\ModelValidators;

use App\DataTransferObjects\ModelValidators\AttributesValidatorDTO as Attribute;
use App\Models\ProcessElement;
use App\Repositories\Contracts\ModelRepositoryInterface;
use App\Repositories\Contracts\ProcessElementRepositoryInterface;
use App\UseCases\ModelValidators\Abstracts\ModelAttributesValidatorAbstractUseCase as Validator;
use App\UseCases\ModelValidators\Contracts\ProcessElementAttributesValidatorUseCaseInterface;

class ProcessElementAttributesValidatorUseCase extends Validator implements ProcessElementAttributesValidatorUseCaseInterface
{
    public function __construct(
        private readonly ProcessElementRepositoryInterface $processElementRepository
    ) {}

    protected function getAttributesConfig(): array
    {
        return [
            'process_id'   => Attribute::integer()->required()->minValue(1),
            'user_role_id' => Attribute::integer()->required()->minValue(1),
            'name'         => Attribute::string()->maxLength(255),
            'type'         => Attribute::string()->inValues(ProcessElement::getAllElementsTypes()),
            'subtype'      => Attribute::string()
        ];
    }

    protected function getModelRepository(): ModelRepositoryInterface
    {
        return $this->processElementRepository;
    }

    public function getModelName(): string
    {
        return 'elemento de proceso';
    }
}