<?php

namespace App\UseCases\ProcessInstance;

use App\Models\ProcessInstance;
use App\DataResultObjects\Generic\ResultDRO as Result;
use App\Traits\Process\ProcessFlowPatternsConstantsTrait;
use App\Traits\Process\ProcessStatusConstantsTrait;
use App\Traits\ProcessElement\ProcessElementAssertsTrait;
use App\UseCases\ProcessInstance\Contracts\GetCurrentStatusPointProcessInstanceUseCaseInterface;
use DomainException;

class GetCurrentStatusPointProcessInstanceUseCase implements GetCurrentStatusPointProcessInstanceUseCaseInterface
{
    use ProcessElementAssertsTrait;
    use ProcessFlowPatternsConstantsTrait;
    use ProcessStatusConstantsTrait;

    public function __invoke(ProcessInstance $processInstance): ?string
    {
        if (!$currentProcessHistory = $processInstance->currentHistory) {
            throw new DomainException('La instancia no tiene un elemento actual, no se puede determinar el estado actual');
        }

        $currentProcessElement = $currentProcessHistory->processElement;
        if ($this->isEndEvent($currentProcessElement)) {
            return self::PROCESS_END;
        }
        if ($this->isStartEvent($currentProcessElement)) {
            return self::PROCESS_START;
        }
        if ($this->isSplitGateway($currentProcessElement)) {
            return self::SPLIT_GATEWAY;
        }
        if ($this->isJoinGateway($currentProcessElement)) {
            return self::JOIN_GATEWAY;
        }
        if ($this->isActivity($currentProcessElement)) {
            return self::CENTRAL_ACTIVITY;
        }

        return null;
    }
}