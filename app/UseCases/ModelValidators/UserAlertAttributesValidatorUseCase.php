<?php

namespace App\UseCases\ModelValidators;

use App\DataTransferObjects\ModelValidators\AttributesValidatorDTO as Attribute;
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
            'process_instance_history_id' => Attribute::integer()->required(),
            'type'                        => Attribute::string()->required()->maxLength(128),
            'visual_representation'       => Attribute::string()->required()->maxLength(128),
            'color'                       => Attribute::string()->required()->maxLength(128),
            'title'                       => Attribute::string()->required()->maxLength(128),
            'message'                     => Attribute::string()->required(),
            'icon'                        => Attribute::string()->required()->maxLength(128),
            'actions'                     => Attribute::array()->required(),
            'alert_moment'                => Attribute::string()->required()->maxLength(128)
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