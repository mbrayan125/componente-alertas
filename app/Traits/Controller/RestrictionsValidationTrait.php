<?php 

namespace App\Traits\Controller;

trait RestrictionsValidationTrait 
{
    private const RESTRICTION_REQUIRED = 'required';
    private const RESTRICTION_MAX_LENGTH = 'maxLength';

    private const PARAM_TYPE_ARRAY = 'array';
    private const PARAM_TYPE_FILE = 'file';
}