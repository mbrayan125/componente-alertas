<?php

namespace App\Traits\Controller;

use Illuminate\Http\Request;

trait GetParamsFromRequestTrait
{
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
        $params = [];
        $requestParams = $request->all();

        foreach ($simpleParams as $param) {
            $value = $requestParams[$param] ?? null;
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
}
