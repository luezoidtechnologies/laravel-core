<?php

namespace Luezoid\Laravelcore\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Luezoid\Laravelcore\Constants\ErrorConstants;
use Luezoid\Laravelcore\Services\UtilityService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    protected $errorResponse;
    protected $statusCode;
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Throwable $exception
     * @return void
     * @throws Exception
     */
    public function report(\Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, \Throwable $exception)
    {
        if ($exception instanceof AppException) {
            $this->appExceptionHandler($exception);
        } else if ($exception instanceof ValidationException) {
            $this->validationExceptionHandler($exception);
        } else if ($exception instanceof NotFoundHttpException) {
            $this->notFoundHttpExceptionHandler($exception);
        } else if ($exception instanceof MethodNotAllowedHttpException) {
            $this->methodNotAllowedHttpExceptionHandler($exception);
        } else if ($exception instanceof InvalidCredentialsException) {
            $this->invalidCredentialsExceptionHandler($exception);
        } else if ($exception instanceof AuthorizationException) {
            $this->authorizationExceptionHandler($exception);
        } else if ($exception instanceof ForbiddenException) {
            $this->forbiddenExceptionHandler($exception);
        } else if ($exception instanceof ServiceNotImplementedException) {
            $this->serviceNotImplementedExceptionHandler($exception);
        } else if ($exception instanceof BusinessLogicException) {
            $this->businessLogicExceptionHandler($exception);
        } else if ($exception instanceof BadRequestHttpException) {
            $this->badRequestHttpExceptionHandler($exception);
        } else if ($exception instanceof ThrottleRequestsException) {
            $this->throttleRequestsExceptionHandler($exception);
        } else {
            $this->exceptionHandler($exception);
        }

        $this->handleLogTrace();

        return response()->json($this->errorResponse, $this->statusCode);
    }


    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Auth\AuthenticationException $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'));
    }

    public function appExceptionHandler($exception)
    {
        $this->statusCode = $exception->getCode();
        $this->errorResponse = [
            'error' => !empty($exception->getMessage()) ? $exception->getMessage() : "Internal Server Error",
            'type' => ErrorConstants::TYPE_INTERNAL_SERVER_ERROR,
            'errorDetails' => $exception->getTrace()
        ];
    }

    public function validationExceptionHandler($exception)
    {
        $errorDetails = [];
        $error = $exception->getMessage();
        if (UtilityService::is_json($exception->getMessage())) {
            $err = json_decode($exception->getMessage());
            $error = $err->error;
            $errorDetails = $err->errorDetails;
        }

        $this->statusCode = 400;
        $this->errorResponse = [
            'status' => 'fail',
            'message' => array_merge([$error], $errorDetails),
            'data' => null,
            'type' => ErrorConstants::TYPE_VALIDATION_ERROR
        ];
    }

    public function notFoundHttpExceptionHandler($exception)
    {
        $this->statusCode = 404;
        $this->errorResponse = [
            'error' => !empty($exception->getMessage()) ? $exception->getMessage() : "Resouce not found",
            'type' => ErrorConstants::TYPE_RESOURCE_NOT_FOUND_ERROR,
            'errorDetails' => "Resource not found"
        ];
    }

    public function methodNotAllowedHttpExceptionHandler($exception)
    {
        $this->statusCode = 405;
        $this->errorResponse = [
            'error' => !empty($exception->getMessage()) ? $exception->getMessage() : "Method not allowed",
            'type' => ErrorConstants::TYPE_METHOD_NOT_ALLOWED_ERROR,
            'errorDetails' => "Method not allowed"
        ];
    }

    public function invalidCredentialsExceptionHandler($exception)
    {
        $this->statusCode = 401;
        $this->errorResponse = [
            'error' => !empty($exception->getMessage()) ? $exception->getMessage() : "Invalid Credentials",
            'type' => ErrorConstants::TYPE_INVALID_CREDENTIALS_ERROR,
            'errorDetails' => $exception->getMessage()
        ];
    }

    public function authorizationExceptionHandler($exception)
    {
        $this->statusCode = 403;
        $this->errorResponse = [
            'error' => !empty($exception->getMessage()) ? $exception->getMessage() : "Authorization Failed",
            'type' => ErrorConstants::TYPE_AUTHORIZATION_ERROR,
            'errorDetails' => $exception->getMessage()
        ];
    }

    public function forbiddenExceptionHandler($exception)
    {
        $this->statusCode = 403;
        $this->errorResponse = [
            'error' => !empty($exception->getMessage()) ? $exception->getMessage() : "Forbidden from performing action",
            'type' => ErrorConstants::TYPE_FORBIDDEN_ERROR,
            'errorDetails' => $exception->getMessage()
        ];
    }

    public function serviceNotImplementedExceptionHandler($exception)
    {
        $this->statusCode = 501;
        $this->errorResponse = [
            'error' => !empty($exception->getMessage()) ? $exception->getMessage() : "Service Not Implemented",
            'type' => ErrorConstants::TYPE_SERVICE_NOT_IMPLEMENTED_ERROR,
            'errorDetails' => $exception->getMessage()
        ];
    }

    public function businessLogicExceptionHandler($exception)
    {
        $message = $exception->getMessage();
        if (UtilityService::is_json($exception->getMessage())) {
            $message = json_decode($exception->getMessage());
        }

        $this->statusCode = $exception->getCode();
        $this->errorResponse = [
            'error' => !empty($message) ? $message : "Business Logic Error",
            'errorDetails' => $exception->getMessage(),
            'type' => ErrorConstants::TYPE_BUSINESS_LOGIC_ERROR
        ];
    }

    public function badRequestHttpExceptionHandler($exception)
    {
        $this->statusCode = 400;
        $this->errorResponse = [
            'error' => !empty($exception->getMessage()) ? $exception->getMessage() : "Bad Request",
            'type' => ErrorConstants::TYPE_BAD_REQUEST_ERROR,
            'errorDetails' => "Bad Request"
        ];
    }

    public function throttleRequestsExceptionHandler($exception)
    {
        $this->statusCode = 429;
        $this->errorResponse = [
            'error' => !empty($exception->getMessage()) ? $exception->getMessage() : "Bad Request",
            'type' => ErrorConstants::TYPE_TOO_MANY_REQUEST_ERROR,
            'errorDetails' => "Too many requests"
        ];
    }

    public function exceptionHandler($exception)
    {
        $this->statusCode = 500;
        $this->errorResponse = [
            'error' => $exception->getMessage(),
            'type' => ErrorConstants::TYPE_INTERNAL_SERVER_ERROR,
            'errorDetails' => $exception->getTrace()
        ];
    }

    public function handleLogTrace()
    {
        if (!env('APP_DEBUG')) {
            unset($this->errorResponse['errorDetails']);
        }
    }
}
