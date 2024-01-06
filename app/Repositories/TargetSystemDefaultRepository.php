<?php

namespace App\Repositories;

use App\Models\TargetSystem;
use App\Repositories\Abstracts\ModelRepositoryAbstract;
use App\Repositories\Contracts\TargetSystemRepositoryInterface;

class TargetSystemDefaultRepository extends ModelRepositoryAbstract implements TargetSystemRepositoryInterface
{
    /**
     * @inheritDoc
     */
    protected function getModelClass(): string
    {
        return TargetSystem::class;
    }
}