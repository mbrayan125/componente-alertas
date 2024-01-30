<?php

namespace App\UseCases\Models;

use App\Models\Abstracts\AbstractModel;
use App\Models\TargetSystem;
use App\Repositories\Contracts\ModelRepositoryInterface;
use App\Repositories\Contracts\TargetSystemRepositoryInterface;
use App\Traits\Model\GenerateTokenTrait;
use App\UseCases\Models\Abstracts\CreateModelAbstractUseCase;
use App\UseCases\Models\Contracts\CreateTargetSystemUseCaseInterface;
use App\UseCases\ModelValidators\Contracts\ModelAttributesValidatorUseCaseInterface;
use App\UseCases\ModelValidators\Contracts\TargetSystemAttributesValidatorUseCaseInterface;
use App\UseCases\Process\Contracts\ReadBpmnProcessUseCaseInterface;
use App\UseCases\Process\Contracts\SaveReadedBpmnProcessUseCaseInterface;
use App\UseCases\Files\Contracts\MoveFileUseCaseInterface;
use App\UseCases\Files\Contracts\RemoveFileUseCaseInterface;

class CreateTargetSystemUseCase extends CreateModelAbstractUseCase implements CreateTargetSystemUseCaseInterface
{
    use GenerateTokenTrait;

    /**
     * Class CreateTargetSystemUseCase
     * 
     * Represents a use case for creating a target system.
     */
    public function __construct(
        private readonly MoveFileUseCaseInterface $moveFileUseCase,
        private readonly ReadBpmnProcessUseCaseInterface $readBpmnProcess,
        private readonly RemoveFileUseCaseInterface $removeFileUseCase,
        private readonly SaveReadedBpmnProcessUseCaseInterface $saveReadedBpmnProcess,
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
    protected function preFillActions(AbstractModel &$newInstance, array &$attributes, array &$extraData): void
    {
        $newInstance->token = $this->generateToken();
    }
}