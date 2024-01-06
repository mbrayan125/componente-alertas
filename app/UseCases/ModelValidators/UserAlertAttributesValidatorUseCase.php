<?php

namespace App\UseCases\ModelValidators;

use App\Repositories\Contracts\ModelRepositoryInterface;
use App\Repositories\Contracts\UserAlertRepositoryInterface;
use App\UseCases\ModelValidators\Abstracts\ModelAttributesValidatorAbstractUseCase as Validator;
use App\UseCases\ModelValidators\Contracts\UserAlertAttributesValidatorUseCaseInterface;

class UserAlertAttributesValidatorUseCase extends Validator implements UserAlertAttributesValidatorUseCaseInterface
{
    public function __construct(
        private readonly UserAlertRepositoryInterface $userAlertRepository
    ) {}

    protected function getAttributesConfig(): array
    {
        return [
            
        ];
    }

    protected function getModelRepository(): ModelRepositoryInterface
    {
        return $this->userAlertRepository;
    }

    public function getModelName(): string
    {
        return 'alerta de usuario';
    }
}