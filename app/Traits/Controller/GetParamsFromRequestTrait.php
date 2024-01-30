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
    public function getParamsFromRequest(Request $request, array $simpleParams, array $fileParams = [])
    {
        $errors = [];

        $params = [];
        $requestParams = $request->all();

        foreach ($simpleParams as $param => $restrictions) {
            $value = $requestParams[$param] ?? null;
            $errors = array_merge(
                $errors,
                $this->getRestrictionErrorsOnValue($param, $value, $restrictions)
            );
            $params[$param] = $value;
        }

        $tempDir = sys_get_temp_dir();
        foreach ($fileParams as $param => $restrictions) {
            $file = $request->file($param) ?? null;

            $restrictions[self::PARAM_TYPE_FILE] = true;
            $errors = array_merge(
                $errors,
                $this->getRestrictionErrorsOnValue($param, $file, $restrictions)
            );
            if (is_null($file)) {
                $params[$param] = null;
                continue;
            }
            $fileName = $file->getClientOriginalName();
            $file->move($tempDir, $fileName);
            $params[$param] = $tempDir . '/' . $fileName;
        }

        if (!empty($errors)) {
            throw new ValidationRequestException('Error en los datos de la petición', $errors);
        }

        return $params;
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
    private function getRestrictionErrorsOnValue(string $param, $value, array $restrictions): array {
        $errors = [];
        foreach ($restrictions as $restriction => $restrictionValue) {
            switch ($restriction) {

                case self::RESTRICTION_REQUIRED:
                    if (is_null($value) && $restrictionValue) {
                        $errors[] = "El parámetro '$param' es requerido";
                    }
                    break;

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
                    if (is_null($value)) {}
                    else if (!is_a($value, 'Illuminate\Http\UploadedFile')) {
                        $errors[] = "El parámetro '$param' debe ser un archivo";
                    }
                    else if (!$value->isValid()) {
                        $errors[] = "El parámetro '$param' debe ser un fichero válido";
                    }
                    break;
            }
        }
        return $errors;
    }
}
