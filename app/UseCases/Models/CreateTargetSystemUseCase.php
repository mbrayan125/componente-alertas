<?php

namespace App\UseCases\Models;

use App\Models\Abstracts\AbstractModel;
use App\Models\TargetSystem;
use App\Repositories\Contracts\ModelRepositoryInterface;
use App\Repositories\Contracts\TargetSystemRepositoryInterface;
use App\Traits\Model\GenerateTokenTrait;
use App\UseCases\Models\Abstracts\CreateUpdateModelAbstractUseCase;
use App\UseCases\Models\Contracts\CreateTargetSystemUseCaseInterface;
use App\UseCases\ModelValidators\Contracts\ModelAttributesValidatorUseCaseInterface;
use App\UseCases\ModelValidators\Contracts\TargetSystemAttributesValidatorUseCaseInterface;

class CreateTargetSystemUseCase extends CreateUpdateModelAbstractUseCase implements CreateTargetSystemUseCaseInterface
{
    use GenerateTokenTrait;

    /**
     * Class CreateTargetSystemUseCase
     * 
     * Represents a use case for creating a target system.
     */
    public function __construct(
        private readonly TargetSystemRepositoryInterface $targetSystemRepository,
        private readonly TargetSystemAttributesValidatorUseCaseInterface $targetSystemAttributesValidator
    ) { }

    /**
     * @inheritDoc
     */
    protected function getModelAttributesValidator(): ModelAttributesValidatorUseCaseInterface
    {
        return $this->targetSystemAttributesValidator;
    }

    /**
     * @inheritDoc
     */
    protected function createInstance(): AbstractModel
    {
        return new TargetSystem();
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
    protected function preFillActions(AbstractModel &$modelInstance, array &$attributes, array &$extraData): void
    {
        if (!$modelInstance->token){
            $modelInstance->token = $this->generateToken();
        }
    }
}