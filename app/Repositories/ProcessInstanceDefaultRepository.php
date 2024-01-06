<?php

namespace App\Repositories;

use App\Models\ProcessInstance;
use App\Repositories\Abstracts\ModelRepositoryAbstract;
use App\Repositories\Contracts\ProcessInstanceRepositoryInterface;

class ProcessInstanceDefaultRepository extends ModelRepositoryAbstract implements ProcessInstanceRepositoryInterface
{
    /**
     * @inheritDoc
     */
    protected function getModelClass(): string
    {
        return ProcessInstance::class;
    }
}