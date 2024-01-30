<?php

namespace App\Traits\Response;

use App\Exceptions\General\FatalException;
use Illuminate\Http\JsonResponse;

trait CreateResponsesTrait
{

    private const succesStatusCodes = [200, 201];
    private const failureStatusCodes = [400, 404, 401, 422, 500];

    /**
     * Generates a JSON success response.
     *
     * @param int $statusCode The HTTP status code.
     * @param string $message The success message.
     * @param array $data The data to include in the response (optional).
     * @param array $warnings The warnings to include in the response (optional).
     * 
     * @return JsonResponse The JSON response.
     */
    protected function jsonSuccessResult(
        int $statusCode, 
        string $message, 
        array $data = [], 
        array $warnings = []
    ): JsonResponse {
        if (!in_array($statusCode, self::succesStatusCodes)) {
            throw new FatalException(sprintf(
                'No es permitido retornar un código de estado diferente a %s en una respuesta exitosa.',
                implode(', ', self::succesStatusCodes)
            ));
        }

        return response()->json([
            'status' => [
                'success'    => true,
                'statusCode' => $statusCode,
                'warnings'   => $warnings,
                'errors'     => []
            ],
            'message' => $message,
            'data'    => $data
        ])->setStatusCode($statusCode);
    }


    /**
     * Generates a JSON failure response.
     *
     * @param int $statusCode The HTTP status code.
     * @param string $message The error message.
     * @param array $errors An array of error messages.
     * @param array $warnings An array of warning messages.
     * 
     * @return JsonResponse The JSON response.
     */
    protected function jsonFailureResult(
        int $statusCode, 
        string $message, 
        array $errors = [], 
        array $warnings = []
    ): JsonResponse {
        if (!in_array($statusCode, self::failureStatusCodes)) {
            throw new FatalException(sprintf(
                'No es permitido retornar un código de estado diferente a %s en una respuesta de error.',
                implode(', ', self::failureStatusCodes)
            ));
        }

        return response()->json([
            'status' => [
                'success'    => false,
                'statusCode' => $statusCode,
                'warnings'   => $warnings,
                'errors'     => $errors
            ],
            'message' => $message,
            'data'    => []
        ])->setStatusCode($statusCode);
    }
}

