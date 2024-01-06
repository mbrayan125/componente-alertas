<?php

namespace App\UseCases\ModelValidators;

use App\DataTransferObjects\ModelValidators\AttributesValidatorDTO as Attribute;
use App\Repositories\Contracts\ModelRepositoryInterface;
use App\Repositories\Contracts\UserRoleRepositoryInterface;
use App\UseCases\ModelValidators\Abstracts\ModelAttributesValidatorAbstractUseCase as Validator;
use App\UseCases\ModelValidators\Contracts\UserRoleAttributesValidatorUseCaseInterface;

class UserRoleAttributesValidatorUseCase extends Validator implements UserRoleAttributesValidatorUseCaseInterface
{
    public function __construct(
        private readonly UserRoleRepositoryInterface $userRoleRepository
    ) {}

    protected function getAttributesConfig(): array
    {
        return [
            'process_id' => Attribute::integer()->required(),
            'name'       => Attribute::string()->required()->maxLength(128)
        ];
    }

    protected function getModelRepository(): ModelRepositoryInterface
    {
        return $this->userRoleRepository;
    }

    public function getModelName(): string
    {
        return 'rol de usuario';
    }
}