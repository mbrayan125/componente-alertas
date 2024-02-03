<?php

namespace App\Traits\Controller;

use App\Exceptions\Request\ValidationRequestException;
use Illuminate\Http\Request;

trait GetParamsFromRequestTrait
{
    use RestrictionsValidationTrait;

    /**
     * Retrieves the parameters from the request object.
     *
     * @param mixed $request The request object.
     * @param array $simpleParams The required parameters.
     * @param array $fileParams The required file parameters.
     * 
     * @return array The retrieved parameters.
     */
    public function getParamsFromRequest(Request $request, array $paramDefinitions)
    {
        $errors = [];
        $params = [];

        foreach ($paramDefinitions as $param => $restrictions) {
            $fileParam = $restrictions[self::PARAM_TYPE_FILE] ?? false;
            if ($fileParam) {
                $this->getFileParam($param, $request, $restrictions, $params, $errors);
                continue;
            }
            $this->getSimpleParam($param, $request, $restrictions, $params, $errors);
        }

        if (!empty($errors)) {
            throw new ValidationRequestException('Error en los datos de la petición', $errors);
        }

        return $params;
    }

    /**
     * Retrieves a simple parameter from the request and adds it to the params array.
     *
     * @param string $param The name of the parameter to retrieve.
     * @param Request $request The request object.
     * @param array $restrictions An array of restrictions to apply to the parameter value.
     * @param array &$params The array to store the retrieved parameter.
     * @param array &$errors The array to store any errors encountered during retrieval.
     * 
     * @return void
     */
    private function getSimpleParam(string $param, Request $request, array $restrictions, array &$params, array &$errors)
    {
        $requestParams = $request->all();
        $value = $requestParams[$param] ?? null;
        $errors = array_merge(
            $errors,
            $this->getRestrictionErrorsOnValue($param, $value, $restrictions)
        );
        $params[$param] = $value;
    }

    /**
     * Retrieves a file parameter from the request and performs validations.
     *
     * @param string $param The name of the file parameter.
     * @param Request $request The request object.
     * @param array $restrictions The restrictions to apply to the file parameter.
     * @param array &$params The array to store the parameter value.
     * @param array &$errors The array to store any validation errors.
     * 
     * @return void
     */
    private function getFileParam(string $param, Request $request, array $restrictions, array &$params, array &$errors)
    {
        $tempDir = sys_get_temp_dir();
        $file = $request->file($param) ?? null;
        $restrictions[self::PARAM_TYPE_FILE] = true;
        $errors = array_merge(
            $errors,
            $this->getRestrictionErrorsOnValue($param, $file, $restrictions)
        );
        if (is_null($file)) {
            $params[$param] = null;
            return;
        }
        $fileName = $file->getClientOriginalName();
        $file->move($tempDir, $fileName);
        $params[$param] = $tempDir . '/' . $fileName;
    }

    /**
     * Retrieves the restriction errors on a given value.
     *
     * @param string $param The parameter name.
     * @param mixed $value The value to check restrictions on.
     * @param array $restrictions The array of restrictions to apply.
     * 
     * @return array The array of restriction errors.
     */
    private function getRestrictionErrorsOnValue(string $param, &$value, array $restrictions): array 
    {
        $required = $restrictions[self::RESTRICTION_REQUIRED] ?? false;
        if ($required && is_null($value)) {
            return ["El parámetro '$param' es requerido"];
        }
        else if (!$required && is_null($value)) {
            return [];
        }

        $errors = [];
        foreach ($restrictions as $restriction => $restrictionValue) {
            switch ($restriction) {
                case self::RESTRICTION_MAX_LENGTH:
                    if (strlen($value) > $restrictionValue) {
                        $errors[] = "El parámetro '$param' no puede tener más de $restrictionValue caracteres";
                    }
                    break;
                case self::PARAM_TYPE_ARRAY:
                    if (is_array($value)) {
                        $valueKeys = array_keys($value);
                        foreach($restrictionValue as $mandatoryKey) {
                            if (!in_array($mandatoryKey, $valueKeys)) {
                                $errors[] = "El parámetro '$param' debe incluír la clave '$mandatoryKey'";
                            }
                        }
                    }
                    if (!is_array($value)) {
                        $errors[] = "El parámetro '$param' debe ser un array con las claves " . implode(', ', $restrictionValue);
                    }
                    break;
                case self::PARAM_TYPE_FILE:
                    if (!is_a($value, 'Illuminate\Http\UploadedFile')) {
                        $errors[] = "El parámetro '$param' debe ser un archivo";
                    }
                    else if (!$value->isValid()) {
                        $errors[] = "El parámetro '$param' debe ser un fichero válido";
                    }
                    break;
                case self::PARAM_TYPE_BOOL:
                    if (!is_bool($value)) {
                        $value = boolval($value);
                    }
                    break;
            }
        }
        return $errors;
    }
}
