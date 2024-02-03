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
            'target_system_id'     => Attribute::integer()->required()->minValue(1),
            'name_subject'         => Attribute::string()->required(),
            'name_verb'            => Attribute::string()->required(),
            'name_complement'      => Attribute::string()->required(),
            'bpmn_filepath'        => Attribute::string()->required(),
            'version'              => Attribute::integer()->required()->minValue(1),
            'risky_execution'      => Attribute::boolean()->required(),
            'idempotent_execution' => Attribute::boolean()->required()
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