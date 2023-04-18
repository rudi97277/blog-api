<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponser;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $e->validator->errors()->getMessages();

        return $this->errorResponse($errors, 422, 42200);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $this->errorResponse('Unauthenticated.', 401, 40100);
    }

    public function render($request, Throwable $e)
    {
        Log::error($request->all(), [
            'url' => $request->url(),
        ]);

        if ($e instanceof ValidationException) {
            Log::debug($e->validator->errors()->getMessages());
            return $this->convertValidationExceptionToResponse($e, $request);
        } else if ($e instanceof ModelNotFoundException) {
            $modelName = strtolower(class_basename($e->getModel()));
            return $this->errorResponse("Does not exists any {$modelName} with the specified identificator", 404, 40400);
        } else if ($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        } else if ($e instanceof AuthorizationException) {
            return $this->errorResponse($e->getMessage(), 403, 40300);
        } else if ($e instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse("The specified method for the request is invalid", 405, 40500);
        } else if ($e instanceof NotFoundHttpException) {
            return $this->errorResponse("The specified URL cannot be found", 404, 40400);
        } else if ($e instanceof HttpException) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode(), $e->getStatusCode() . 00);
        } else if ($e instanceof QueryException) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode == 1451) {
                return $this->errorResponse("Cannot remove this resource permanently. It is related with any other resource", 409, 40900);
            } else if ($errorCode == 1062) {
                return $this->errorResponse("Duplication attribute detected", 409, 40901);
            }
        }

        // only show the detailed error when in DEBUG mode
        if (config('app.debug')) {
            return parent::render($request, $e);
        }

        // show reserved error message when in PROD mode
        return $this->errorResponse("Unexpected Exception. Try later", 500, 50000);
    }

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function report(Throwable $e)
    {
        if ($this->shouldntReport($e)) {
            return;
        }

        if (!config('app.debug')) {
            Log::error(
                sprintf(
                    "\n\r%s: %s in %s:%d\n\r",
                    get_class($e),
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine(),
                )
            );
        }
    }
}
