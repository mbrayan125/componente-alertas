<?php

namespace App\Repositories\Contracts;

use App\Models\Process;
use App\Models\ProcessElement;

interface ProcessElementRepositoryInterface extends ModelRepositoryInterface
{
    /**
     * Get the start event of a process.
     *
     * @param Process $process The process object.
     * 
     * @return ProcessElement|null The start event of the process, or null if not found.
     */
    public function getStartEventByProcess(Process $process): ?ProcessElement;
}