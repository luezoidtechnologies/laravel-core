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

class Handler extends ExceptionHandler {
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
    public function report(\Throwable $exception) {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param \Throwable $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, \Throwable $exception) {
        $errorResponse = null;
        $statusCode = 500;

        if ($exception instanceof AppException) {
            $errorResponse = [
                'error' => !empty($exception->getMessage()) ? $exception->getMessage() : "Internal Server Error",
                'type' => ErrorConstants::TYPE_INTERNAL_SERVER_ERROR,
                'errorDetails' => $exception->getTrace()
            ];
            $statusCode = $exception->getCode();
        } else if ($exception instanceof ValidationException) {
            $errorDetails = [];
            $error = $exception->getMessage();
            if (UtilityService::is_json($exception->getMessage())) {
                $err = json_decode($exception->getMessage());
                $error = $err->error;
                $errorDetails = $err->errorDetails;
            }
            $errorResponse = [
                'status' => 'fail',
                'message' => array_merge([$error], $errorDetails),
                'data' => null,
                'type' => ErrorConstants::TYPE_VALIDATION_ERROR
            ];
            $statusCode = 400;
        } else if ($exception instanceof NotFoundHttpException) {
            $errorResponse = [
                'error' => !empty($exception->getMessage()) ? $exception->getMessage() : "Resouce not found",
                'type' => ErrorConstants::TYPE_RESOURCE_NOT_FOUND_ERROR,
                'errorDetails' => "Resource not found"
            ];
            $statusCode = 404;
        } else if ($exception instanceof MethodNotAllowedHttpException) {
            $errorResponse = [
                'error' => !empty($exception->getMessage()) ? $exception->getMessage() : "Method not allowed",
                'type' => ErrorConstants::TYPE_METHOD_NOT_ALLOWED_ERROR,
                'errorDetails' => "Method not allowed"
            ];
            $statusCode = 405;
        } else if ($exception instanceof InvalidCredentialsException) {
            $errorResponse = [
                'error' => !empty($exception->getMessage()) ? $exception->getMessage() : "Invalid Credentials",
                'type' => ErrorConstants::TYPE_INVALID_CREDENTIALS_ERROR,
                'errorDetails' => $exception->getMessage()
            ];
            $statusCode = 401;
        } else if ($exception instanceof AuthorizationException) {
            $errorResponse = [
                'error' => !empty($exception->getMessage()) ? $exception->getMessage() : "Authorization Failed",
                'type' => ErrorConstants::TYPE_AUTHORIZATION_ERROR,
                'errorDetails' => $exception->getMessage()
            ];
            $statusCode = 403;
        } else if ($exception instanceof ForbiddenException) {
            $errorResponse = [
                'error' => !empty($exception->getMessage()) ? $exception->getMessage() : "Forbidden from performing action",
                'type' => ErrorConstants::TYPE_FORBIDDEN_ERROR,
                'errorDetails' => $exception->getMessage()
            ];
            $statusCode = 403;
        } else if ($exception instanceof ServiceNotImplementedException) {
            $errorResponse = [
                'error' => !empty($exception->getMessage()) ? $exception->getMessage() : "Service Not Implemented",
                'type' => ErrorConstants::TYPE_SERVICE_NOT_IMPLEMENTED_ERROR,
                'errorDetails' => $exception->getMessage()
            ];
            $statusCode = 501;
        } else if ($exception instanceof BusinessLogicException) {
            $message = $exception->getMessage();
            if (UtilityService::is_json($exception->getMessage())) {
                $message = json_decode($exception->getMessage());
            };
            $errorResponse = [
                'error' => !empty($message) ? $message : "Business Logic Error",
                'errorDetails' => $exception->getMessage(),
                'type' => ErrorConstants::TYPE_BUSINESS_LOGIC_ERROR
            ];
            $statusCode = $exception->getCode();
        } else if ($exception instanceof BadRequestHttpException) {
            $errorResponse = [
                'error' => !empty($exception->getMessage()) ? $exception->getMessage() : "Bad Request",
                'type' => ErrorConstants::TYPE_BAD_REQUEST_ERROR,
                'errorDetails' => "Bad Request"
            ];
            $statusCode = 400;
        } else if ($exception instanceof ThrottleRequestsException) {
            $errorResponse = [
                'error' => !empty($exception->getMessage()) ? $exception->getMessage() : "Bad Request",
                'type' => ErrorConstants::TYPE_TOO_MANY_REQUEST_ERROR,
                'errorDetails' => "Too many requests"
            ];
            $statusCode = 429;
        } else {
            $errorResponse = [
                'error' => $exception->getMessage(),
                'type' => ErrorConstants::TYPE_INTERNAL_SERVER_ERROR,
                'errorDetails' => $exception->getTrace()
            ];
            $statusCode = 500;
        }

        if (!env('APP_DEBUG')) {
            unset($errorResponse['errorDetails']);
        }

        return response()->json($errorResponse, $statusCode);
    }


    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Auth\AuthenticationException $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'));
    }
}
