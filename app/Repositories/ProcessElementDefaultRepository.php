<?php

namespace App\Repositories;

use App\Models\ProcessElement;
use App\Repositories\Abstracts\ModelRepositoryAbstract;
use App\Repositories\Contracts\ProcessElementRepositoryInterface;

class ProcessElementDefaultRepository extends ModelRepositoryAbstract implements ProcessElementRepositoryInterface
{
    /**
     * @inheritDoc
     */
    protected function getModelClass(): string
    {
        return ProcessElement::class;
    }
}