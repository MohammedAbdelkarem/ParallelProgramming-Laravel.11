<?php

namespace App\Exceptions;

use App\Constants\ExceptionMessages;
use Exception;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Exceptions\ApiException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */

    protected $dontReport = [
        ApiException::class,
    ];
    

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        DB::rollBack();
        $code =  $e->getCode();
        $msg  =  $e->getTrace();
        $msg = "{$e->getMessage()}";       //TODO DELETE
        // $msg = "{$e->getMessage()}{$e->getTraceAsString()}";       //TODO DELETE
        $data  =  null;

        if ($e instanceof ApiException) {
            $code   = $e->getStatusCode();
            $msg    = $e->getMessage();
            $data   = $e->getData();
        }
        if ($e instanceof AuthorizationException) {
            $msg = trans(ExceptionMessages::MSG_NOT_ALLOWED);
            $code = 403;
        }
        if ($e instanceof ValidationException) {
            $msg = $e->validator->errors()->first();
            $code = 400;
        } elseif ($e instanceof NotFoundHttpException) {
            $code = 404;
            $msg = trans(ExceptionMessages::MSG_URL_NOT_FOUND);
        } elseif ($e instanceof AuthenticationException) {
            $code = 401;
            $msg = trans(ExceptionMessages::MSG_NOT_AUTHENTICATED);
        } elseif ($e instanceof MethodNotAllowedHttpException) {
            $code = 405;
            $msg = trans(ExceptionMessages::MSG_METHOD_NOT_ALLOWED);
        } elseif ($e instanceof ModelNotFoundException) {
            $code = 404;
            $msg = trans(ExceptionMessages::MSG_ITEM_NOT_FOUND);
        } elseif ($e instanceof ThrottleRequestsException) {
            $code = 400;
            $msg = trans(ExceptionMessages::MSG_TOO_MANY_REQUESTS);
        } elseif ($e instanceof ServiceUnavailableHttpException) {
            $code = 400;
            $msg = trans(ExceptionMessages::MSG_SERVICE_UNAVAILABLE);
        } elseif ($e instanceof Exception) {
            // $code = 500;
            // $msg = 'somthing went wrong'; //TODO Uncomment
        }

        if (!$code || $code > 599 || $code <= 0 || gettype($code) !== "integer") {
            $code = 500;
        }

        return response()->json([
            'status_code' => $code,
            'message' => $msg,
            'data' => $data,
        ], $code);
    }
}
