<?php

namespace Luezoid\Laravelcore\Exceptions;

use Luezoid\Laravelcore\Constants\ErrorConstants;
use Luezoid\Laravelcore\Services\UtilityService;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Handler extends ExceptionHandler
{
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
     * @param  \Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        $response = null;

        if ($exception instanceof AppException) {

            $response = response()->json([
                'error' => !empty($exception->getMessage()) ? $exception->getMessage() : "Internal Server Error",
                'type' => ErrorConstants::TYPE_INTERNAL_SERVER_ERROR,
                'errorDetails' => $exception->getTrace()
            ], $exception->getCode());

        } else if ($exception instanceof ValidationException) {
            $errorDetails = [];
            $error = $exception->getMessage();
            if (UtilityService::is_json($exception->getMessage())) {
                $err = json_decode($exception->getMessage());
                $error = $err->error;
                $errorDetails = $err->errorDetails;
            }
            $response = response()->json([
                'status' => 'fail',
                'message' => array_merge([$error], $errorDetails),
                'data' => null,
                'type' => ErrorConstants::TYPE_VALIDATION_ERROR
            ], 400);
        } else if ($exception instanceof NotFoundHttpException) {
            $response = response()->json([
                'error' => !empty($exception->getMessage()) ? $exception->getMessage() : "Resouce not found",
                'type' => ErrorConstants::TYPE_RESOURCE_NOT_FOUND_ERROR,
                'errorDetails' => "Resource not found"
            ], 404);
        } else if ($exception instanceof MethodNotAllowedHttpException) {
            $response = response()->json([
                'error' => !empty($exception->getMessage()) ? $exception->getMessage() : "Method not allowed",
                'type' => ErrorConstants::TYPE_METHOD_NOT_ALLOWED_ERROR,
                'errorDetails' => "Method not allowed"
            ], 405);
        } else if ($exception instanceof InvalidCredentialsException) {
            $response = response()->json([
                'error' => !empty($exception->getMessage()) ? $exception->getMessage() : "Invalid Credentials",
                'type' => ErrorConstants::TYPE_INVALID_CREDENTIALS_ERROR,
                'errorDetails' => $exception->getMessage()
            ], 401);
        } else if ($exception instanceof AuthorizationException) {
            $response = response()->json([
                'error' => !empty($exception->getMessage()) ? $exception->getMessage() : "Authorization Failed",
                'type' => ErrorConstants::TYPE_AUTHORIZATION_ERROR,
                'errorDetails' => $exception->getMessage()
            ], 403);
        } else if ($exception instanceof ForbiddenException) {
            $response = response()->json([
                'error' => !empty($exception->getMessage()) ? $exception->getMessage() : "Forbidden from performing action",
                'type' => ErrorConstants::TYPE_FORBIDDEN_ERROR,
                'errorDetails' => $exception->getMessage()
            ], 403);
        } else if ($exception instanceof ServiceNotImplementedException) {
            $response = response()->json([
                'error' => !empty($exception->getMessage()) ? $exception->getMessage() : "Service Not Implemented",
                'type' => ErrorConstants::TYPE_SERVICE_NOT_IMPLEMENTED_ERROR,
                'errorDetails' => $exception->getMessage()
            ], 501);
        } else if ($exception instanceof BusinessLogicException) {
            $message = $exception->getMessage();
            if (UtilityService::is_json($exception->getMessage())) {
                $message = json_decode($exception->getMessage());
            };
            $response = response()->json([
                'error' => !empty($message) ? $message : "Business Logic Error",
                'errorDetails' => $exception->getMessage(),
                'type' => ErrorConstants::TYPE_BUSINESS_LOGIC_ERROR
            ], $exception->getCode());
        } else if ($exception instanceof BadRequestHttpException) {
            $response = response()->json([
                'error' => !empty($exception->getMessage()) ? $exception->getMessage() : "Bad Request",
                'type' => ErrorConstants::TYPE_BAD_REQUEST_ERROR,
                'errorDetails' => "Bad Request"
            ], 400);
        } else if ($exception instanceof ThrottleRequestsException) {
            $response = response()->json([
                'error' => !empty($exception->getMessage()) ? $exception->getMessage() : "Bad Request",
                'type' => ErrorConstants::TYPE_TOO_MANY_REQUEST_ERROR,
                'errorDetails' => "Too many requests"
            ], 429);
        } else {
            $response =   $response = response()->json([
                'error' => $exception->getMessage(),
                'type' => ErrorConstants::TYPE_INTERNAL_SERVER_ERROR,
                'errorDetails' => $exception->getTrace()
            ], 500);
        }
        return $response;
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
