<?php

namespace Tests;

use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
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
