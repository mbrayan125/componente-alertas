<?php

namespace App\DataTransferObjects\ModelValidators;

use App\DataResultObjects\Generic\ResultDRO;
use App\Traits\Validator\ValidatorConstantsTrait;

class AttributesValidatorDTO
{
    use ValidatorConstantsTrait;

    private $validator = null;

    public function __construct(
        public readonly string $type,
        public bool $required,
        public array $restrictions
    )
    { }

    public static function string(): self
    {
        return new self(
            self::TYPE_STRING,
            false,
            []
        );
    }

    public static function integer(): self
    {
        return new self(
            self::TYPE_INT,
            false,
            []
        );
    }

    public static function boolean(): self
    {
        return new self(
            self::TYPE_BOOL,
            false,
            []
        );
    }

    public static function array(): self
    {
        return new self(
            self::TYPE_ARRAY,
            false,
            []
        );
    }

    public function required(bool $required = true): self
    {
        $this->required = $required;
        return $this;
    }

    public function minLength(int $minLength): self
    {
        $this->restrictions[self::RESTRICTION_MIN_LENGTH] = $minLength;
        return $this;
    }

    public function maxLength(int $maxLength): self
    {
        $this->restrictions[self::RESTRICTION_MAX_LENGTH] = $maxLength;
        return $this;
    }

    public function minValue(int $minValue): self
    {
        $this->restrictions[self::RESTRICTION_MIN_VALUE] = $minValue;
        return $this;
    }

    public function maxValue(int $maxValue): self
    {
        $this->restrictions[self::RESTRICTION_MAX_VALUE] = $maxValue;
        return $this;
    }

    public function unique(): self
    {
        $this->restrictions[self::RESTRICTION_UNIQUE] = true;
        return $this;
    }

    public function inValues(array $values): self
    {
        $this->restrictions[self::RESTRICTION_IN_VALUES] = $values;
        return $this;
    }

    public function validator(callable $validator): self
    {
        $this->validator = $validator;
        return $this;
    }

    public function callCustomValidator(CustomFunctionValidatorDTO $parameters): ResultDRO
    {
        if (is_null($this->validator)) {
            return ResultDRO::createSuccess([]);
        }
        return call_user_func($this->validator, $parameters);
    }
}