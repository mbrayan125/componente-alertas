<?php

namespace App\DataTransferObjects\ModelValidators;

use App\Models\Abstracts\AbstractModel;

class CustomFunctionValidatorDTO
{
    public function __construct(
        public readonly AbstractModel $entity,
        public readonly mixed $valueSent
    ) { }

    public static function create(
        AbstractModel $entity,
        mixed $valueSent
    ): self {
        return new self($entity, $valueSent);
    }
}