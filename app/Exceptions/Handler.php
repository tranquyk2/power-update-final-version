<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Throwable;

class Handler extends ExceptionHandler
{
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
        'password',
        'password_confirmation',
    ];

    public function report(Throwable $e): void
    {
        parent::report($e);
    }

    public function render($request, Throwable $e): Response|JsonResponse|RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        if ($request->is('api/*')) {
            $return_val =  $this->handleApiException($request, $e);
        } else {
            $return_val = parent::render($request, $e);
        }

        return $return_val;
    }

    private function handleApiException($request, Throwable $exception): JsonResponse
    {
        $exception = $this->prepareException($exception);


        if ($exception instanceof HttpResponseException) {
            $exception = $exception->getResponse();
        }

        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            $exception = $this->convertValidationExceptionToResponse($exception, $request);
        }

        return $this->customApiResponse($exception);
    }

    /**
     * Custom Api Response
     * @param $exception
     * @return JsonResponse
     */
    private function customApiResponse($exception): JsonResponse
    {
        if (method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception?->getStatusCode();
        } else {
            $statusCode = 500;
        }

        $response = [];
        $response['success'] = false;
        switch ($statusCode) {
            case 401:
                $response['message'] = 'Unauthorized';
                break;
            case 403:
                $response['message'] = 'Forbidden';
                break;
            case 404:
                $response['message'] = 'Not Found';
                break;
            case 405:
                $response['message'] = 'Method Not Allowed';
                break;
            case 422:
                $response['message'] = $exception->original['message'];
                $response['errors'] = $exception->original['errors'];
                break;
            default:
                $response['message'] = $exception?->getMessage();
                break;
        }
        $response['error_code'] = $exception?->getCode();
        $response['data'] = (object)[];
        if (config('app.debug')) {
            $response['data'] = $exception?->getTrace();
        }

        return response()->json($response, $statusCode);
    }
}
