<?php

namespace App\UseCases\ProcessInstance\Contracts;

use App\DataResultObjects\Generic\ResultDRO as Result;
use App\Models\ProcessInstance;

interface GetSuggestedAlertsProcessInstanceUseCaseInterface
{
    /**
     * Invokes the GetSuggestedAlertsProcessInstanceUseCaseInterface.
     *
     * @param ProcessInstance $processInstance The process instance.
     *
     * @return Result The result of the use case.
     */
    public function __invoke(ProcessInstance $processInstance): Result;
}