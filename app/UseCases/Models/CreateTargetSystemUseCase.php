<?php

namespace App\UseCases\Models;

use App\Exceptions\Model\ModelValidationException;
use App\Models\Abstracts\AbstractModel;
use App\Models\TargetSystem;
use App\Repositories\Contracts\ModelRepositoryInterface;
use App\Repositories\Contracts\TargetSystemRepositoryInterface;
use App\UseCases\Models\Abstracts\CreateModelAbstractUseCase;
use App\UseCases\Models\Contracts\CreateTargetSystemUseCaseInterface;
use App\UseCases\ModelValidators\Contracts\ModelAttributesValidatorUseCaseInterface;
use App\UseCases\ModelValidators\Contracts\TargetSystemAttributesValidatorUseCaseInterface;
use App\UseCases\Process\Contracts\ReadBpmnProcessUseCaseInterface;
use App\UseCases\Process\Contracts\SaveReadedBpmnProcessUseCaseInterface;
use App\UseCases\Files\Contracts\MoveFileUseCaseInterface;
use App\UseCases\Files\Contracts\RemoveFileUseCaseInterface;
use Throwable;

class CreateTargetSystemUseCase extends CreateModelAbstractUseCase implements CreateTargetSystemUseCaseInterface
{
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
    protected function postSaveActions(AbstractModel &$newInstance, array &$attributes, array &$extraData): void
    {
        // Read and save the BPMN process
        $readedBpmn = ($this->readBpmnProcess)($newInstance->process_path);
        if (!$readedBpmn->success) {
            throw new ModelValidationException(
                'El proceso BPMN no pudo ser leído',
                $readedBpmn->errors,
                $readedBpmn->warnings
            );
        }
        ($this->saveReadedBpmnProcess)($readedBpmn, $newInstance);

        // Move the file to the storage
        $fileInfo = pathinfo($newInstance->process_path);
        $finalPath = $this->generateStoragePath($newInstance, $fileInfo['basename']);
        ($this->moveFileUseCase)($newInstance->process_path, $finalPath);
        $newInstance->process_path = $finalPath;
    }

    /**
     * @inheritDoc
     */
    protected function handleRollback(AbstractModel $newInstance, Throwable $ex): void
    {
        $processPath = $newInstance->process_path;
        if ($processPath && file_exists($processPath)) {
            ($this->removeFileUseCase)($processPath);
        }
    }

    /**
     * Generates the storage path for a new instance of AbstractModel.
     *
     * @param AbstractModel $newInstance The new instance of AbstractModel.
     * @param string $fileName The name of the file.
     * 
     * @return string The generated storage path.
     */
    private function generateStoragePath(AbstractModel $newInstance, string $fileName): string
    {
        return sprintf('/app/storage/processes/%s/v-%s_%s', $newInstance->id, 1, $fileName);
    }
}