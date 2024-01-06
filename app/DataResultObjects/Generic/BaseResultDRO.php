<?php

namespace App\DataResultObjects\Generic;

use Exception;

abstract class BaseResultDRO
{
    /**
     * Class BaseResultDRO
     * 
     * This class represents a base result data transfer object.
     * It provides common functionality and properties for result objects.
     */
    final public function __construct(
        public readonly bool $success,
        public readonly mixed $data,
        public readonly array $errors,
        public readonly array $warnings
    ) { 
        $this->validateDataType($data);
    }

    /**
     * Creates a success result object.
     *
     * @return BaseResultDRO The success result object.
     */
    final public static function createSuccess(mixed ...$data): self 
    {
        $composed = static::generateComposedData(...$data);
        return new static(true, $composed, [], []);
    }

    /**
     * Creates a failure instance of the BaseResultDRO class.
     *
     * @return BaseResultDRO The failure instance.
     */
    final public static function createFailure(array $errors, array $warnings = []): self 
    {
        return new static(false, null, $errors, $warnings);
    }

    /**
     * Sets the warnings for the result.
     *
     * @param array $warnings The array of warnings.
     * 
     * @return self The updated instance of the BaseResultDRO.
     */
    final public function setWarnings(array $warnings): self
    {
        return new static(
            $this->success,
            $this->data,
            $this->errors,
            $warnings
        );
    }

    /**
     * Validates the data type.
     *
     * @param mixed $data The data to be validated.
     * 
     * @return void
     */
    private function validateDataType(mixed $data)
    {
        if (is_null($data) && !$this->success) {
            return;
        }
        
        if ($this->isValidDataType($data)) {
            return;
        }

        throw new Exception(sprintf(
            'Invalid data type for DRO %s',
            self::class
        ));
    }

    /**
     * Checks if the given data is of a valid data type.
     *
     * @param mixed $data The data to be checked.
     * 
     * @return bool Returns true if the data is of a valid data type, false otherwise.
     */
    abstract protected function isValidDataType($data): bool;


    /**
     * Generates data based on the provided arguments.
     *
     * @param mixed ...$data The data arguments.
     * 
     * @return mixed The generated data.
     */
    abstract protected static function generateComposedData(mixed ...$data): mixed;
}