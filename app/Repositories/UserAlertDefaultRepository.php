<?php

namespace App\Repositories;

use App\Models\UserAlert;
use App\Repositories\Abstracts\ModelRepositoryAbstract;
use App\Repositories\Contracts\UserAlertRepositoryInterface;

class UserAlertDefaultRepository extends ModelRepositoryAbstract implements UserAlertRepositoryInterface
{
    /**
     * @inheritDoc
     */
    protected function getModelClass(): string
    {
        return UserAlert::class;
    }
}