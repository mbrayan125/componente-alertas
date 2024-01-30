<?php

namespace App\UseCases\Models;

use App\Models\Abstracts\AbstractModel;
use App\Models\UserAlert;
use App\Repositories\Contracts\ModelRepositoryInterface;
use App\Repositories\Contracts\UserAlertRepositoryInterface;
use App\UseCases\Models\Abstracts\CreateUpdateModelAbstractUseCase;
use App\UseCases\Models\Contracts\CreateUserAlertUseCaseInterface;
use App\UseCases\ModelValidators\Contracts\ModelAttributesValidatorUseCaseInterface;
use App\UseCases\ModelValidators\Contracts\UserAlertAttributesValidatorUseCaseInterface;

class CreateUserAlertUseCase extends CreateUpdateModelAbstractUseCase implements CreateUserAlertUseCaseInterface
{
    public function __construct(
        private readonly UserAlertRepositoryInterface $userAlertRepository,
        private readonly UserAlertAttributesValidatorUseCaseInterface $userAlertAttributesValidator
    ) {}

    protected function getModelAttributesValidator(): ModelAttributesValidatorUseCaseInterface
    {
        return $this->userAlertAttributesValidator;
    }

    protected function createInstance(): AbstractModel
    {
        return new UserAlert();
    }

    protected function getModelRepository(): ModelRepositoryInterface
    {
        return $this->userAlertRepository;
    }
}