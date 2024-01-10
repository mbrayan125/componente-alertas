<?php

namespace Tests;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use DatabaseMigrations;

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    /**
     * Asserts that an array contains the expected values.
     *
     * @param array $expected The expected values.
     * @param array $actual The actual array to check.
     * 
     * @return void
     */
    protected function assertArrayContains(array $expected, array $actual): void
    {
        foreach ($expected as $key => $value) {
            $this->assertArrayHasKey($key, $actual, sprintf('Error asserting that the key "%s" exists in the array.', $key));
            $this->assertEquals($value, $actual[$key], sprintf('Error asserting that the key "%s" has the value "%s".', $key, $value));
        }
    }

    /**
     * Returns the relative resource path for a given resource path.
     *
     * @param string $resourcePath The resource path.
     * 
     * @return string The relative resource path.
     */
    protected function getResourcePath(string $resourcePath): string
    {
        return '/app/user-alerts-component/tests/Resources/' . $resourcePath;
    }
}
