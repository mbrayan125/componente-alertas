<?php

namespace App\Repositories;

use App\Models\UserRole;
use App\Repositories\Abstracts\ModelRepositoryAbstract;
use App\Repositories\Contracts\UserRoleRepositoryInterface;

class UserRoleDefaultRepository extends ModelRepositoryAbstract implements UserRoleRepositoryInterface
{
    /**
     * @inheritDoc
     */
    protected function getModelClass(): string
    {
        return UserRole::class;
    }
}