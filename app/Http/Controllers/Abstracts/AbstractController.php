<?php

namespace App\Http\Controllers\Abstracts;

use App\Traits\Controller\GetParamsFromRequestTrait;
use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Routing\Controller;

abstract class AbstractController extends Controller
{
    use GetParamsFromRequestTrait;

    /**
     * Returns a JSON success result with the given data.
     *
     * @param string $message The message to be included in the response.
     * @param array $data The data to be included in the response.
     * 
     * @return void
     */
    protected function jsonSuccessResult(string $message, array $data = []): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data
        ]);
    }
}