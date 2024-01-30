<?php

namespace App\Exceptions;

use App\Exceptions\General\DomainException;
use App\Traits\Response\CreateResponsesTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use CreateResponsesTrait;

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
        DomainException::class
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param  Throwable  $exception
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof DomainException) {
            return $this->jsonFailureResult(
                $exception->httpCode,
                $exception->mainMessage,
                $exception->errors,
                $exception->warnings
            );
        }


        return parent::render($request, $exception);

        if (env('APP_DEBUG')) {
            return $this->jsonFailureResult(
                500,
                'Error desconocido',
                [
                    $exception->getMessage()
                ]
            );
        }

        return parent::render($request, $exception);
    }
}
