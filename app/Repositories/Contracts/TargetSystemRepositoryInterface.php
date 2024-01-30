<?php

namespace App\Repositories\Contracts;

use App\Models\TargetSystem;

interface TargetSystemRepositoryInterface extends ModelRepositoryInterface
{
    /**
     * Find a target system by its token.
     *
     * @param string $token The token of the target system.
     * 
     * @return TargetSystem|null The found target system, or null if not found.
     */
    public function findByToken(string $token): ?TargetSystem;
}