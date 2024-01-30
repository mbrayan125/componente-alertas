<?php

namespace App\Repositories\Contracts;

use App\Models\Process;
use App\Models\TargetSystem;

interface ProcessRepositoryInterface extends ModelRepositoryInterface
{
    /**
     * Find a process by its target system and token.
     *
     * @param TargetSystem $targetSystem The target system of the process.
     * @param string $processToken The token of the process.
     * 
     * @return Process|null The found process, or null if not found.
     */
    public function findByTargetSystemAndToken(TargetSystem $targetSystem, string $processToken): ?Process;
}