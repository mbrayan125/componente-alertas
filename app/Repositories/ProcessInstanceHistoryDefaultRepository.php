<?php

namespace App\Repositories;

use App\Models\ProcessInstanceHistory;
use App\Repositories\Abstracts\ModelRepositoryAbstract;
use App\Repositories\Contracts\ProcessInstanceHistoryRepositoryInterface;

class ProcessInstanceHistoryDefaultRepository extends ModelRepositoryAbstract implements ProcessInstanceHistoryRepositoryInterface
{
    /**
     * @inheritDoc
     */
    protected function getModelClass(): string
    {
        return ProcessInstanceHistory::class;
    }
}