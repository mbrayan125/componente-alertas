<?php

namespace Tests\UseCases\Process;

use App\Exceptions\General\FatalException;
use App\Traits\Response\CreateResponsesTrait;
use Tests\TestCase;

class CreateGenericResponseTest extends TestCase
{
    use CreateResponsesTrait;

    /**
     * Test case for creating a success response without warnings.
     *
     * This test verifies that the jsonSuccessResult method returns the expected response
     * with the provided status code, message, and array data. It also checks that the response
     * contains the correct status code, success flag, message, data, and empty warnings and errors.
     */
    public function test_create_success_response_without_warnings()
    {
        $statusCode = 200;
        $message = $this->faker->sentence;
        $arrayData = [
            'key1' => $this->faker->word,
            'key2' => $this->faker->word,
            'key3' => $this->faker->word
        ];

        $response = $this->jsonSuccessResult($statusCode, $message, $arrayData);
        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertEquals($statusCode, $response->getData()->status->statusCode);
        $this->assertTrue($response->getData()->status->success);
        $this->assertEquals($message, $response->getData()->message);
        $this->assertEquals($arrayData, (array)$response->getData()->data);
        $this->assertEmpty($response->getData()->status->warnings);
        $this->assertEmpty($response->getData()->status->errors);
    }

    /**
     * Test case for creating a success response with warnings.
     *
     * This test verifies that the jsonSuccessResult method returns the expected response
     * with the provided status code, message, data, and warnings.
     */
    public function test_create_success_response_with_warnings()
    {
        $statusCode = 200;
        $message = $this->faker->sentence;
        $arrayData = [
            'key1' => $this->faker->word,
            'key2' => $this->faker->word,
            'key3' => $this->faker->word
        ];
        $arrayWarnings = [
            $this->faker->sentence,
            $this->faker->sentence,
            $this->faker->sentence
        ];

        $response = $this->jsonSuccessResult($statusCode, $message, $arrayData, $arrayWarnings);
        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertEquals($statusCode, $response->getData()->status->statusCode);
        $this->assertTrue($response->getData()->status->success);
        $this->assertEquals($message, $response->getData()->message);
        $this->assertEquals($arrayData, (array)$response->getData()->data);
        $this->assertEquals($arrayWarnings, (array)$response->getData()->status->warnings);
        $this->assertEmpty($response->getData()->status->errors);
    }

    /**
     * Test case for creating a wrong success response exception.
     */
    public function test_create_wrong_success_response_exception()
    {
        $this->expectException(FatalException::class);
        $this->expectExceptionMessage('No es permitido retornar un código de estado diferente a 200, 201 en una respuesta exitosa.');
        $this->jsonSuccessResult(500, $this->faker->sentence);
    }

    /**
     * Test case for creating a failure response without warnings.
     *
     * This test verifies that the jsonFailureResult method returns the expected response when called with a specific status code, message, and array of errors.
     * It checks that the response has the correct status code, success status, message, errors, and empty warnings and data.
     */
    public function test_create_failure_response_without_warnings()
    {
        // Test setup
        $statusCode = 404;
        $message = $this->faker->sentence;
        $arrayErrors = [
            $this->faker->sentence,
            $this->faker->sentence,
            $this->faker->sentence
        ];

        // Perform the test
        $response = $this->jsonFailureResult($statusCode, $message, $arrayErrors);

        // Assertions
        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertEquals($statusCode, $response->getData()->status->statusCode);
        $this->assertFalse($response->getData()->status->success);
        $this->assertEquals($message, $response->getData()->message);
        $this->assertEquals($arrayErrors, (array)$response->getData()->status->errors);
        $this->assertEmpty((array)$response->getData()->status->warnings);
        $this->assertEmpty((array)$response->getData()->data);
    }

    /**
     * Test case for creating a failure response with warnings.
     *
     * This test verifies that the jsonFailureResult method returns the expected response when called with a specific status code, message, array of errors, and array of warnings.
     * It checks that the response has the correct status code, success status, message, errors, warnings, and empty data.
     */
    public function test_create_failure_response_with_warnings()
    {
        // Test setup
        $statusCode = 404;
        $message = $this->faker->sentence;
        $arrayErrors = [
            $this->faker->sentence,
            $this->faker->sentence,
            $this->faker->sentence
        ];
        $arrayWarnings = [
            $this->faker->sentence,
            $this->faker->sentence,
            $this->faker->sentence
        ];

        // Perform the test
        $response = $this->jsonFailureResult($statusCode, $message, $arrayErrors, $arrayWarnings);

        // Assertions
        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertEquals($statusCode, $response->getData()->status->statusCode);
        $this->assertFalse($response->getData()->status->success);
        $this->assertEquals($message, $response->getData()->message);
        $this->assertEquals($arrayErrors, (array)$response->getData()->status->errors);
        $this->assertEquals($arrayWarnings, (array)$response->getData()->status->warnings);
        $this->assertEmpty((array)$response->getData()->data);
    }

    /**
     * Test case for creating a wrong failure response exception.
     *
     * This test verifies that the jsonFailureResult method throws a FatalException when called with an invalid status code.
     * It checks that the exception message is correct.
     */
    public function test_create_wrong_failure_response_exception()
    {
        // Assertions
        $this->expectException(FatalException::class);
        $this->expectExceptionMessage('No es permitido retornar un código de estado diferente a 400, 404, 401, 422, 500 en una respuesta de error.');

        // Perform the test
        $this->jsonFailureResult(200, $this->faker->sentence);
    }
}