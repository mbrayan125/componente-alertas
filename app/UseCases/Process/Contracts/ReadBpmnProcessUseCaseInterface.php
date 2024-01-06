<?php

namespace App\UseCases\Process\Contracts;

use App\DataResultObjects\Process\ReadBpmnUseCaseDRO;

interface ReadBpmnProcessUseCaseInterface
{
    /**
     * Reads a BPMN process from the specified path.
     *
     * @param string $bpmnPath The path to the BPMN file.
     * 
     * @return ReadBpmnUseCaseDRO The result data transfer object.
     */
    public function __invoke(string $bpmnPath): ReadBpmnUseCaseDRO;
}