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

    /**
     * Get a process element by its BPMN ID and process.
     *
     * @param Process $process The process object.
     * @param string $bpmnId The BPMN ID of the process element.
     * 
     * @return ProcessElement|null The process element matching the BPMN ID and process, or null if not found.
     */
    public function getByBpmnIdAndProcess(Process $process, string $bpmnId): ?ProcessElement;
}