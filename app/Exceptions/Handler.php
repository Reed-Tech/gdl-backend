<?php

namespace App\Exceptions;

use App\support\Responses\Codes;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        AuthenticationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        UnprocessableEntityException::class,
        ValidationException::class,
    ];

    /**
     * A list of the exception representing entities that are not found.
     *
     * @var array
     */

    protected $notFoundExceptions = [
        RecordNotFoundException::class,
        ModelNotFoundException::class,
        NotFoundHttpException::class
    ];

    /**
     * A list of the exception that has to do with user input.
     *
     * @var array
     */

    protected $clientInputExceptions = [
        \UnexpectedValueException::class,
        AuthorizationException::class,
        ValidationException::class
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
        if (app()->bound('sentry') && $this->shouldReport($exception)) {
            app('sentry')->captureException($exception);
        }

        parent::report($exception);
    }




    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param Throwable $exception
     * @return \Illuminate\Http\JsonResponse|Response
     * @throws AuthenticationException
     * @throws Throwable
     * @throws ValidationException
     */
    public function render($request, Throwable  $exception)
    {

        $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        # the status code
        $exceptionClass = get_class($exception);
        # we get the class name for the exception
        if (in_array($exceptionClass, $this->notFoundExceptions, true)) {
            $status = Response::HTTP_NOT_FOUND;
            $message = $exceptionClass === RecordNotFoundException::class ?
                $exception->getMessage() : 'route does not exist';
            # our response message
            $response = [
                'status' => $status,
                'title' => $message,
                'source' => array_merge($request->all(), ['path' => $request->getPathInfo()])
            ];

        } elseif ($exception instanceof MethodNotAllowedHttpException) {
            $status = Response::HTTP_METHOD_NOT_ALLOWED;
            $response = [
                'status' => $status,
                'title' => 'This method is not allowed for this endpoint.',
                'source' => array_merge($request->all(),
                    ['path' => $request->getPathInfo(), 'method' => $request->getMethod()])
            ];

        } elseif ($exception instanceof ValidationException) {

            $status = Response::HTTP_BAD_REQUEST;
            $response = [
                'status' => $status,
                'title' => 'Some validation errors were encountered while processing your request',
                'source' => [
                    'path' => $request->getPathInfo(),
                    'method' => $request->getMethod()
                ],
                'data' => $exception->response->original
            ];

        } elseif ($exception instanceof PermissionAccessDeniedException) {
            $status = Response::HTTP_FORBIDDEN;
            $response = [
                'status' => $status,
                'title' => $exception->getMessage(),
                'source' => array_merge($request->all(),
                    ['path' => $request->getPathInfo(), 'method' => $request->getMethod()])
            ];

        } elseif (in_array($exceptionClass, $this->clientInputExceptions, true)) {
            $status = Response::HTTP_BAD_REQUEST;
            $response = [
                'status' => $status,
                'title' => $exception->getMessage(),
                'source' => array_merge($request->all(),
                    ['path' => $request->getPathInfo(), 'method' => $request->getMethod()])
            ];

        } elseif ($exception instanceof DeletingFailedException) {
            $response = [
                'status' => $status,
                'title' => $exception->getMessage(),
                'source' => array_merge($request->all(),
                    ['path' => $request->getPathInfo(), 'method' => $request->getMethod()])
            ];

        } elseif ($exception instanceof UnprocessableEntityException) {
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
            $response = [
                'status' => $status,
                'title' => $exception->getMessage(),
                'source' => array_merge($request->all(),
                    ['path' => $request->getPathInfo(), 'method' => $request->getMethod()])
            ];

        } elseif ($exception instanceof UnauthorizedUserException) {
            $status = Response::HTTP_FORBIDDEN;
            $response = [
                'status' => $status,
                'title' => $exception->getMessage(),
                'source' => array_merge($request->all(),
                    ['path' => $request->getPathInfo(), 'method' => $request->getMethod()])
            ];

        } elseif ($exception instanceof AuthenticationException) {
            $status = Response::HTTP_UNAUTHORIZED;
            $response = [
                'status' => $status,
                'title' => $exception->getMessage(),
                'source' => array_merge($request->all(),
                    ['path' => $request->getPathInfo(), 'method' => $request->getMethod()])
            ];
        }else if($exception instanceof ResourceNotFoundException)
        {
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
            $response = [
                'status' => $status,
                'title' => $exception->getMessage(),
                'source' => array_merge($request->all(),
                    ['path' => $request->getPathInfo(), 'method' => $request->getMethod()])
            ];
        }

        else if($exception instanceof CustomValidationFailed)
        {
            $status = Response::HTTP_BAD_REQUEST;
            $response = [
                'status' => $status,
                'title' => $exception->getMessage(),
                'source' => array_merge($request->all(),
                    ['path' => $request->getPathInfo(), 'method' => $request->getMethod()])
            ];
        }
        else {
            $response = [
                'status' => $status,
                'title' => $exception->getMessage(),
            ];
        }
        if(app()->environment() === "testing")
        {
            throw $exception;
        }
        return response()->json(['errors' => [$response]], $status);
    }
}
