<?php

namespace App\UseCases\Models;

use App\Models\Abstracts\AbstractModel;
use App\Models\ProcessInstance;
use App\Repositories\Contracts\ModelRepositoryInterface;
use App\Repositories\Contracts\ProcessElementRepositoryInterface;
use App\Repositories\Contracts\ProcessInstanceRepositoryInterface;
use App\Traits\Model\GenerateTokenTrait;
use App\UseCases\Models\Abstracts\CreateUpdateModelAbstractUseCase;
use App\UseCases\Models\Contracts\CreateProcessInstanceHistoryUseCaseInterface;
use App\UseCases\Models\Contracts\CreateProcessInstanceUseCaseInterface;
use App\UseCases\ModelValidators\Contracts\ModelAttributesValidatorUseCaseInterface;
use App\UseCases\ModelValidators\Contracts\ProcessInstanceAttributesValidatorUseCaseInterface;
use App\UseCases\Process\Contracts\CheckFlowPatternBpmnProcessUseCaseInterface;

class CreateProcessInstanceUseCase extends CreateUpdateModelAbstractUseCase implements CreateProcessInstanceUseCaseInterface
{
    use GenerateTokenTrait;

    private bool $newProcessInstance = false;
    private ?int $currentHistoryId = null;

    public function __construct(
        private readonly CreateProcessInstanceHistoryUseCaseInterface $createProcessInstanceHistoryUseCase,
        private readonly ProcessElementRepositoryInterface $processElementRepository,
        private readonly ProcessInstanceRepositoryInterface $processInstanceRepository,
        private readonly ProcessInstanceAttributesValidatorUseCaseInterface $processInstanceAttributesValidator
    ) {}

    protected function getModelAttributesValidator(): ModelAttributesValidatorUseCaseInterface
    {
        return $this->processInstanceAttributesValidator;
    }

    protected function createInstance(): AbstractModel
    {
        return new ProcessInstance();
    }

    protected function getModelRepository(): ModelRepositoryInterface
    {
        return $this->processInstanceRepository;
    }

    /**
     * @inheritDoc
     */
    protected function preFillActions(AbstractModel &$modelInstance, array &$attributes, array &$extraData): void
    {
        $this->newProcessInstance = $modelInstance->id === null;
        $this->currentHistoryId = $modelInstance->current_history_id;
        if (!$modelInstance->token){
            $modelInstance->token = $this->generateToken();
        }
    }

    /**
     * @inheritDoc
     */
    protected function postSaveActions(AbstractModel &$modelInstance, array &$attributes, array &$extraData): void
    {
        $process = $modelInstance->process;
        $selectedProcessElement = null;
        $createHistory = false;

        if (!$currentHistoryElement = $modelInstance->currentHistory) {
            $selectedProcessElement = $this->processElementRepository->getStartEventByProcess($process);
            $createHistory = true;
        }

        if ($nextElementId = $extraData['next_element_id'] ?? null) {
            $selectedProcessElement = $this->processElementRepository->getByBpmnIdAndProcess($process, $nextElementId);
            if ($currentHistoryElement && $currentHistoryElement->processElement->id !== $selectedProcessElement->id) {
                $createHistory = true;
            }
        }

        if ($createHistory && $selectedProcessElement) {
            $newHistory = ($this->createProcessInstanceHistoryUseCase)([
                'process_instance_id' => $modelInstance->id,
                'process_element_id'  => $selectedProcessElement->id,
                'history_previous_id' => $currentHistoryElement ? $currentHistoryElement->id : null
            ]);
            $modelInstance->current_history_id = $newHistory->id;
        }
    }
}