<?php

namespace App\UseCases\Process\Contracts;

use App\Models\Process;

interface CheckFlowPatternBpmnProcessUseCaseInterface
{
    /**
     * Executes the CheckFlowPatternProcessUseCaseInterface.
     *
     * @param Process $process The process to be checked.
     *
     * @return array The result of the process check.
     */
    public function __invoke(Process $process): array;
}