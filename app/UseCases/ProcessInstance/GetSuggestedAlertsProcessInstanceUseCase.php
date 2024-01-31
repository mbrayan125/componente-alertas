<?php

namespace App\UseCases\ProcessInstance;

use App\DataResultObjects\Generic\ResultDRO as Result;
use App\Models\ProcessInstance;
use App\UseCases\Process\Contracts\CheckFlowPatternBpmnProcessUseCaseInterface;
use App\UseCases\ProcessInstance\Contracts\GetSuggestedAlertsProcessInstanceUseCaseInterface;

class GetSuggestedAlertsProcessInstanceUseCase implements GetSuggestedAlertsProcessInstanceUseCaseInterface
{
    public function __construct(
        private readonly CheckFlowPatternBpmnProcessUseCaseInterface $checkFlowPatternBpmnProcessUseCase
    ) { }

    public function __invoke(ProcessInstance $processInstance): Result
    {
        $flowPattern = ($this->checkFlowPatternBpmnProcessUseCase)($processInstance->process);
        return Result::createSuccess([]);
    }
}