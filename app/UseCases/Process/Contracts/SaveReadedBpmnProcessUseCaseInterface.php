<?php

namespace App\UseCases\Process\Contracts;

use App\DataResultObjects\Process\ReadBpmnUseCaseDRO;
use App\Models\TargetSystem;

interface SaveReadedBpmnProcessUseCaseInterface
{
    public function __invoke(ReadBpmnUseCaseDRO $readedProcess, TargetSystem $targetSystem): void;
}