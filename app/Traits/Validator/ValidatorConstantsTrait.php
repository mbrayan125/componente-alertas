<?php

namespace App\Traits\Validator;

trait ValidatorConstantsTrait
{
    public const TYPE_STRING = 'texto';
    public const TYPE_INT    = 'entero';
    public const TYPE_BOOL   = 'booleano';
    public const TYPE_ARRAY  = 'colección';

    public const RESTRICTION_REQUIRED   = 'attr_required';
    public const RESTRICTION_MIN_LENGTH = 'attr_min_length';
    public const RESTRICTION_MAX_LENGTH = 'attr_max_length';
    public const RESTRICTION_MIN_VALUE  = 'attr_min_value';
    public const RESTRICTION_MAX_VALUE  = 'attr_max_value';
    public const RESTRICTION_UNIQUE     = 'attr_unique';
    public const RESTRICTION_IN_VALUES  = 'attr_in_values';
}