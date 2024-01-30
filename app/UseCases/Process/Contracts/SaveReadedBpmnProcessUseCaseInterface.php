<?php

namespace App\UseCases\Process\Contracts;

use App\DataResultObjects\Process\ReadBpmnUseCaseDRO;
use App\Models\Process;

interface SaveReadedBpmnProcessUseCaseInterface
{
    public function __invoke(ReadBpmnUseCaseDRO $readedProcess, Process $newProcess): void;
}