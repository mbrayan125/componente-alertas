<?php

namespace Tests\UseCases\Process;

use App\Exceptions\Request\ValidationRequestException;
use App\Traits\Controller\GetParamsFromRequestTrait;
use Illuminate\Http\Request;
use Tests\TestCase;

class ValidateRequestParametersTest extends TestCase
{
    use GetParamsFromRequestTrait;

    /**
     * Test case for retrieving parameters from the request
     */
    public function test_retrieve_parameters_from_request_valid_params()
    {
        $paramDefinitions = [
            'param1' => [
                'type'      => 'string',
                'required'  => true,
                'maxLength' => 10
            ],
            'param2' => [
                'type'     => 'integer',
                'required' => true
            ],
            'param3' => [
                'type'     => 'boolean',
                'required' => true
            ],
            'param4' => [
                'required' => true,
                'array'    => ['key1', 'key2', 'key3']
            ]
        ];

        $request = $this->createMock(Request::class);
        $request->expects($this->exactly(count($paramDefinitions)))
            ->method('all')
            ->willReturn([
                'param1' => 'string',
                'param2' => 50,
                'param3' => true,
                'param4' => ['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3']
            ]);
        
        $params = $this->getParamsFromRequest($request, $paramDefinitions);

        $this->assertEquals([
            'param1' => 'string',
            'param2' => 50,
            'param3' => true,
            'param4' => ['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3']
        ], $params);
    }

    /**
     * Test case for retrieving parameters from a request with valid optional parameters.
     */
    public function test_retrieve_parameters_from_request_valid_params_optionals()
    {
        $paramDefinitions = [
            'param1' => [
                'type'      => 'string',
                'required'  => true,
                'maxLength' => 10
            ],
            'param2' => [
                'type'     => 'integer',
                'required' => false,
                'minValue' => 1,
                'maxValue' => 100
            ],
            'param3' => [
                'type'     => 'boolean',
                'required' => false
            ],
            'param4' => [
                'required' => true,
                'array'    => ['key1', 'key2', 'key3']
            ]
        ];

        $request = $this->createMock(Request::class);
        $request->expects($this->exactly(count($paramDefinitions)))
            ->method('all')
            ->willReturn([
                'param1' => 'string',
                'param4' => ['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3']
            ]);
        
        $params = $this->getParamsFromRequest($request, $paramDefinitions);

        $this->assertEquals([
            'param1' => 'string',
            'param2' => null,
            'param3' => null,
            'param4' => ['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3']
        ], $params);
    }

    /**
     * Test case for retrieving parameters from a request with invalid parameters.
     */
    public function test_retrieve_parameters_from_request_invalid_params()
    {
        $paramDefinitions = [
            'param1' => [
                'type'      => 'string',
                'required'  => true,
                'maxLength' => 10
            ],
            'param2' => [
                'type'     => 'integer',
                'required' => true
            ],
            'param3' => [
                'type'     => 'boolean',
                'required' => true
            ],
            'param4' => [
                'required' => true,
                'array'    => ['key1', 'key2', 'key3']
            ]
        ];

        $request = $this->createMock(Request::class);
        $request->expects($this->exactly(count($paramDefinitions)))
            ->method('all')
            ->willReturn([
                'param1' => 'stringmorethanlen',
                'param3' => 'true',
                'param4' => ['key1' => 'value1']
            ]);
        
        try {
            $this->getParamsFromRequest($request, $paramDefinitions);
            $this->fail('An exception should have been thrown');
        } catch (ValidationRequestException $e) {
            $this->assertEquals('Error en los datos de la petición', $e->mainMessage);
            $this->assertEquals([
                "El parámetro 'param1' no puede tener más de 10 caracteres",
                "El parámetro 'param2' es requerido",
                "El parámetro 'param4' debe incluír la clave 'key2'",
                "El parámetro 'param4' debe incluír la clave 'key3'",
            ], $e->errors);
        }

    }
}