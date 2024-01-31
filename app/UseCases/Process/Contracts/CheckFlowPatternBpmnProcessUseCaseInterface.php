<?php

namespace App\UseCases\Process\Contracts;

use App\DataResultObjects\Generic\ResultDRO as Result;
use App\Models\Process;

interface CheckFlowPatternBpmnProcessUseCaseInterface
{
    /**
     * Executes the CheckFlowPatternProcessUseCaseInterface.
     *
     * @param Process $process The process to be checked.
     *
     * @return Result The result of the process check.
     */
    public function __invoke(Process $process): Result;
}