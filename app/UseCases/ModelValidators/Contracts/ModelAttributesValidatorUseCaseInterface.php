<?php

namespace App\UseCases\ModelValidators\Contracts;

use App\DataResultObjects\Generic\ResultDRO as Result;
use App\Models\Abstracts\AbstractModel;

interface ModelAttributesValidatorUseCaseInterface
{
    /**
     * Validates the attributes of a model entity.
     *
     * @param array $attributes The attributes to be validated.
     * @param AbstractModel $entity The model entity to validate against.
     * 
     * @return Result The result of the validation process.
     */
    public function __invoke(array $attributes, AbstractModel $entity): Result;

    /**
     * Returns the name of the model.
     *
     * @return string The name of the model.
     */
    public function getModelName(): string;
}