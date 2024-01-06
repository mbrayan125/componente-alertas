<?php

namespace App\Exceptions\Model;

use App\DataResultObjects\Generic\BaseResultDRO;
use App\Exceptions\General\DomainException;

class ModelValidationException extends DomainException
{
    /**
     * Class ModelValidationException
     * 
     * Exception thrown when incorrect data is sent for creating a model.
     * 
     * @param array $errors An array of errors encountered during the data validation.
     * @param array $warnings An array of warnings encountered during the data validation.
     */
    public function __construct(
        string $message,
        array $errors,
        array $warnings = [],
    ) {
        parent::__construct($message, $errors, $warnings, self::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Creates an instance of IncorrectDataSentException with a single error message.
     *
     * @param string $message The error message.
     * 
     * @return self The created IncorrectDataSentException instance.
     */
    public static function createFromSingleMessage(string $message): self
    {
        return new self($message, []);
    }
}
