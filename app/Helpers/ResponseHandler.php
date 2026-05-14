<?php

use Illuminate\Http\Response;
use App\Exceptions\ApiException;
use Illuminate\Http\JsonResponse;
use App\Services\ErrorsLogService;

if (!function_exists('output')) {

    function output($data, $message = null, $resource = null, $messageResource = [], $statusCode = Response::HTTP_OK, $transable = true, $additionalData = null): JsonResponse
    {
        $model = null;

        if (!is_null($data)) {
            if (is_iterable($data) && $resource != null) {
                $model = $resource::collection($data);
            } else if (!is_iterable($data) && $resource != null) {
                $model = $resource::make($data);
            } else if ((!is_iterable($data) || is_iterable($data)) && $resource == null) {
                $model = $data;
            }
        }

        if (!empty($additionalData)) {
            $model += $additionalData;
        }

        if (isset($message))
            $message = $transable ? trans($message, $messageResource) : $message;
        else
            $message = Response::$statusTexts[$statusCode];

        $message = isset($message) ? transResource($message, $messageResource) : Response::$statusTexts[$statusCode];

        $response = [
            'status_code' => $statusCode,
            'message'     => $message,
            'data'        => $model
        ];

        return response()->json($response, $statusCode);
    }
}

if (!function_exists('outputWithPagination')) {

    function outputWithPagination($paginator, $message = null, $messageResource = [], $resourceCollection = null, int $statusCode = Response::HTTP_OK, $transable = true, $additionalData = null)
    {
        $model = $paginator;
        $paginateArray = $paginator->toArray();

        if (isset($resourceCollection)) {
            $paginationData = customizePaginationData($paginateArray);
            $model = [
                'data'            =>  $resourceCollection::collection($paginator),
                'pagination_data' => $paginationData
            ];
        } else {
            $paginationData = customizePaginationData($paginateArray);
            $model = [
                'data'            =>  $paginator->items(),
                'pagination_data' => $paginationData
            ];
        }

        if (!empty($additionalData)) {
            $model += $additionalData;
        }

        $messageParams = [];
        if ($messageResource != null) {
            $messageParams['resource']  = $messageResource;
        }

        if (isset($message))
            $message = $transable ? trans($message, $messageResource) : $message;
        else
            $message = Response::$statusTexts[$statusCode];

        $response = [
            'status_code' => $statusCode,
            'message' => $message,
            ...$model
        ];

        return response()->json($response, $statusCode);
    }
}

if (!function_exists('failure')) {

    /**
     * @param string|null $message The error message to be returned. If null, a default status text is used.
     * @param int $statusCode The HTTP status code for the response. Defaults to 500 (Internal Server Error).
     * @param array $messageResource Additional parameters for message translation, if applicable.
     * @param bool $transable Whether the message should be translatable. Defaults to true.
     * @param mixed|null $data Optional data to include in the exception.
     * @throws ApiException Always throws an ApiException with the given information.
     * @return JsonResponse This function does not return a response, as it throws an exception.
     */
    function failure($message = null, int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR, $messageResource = [], $transable = true, $data = null)
    {
        if (!$message) {
            $message = Response::$statusTexts[$statusCode];
        }

        if (isset($message))
        {
            $message = $transable ? trans($message, $messageResource) : $message;
            // dd($message , $transable , trans($message, $messageResource));
        }
        else
            $message = Response::$statusTexts[$statusCode];
        
        throw new ApiException($data, $message, $statusCode);
    }
}

if (!function_exists('success')) {

    /**
     * Generates a successful response with the given data, message and status code.
     * If $withPagination is true, the $data should be a paginator object and the output will be paginated.
     * If $withPagination is false, the output will be regular.
     *
     * @param mixed $data The data to be returned in the response.
     * @param string|null $message The message to be returned in the response. If null, a default status text is used.
     * @param string|object|null $apiResource The api resource class to be used to transform the data.
     * @param bool $withPagination Whether to paginate the data or not.
     * @param array $messageResource Additional parameters for message translation, if applicable.
     * @param int $statusCode The HTTP status code for the response. Defaults to 200 (OK).
     * @param bool $transable Whether the message should be translatable. Defaults to true.
     * @param mixed|null $additionalData Optional data to include in the exception.
     * @return JsonResponse The response containing the given information.
     */
    function success($data = null, $message = null, $apiResource = null, $withPagination = false, $messageResource = [], int $statusCode = Response::HTTP_OK, $transable = true, $additionalData = null): JsonResponse
    {
        if ($withPagination == false) {
            return output($data, $message, $apiResource, $messageResource, $statusCode, $transable, $additionalData);
        } else {
            return outputWithPagination($data, $message, $messageResource, $apiResource, $statusCode, $transable, $additionalData);
        }
    }
}

if (!function_exists('createdSuccess')) {
    /**
     * Generates a successful response with the given data, message and the status code set to 201 (Created).
     * If $apiResource is given, it will be used to transform the data.
     *
     * @param mixed $data The data to be returned in the response.
     * @param string|null $message The message to be returned in the response. If null, a default status text is used.
     * @param string|object|null $apiResource The api resource class to be used to transform the data.
     * @param array $messageResource Additional parameters for message translation, if applicable.
     * @return JsonResponse The response containing the given information.
     */
    function createdSuccess($data = null, $message = null, $apiResource = null, $messageResource = []): JsonResponse
    {
        return output($data, $message, $apiResource, $messageResource, Response::HTTP_CREATED);
    }
}

if (!function_exists('forbiddenFailure')) {
    function forbiddenFailure($data = null, $message = null, $messageResource = [])
    {
        return failure(
            message: $message,
            statusCode: Response::HTTP_FORBIDDEN,
            messageResource: $messageResource,
            data: $data,
        );
    }
}

if (!function_exists('lockedFailure')) {
    function lockedFailure($data = null, $message = null, $messageResource = [])
    {
        return failure(
            message: $message,
            statusCode: Response::HTTP_LOCKED,
            messageResource: $messageResource,
            data: $data,
        );
    }
}

if (!function_exists('notFoundFailure')) {
    function notFoundFailure($data = null, $message = null)
    {
        return failure(
            message: $message,
            statusCode: Response::HTTP_NOT_FOUND,
            data: $data
        );
    }
}

if (!function_exists('unauthorizedFailure')) {
    function unauthorizedFailure($data = null, $message = null)
    {
        return failure(
            message: $message,
            statusCode: Response::HTTP_UNAUTHORIZED,
            data: $data
        );
    }
}

if (!function_exists('notAllowedFailure')) {
    function notAllowedFailure($data = null, $message = null)
    {
        return failure(
            message: $message,
            statusCode: Response::HTTP_METHOD_NOT_ALLOWED,
            data: $data
        );
    }
}

if (!function_exists('unprocessableFailure')) {
    function unprocessableFailure($data = null, $message = null)
    {
        return failure(
            message: $message,
            statusCode: Response::HTTP_UNPROCESSABLE_ENTITY,
            data: $data
        );
    }
}

if (!function_exists('unavailableServiceFailure')) {
    function unavailableServiceFailure($data = null, $message = null)
    {
        return failure(
            message: $message,
            statusCode: Response::HTTP_SERVICE_UNAVAILABLE,
            data: $data
        );
    }
}

if (!function_exists('logException')) {
    function logException($e)
    {
        $errorLogService = new ErrorsLogService();
        $errorLogService->newError($e);
    }
}
