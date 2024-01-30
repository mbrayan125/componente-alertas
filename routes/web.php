<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/targetsystem', 'CreateTargetSystemController');
$router->get('/targetsystem', 'GetTargetSystemController');

$router->post('/process', 'CreateProcessController');
$router->get('/process', 'GetProcessController');

$router->post('/instance', 'CreateInstanceController');
$router->get('/instance', 'GetInstanceController');