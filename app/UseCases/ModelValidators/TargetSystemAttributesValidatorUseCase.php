<?php

namespace App\UseCases\ModelValidators;

use App\DataTransferObjects\ModelValidators\AttributesValidatorDTO as Attribute;
use App\Repositories\Contracts\ModelRepositoryInterface;
use App\Repositories\Contracts\TargetSystemRepositoryInterface;
use App\UseCases\ModelValidators\Abstracts\ModelAttributesValidatorAbstractUseCase as Validator;
use App\UseCases\ModelValidators\Contracts\TargetSystemAttributesValidatorUseCaseInterface;

class TargetSystemAttributesValidatorUseCase extends Validator implements TargetSystemAttributesValidatorUseCaseInterface
{
    /**
     * Class TargetSystemAttributesValidatorUseCase
     * 
     * This class is responsible for validating the target system attributes.
     */
    public function __construct(
        private readonly TargetSystemRepositoryInterface $targetSystemRepository
    ) { }

    /**
     * @inheritDoc
     */
    protected function getAttributesConfig(): array
    {
        return [
            'name'         => Attribute::string()->required()->maxLength(128),
            'nickname'     => Attribute::string()->required()->minLength(12)->maxLength(12)->unique(),
            'process_path' => Attribute::string()->required()
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getModelRepository(): ModelRepositoryInterface
    {
        return $this->targetSystemRepository;
    }

    /**
     * @inheritDoc
     */
    public function getModelName(): string
    {
        return 'sistema objetivo';
    }
}