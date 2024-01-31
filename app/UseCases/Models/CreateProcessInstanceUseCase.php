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
    private ?int $currentElementId = null;

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

    protected function preFillActions(AbstractModel &$modelInstance, array &$attributes, array &$extraData): void
    {
        $this->newProcessInstance = $modelInstance->id === null;
        $this->currentElementId = $modelInstance->current_element_id;
        if (!$modelInstance->token){
            $modelInstance->token = $this->generateToken();
        }
    }

    protected function postFillActions(AbstractModel &$modelInstance, array &$attributes, array &$extraData): void
    {
        $process = $modelInstance->process;
        $selectedProcessElement = null;
        if (!$this->currentElementId) {
            $selectedProcessElement = $this->processElementRepository->getStartEventByProcess($process);
        }

        if ($nextElementId = $extraData['next_element_id'] ?? null) {
            $selectedProcessElement = $this->processElementRepository->getByBpmnIdAndProcess($process, $nextElementId);
            unset($extraData['next_element_id']);
        }

        if ($selectedProcessElement) {
            $modelInstance->current_element_id = $selectedProcessElement->id;
        }
    }

    protected function postSaveActions(AbstractModel &$modelInstance, array &$attributes, array &$extraData): void
    {
        if ($modelInstance->current_element_id !== $this->currentElementId) {
            ($this->createProcessInstanceHistoryUseCase)([
                'process_instance_id' => $modelInstance->id,
                'process_element_id'  => $modelInstance->current_element_id,
                'history_previous_id' => $this->currentElementId
            ]);
        }
    }
}