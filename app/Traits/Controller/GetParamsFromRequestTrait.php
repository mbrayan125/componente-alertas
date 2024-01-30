<?php

namespace App\Traits\Controller;

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
            if (is_null($value)) {
                continue;
            }
            $params[$param] = $value;
        }

        $tempDir = sys_get_temp_dir();
        foreach ($fileParams as $param) {
            $file = $request->file($param) ?? null;
            if (is_null($value)) {
                continue;
            }
            $fileName = $file->getClientOriginalName();
            $file->move($tempDir, $fileName);
            $params[$param] = $tempDir . '/' . $fileName;
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
            }
        }
        return $errors;
    }
}
