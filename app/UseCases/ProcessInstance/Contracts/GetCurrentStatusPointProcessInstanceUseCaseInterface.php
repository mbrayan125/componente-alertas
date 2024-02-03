<?php

namespace App\UseCases\ProcessInstance\Contracts;

use App\Models\ProcessInstance;

interface GetCurrentStatusPointProcessInstanceUseCaseInterface
{
    /**
     * Invokes the GetSuggestedAlertsProcessInstanceUseCaseInterface.
     *
     * @param ProcessInstance $processInstance The process instance.
     *
     * @return string|null The result of the use case.
     */
    public function __invoke(ProcessInstance $processInstances): ?string;
}