<?php

namespace App\UseCases\Models;

use App\Exceptions\Model\ModelValidationException;
use App\Models\Abstracts\AbstractModel;
use App\Models\Process;
use App\Repositories\Contracts\ModelRepositoryInterface;
use App\Repositories\Contracts\ProcessRepositoryInterface;
use App\Traits\Model\GenerateTokenTrait;
use App\UseCases\Files\Contracts\MoveFileUseCaseInterface;
use App\UseCases\Files\Contracts\RemoveFileUseCaseInterface;
use App\UseCases\Models\Abstracts\CreateModelAbstractUseCase;
use App\UseCases\Models\Contracts\CreateProcessUseCaseInterface;
use App\UseCases\ModelValidators\Contracts\ModelAttributesValidatorUseCaseInterface;
use App\UseCases\ModelValidators\Contracts\ProcessAttributesValidatorUseCaseInterface;
use App\UseCases\Process\Contracts\ReadBpmnProcessUseCaseInterface;
use App\UseCases\Process\Contracts\SaveReadedBpmnProcessUseCaseInterface;
use Throwable;

class CreateProcessUseCase extends CreateModelAbstractUseCase implements CreateProcessUseCaseInterface
{
    use GenerateTokenTrait;

    public function __construct(
        private readonly MoveFileUseCaseInterface $moveFileUseCase,
        private readonly ProcessRepositoryInterface $processRepository,
        private readonly ProcessAttributesValidatorUseCaseInterface $processAttributesValidator,
        private readonly RemoveFileUseCaseInterface $removeFileUseCase,
        private readonly ReadBpmnProcessUseCaseInterface $readBpmnProcessUseCase,
        private readonly SaveReadedBpmnProcessUseCaseInterface $saveReadedBpmnProcessUseCase
    ) {}

    /**
     * @inheritDoc
     */
    protected function getModelAttributesValidator(): ModelAttributesValidatorUseCaseInterface
    {
        return $this->processAttributesValidator;
    }

    /**
     * @inheritDoc
     */
    protected function createInstance(): AbstractModel
    {
        return new Process();
    }

    /**
     * @inheritDoc
     */
    protected function getModelRepository(): ModelRepositoryInterface
    {
        return $this->processRepository;
    }

    /**
     * @inheritDoc
     */
    protected function preFillActions(AbstractModel &$newInstance, array &$attributes, array &$extraData): void
    {
        $newInstance->token = $this->generateToken();
    }

    /**
     * @inheritDoc
     */
    protected function postSaveActions(AbstractModel &$newInstance, array &$attributes, array &$extraData): void
    {
        // Read and save the BPMN process
        $readedBpmn = ($this->readBpmnProcessUseCase)($newInstance->bpmn_filepath);
        if (!$readedBpmn->success) {
            throw new ModelValidationException(
                'El proceso BPMN no pudo ser leído',
                $readedBpmn->errors,
                $readedBpmn->warnings
            );
        }
        ($this->saveReadedBpmnProcessUseCase)($readedBpmn, $newInstance);

        // Move the file to the storage
        $fileInfo = pathinfo($newInstance->bpmn_filepath);
        $finalPath = $this->generateStoragePath($newInstance, $fileInfo['basename']);
        ($this->moveFileUseCase)($newInstance->bpmn_filepath, $finalPath);
        $newInstance->bpmn_filepath = $finalPath;
    }

    /**
     * @inheritDoc
     */
    protected function handleRollback(AbstractModel $newInstance, Throwable $ex): void
    {
        $processPath = $newInstance->bpmn_filepath;
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