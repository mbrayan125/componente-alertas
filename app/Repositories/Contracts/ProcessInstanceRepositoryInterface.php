<?php

namespace App\Repositories\Contracts;

use App\Models\ProcessInstance;
use App\Models\TargetSystem;

interface ProcessInstanceRepositoryInterface extends ModelRepositoryInterface
{
    /**
     * Retrieves a process instance by target system and instance token.
     *
     * @param TargetSystem $targetSystem The target system of the process instance.
     * @param string $instanceToken The instance token of the process instance.
     * 
     * @return ProcessInstance|null The process instance if found, null otherwise.
     */
    public function findByTargetSystemAndInstanceToken(TargetSystem $targetSystem, string $instanceToken): ?ProcessInstance;
}