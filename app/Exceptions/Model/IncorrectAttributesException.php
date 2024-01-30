<?php

namespace App\Exceptions\Model;

class IncorrectAttributesException extends ModelValidationException
{
    /**
     * Class IncorrectAttributesException
     * 
     * Exception thrown when incorrect data is sent for creating a model.
     * 
     * @param array $errors An array of errors encountered during the data validation.
     * @param array $warnings An array of warnings encountered during the data validation.
     */
    public function __construct(
        string $model,
        array $errors,
        array $warnings = [],
    ) {
        parent::__construct(sprintf('Datos para creación de %s inválidos', $model), $errors, $warnings, self::HTTP_UNPROCESSABLE_ENTITY);
    }
}