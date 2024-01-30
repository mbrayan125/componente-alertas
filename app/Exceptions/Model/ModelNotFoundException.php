<?php

namespace App\Exceptions\Model;

use App\Exceptions\General\DomainException;

class ModelNotFoundException extends DomainException
{
    
    /**
     * Exception thrown when a model is not found.
     *
     * @param string $model The name of the model.
     * @param array $params The parameters used to search for the model.
     */
    public function __construct(
        string $model,
        array $params
    ) {
        $preparedParams = [];
        foreach ($params as $key => $value) {
            $preparedParams[] = sprintf('%s %s', $key, $value);
        }

        $error = sprintf(
            'No existe ningún %s con %s',
            $model,
            implode(', ', $preparedParams)
        );

        parent::__construct(sprintf('%s no encontrado', ucfirst($model)), [$error], [], self::HTTP_NOT_FOUND);
    }
}