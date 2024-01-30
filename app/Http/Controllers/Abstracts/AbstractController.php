<?php

namespace App\Http\Controllers\Abstracts;

use App\Traits\Controller\GetParamsFromRequestTrait;
use App\Traits\Response\CreateResponsesTrait;
use Laravel\Lumen\Routing\Controller;

abstract class AbstractController extends Controller
{
    use GetParamsFromRequestTrait;
    use CreateResponsesTrait;
}