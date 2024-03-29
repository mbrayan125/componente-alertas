<?php

namespace App\UseCases\ProcessInstance\Contracts;

use App\Models\ProcessInstance;

interface GetSuggestedAlertsProcessInstanceUseCaseInterface
{
    /**
     * Invokes the GetSuggestedAlertsProcessInstanceUseCaseInterface.
     *
     * @param ProcessInstance $processInstance The process instance.
     *
     * @return array The result of the use case.
     */
    public function __invoke(ProcessInstance $processInstance): array;
}