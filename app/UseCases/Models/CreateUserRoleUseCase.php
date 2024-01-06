<?php

namespace App\UseCases\Models;

use App\Models\Abstracts\AbstractModel;
use App\Models\UserRole;
use App\Repositories\Contracts\ModelRepositoryInterface;
use App\Repositories\Contracts\UserRoleRepositoryInterface;
use App\UseCases\Models\Abstracts\CreateModelAbstractUseCase;
use App\UseCases\Models\Contracts\CreateUserRoleUseCaseInterface;
use App\UseCases\ModelValidators\Contracts\ModelAttributesValidatorUseCaseInterface;
use App\UseCases\ModelValidators\Contracts\UserRoleAttributesValidatorUseCaseInterface;

class CreateUserRoleUseCase extends CreateModelAbstractUseCase implements CreateUserRoleUseCaseInterface
{
    public function __construct(
        private readonly UserRoleRepositoryInterface $userRoleRepository,
        private readonly UserRoleAttributesValidatorUseCaseInterface $userRoleAttributesValidator
    ) {}

    protected function getModelAttributesValidator(): ModelAttributesValidatorUseCaseInterface
    {
        return $this->userRoleAttributesValidator;
    }

    protected function createInstance(): AbstractModel
    {
        return new UserRole();
    }

    protected function getModelRepository(): ModelRepositoryInterface
    {
        return $this->userRoleRepository;
    }
}