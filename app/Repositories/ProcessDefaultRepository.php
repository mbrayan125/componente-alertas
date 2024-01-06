<?php

namespace App\Repositories;

use App\Models\Process;
use App\Repositories\Abstracts\ModelRepositoryAbstract;
use App\Repositories\Contracts\ProcessRepositoryInterface;

class ProcessDefaultRepository extends ModelRepositoryAbstract implements ProcessRepositoryInterface
{
    /**
     * @inheritDoc
     */
    protected function getModelClass(): string
    {
        return Process::class;
    }
}