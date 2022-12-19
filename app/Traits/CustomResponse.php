<?php

namespace App\Traits;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait CustomResponse
{
    public function errorResponse(string $message, int $code = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        return $this->returnResponse($message, $code);
    }

    public function handleException(Exception $exception): JsonResponse
    {
        $message = $exception->getMessage();
        $code = $exception->getCode();
        $code = ($code < Response::HTTP_OK || $code > Response::HTTP_INTERNAL_SERVER_ERROR) ? Response::HTTP_INTERNAL_SERVER_ERROR : $code;

        if ($exception instanceof ModelNotFoundException) {
            $code = Response::HTTP_NOT_FOUND;
            $message = "Couldn't find what you were looking for.";
        }

        return $this->returnResponse($message, $code);
    }

    public function successResponse(string|array $message, object $data = null, int $code = Response::HTTP_OK): JsonResponse
    {
        return $this->returnResponse($message, $code, $data);
    }

    public function returnResponse(string|array $message, int $code, object $data = null): JsonResponse
    {
        $response = [
            "response" => $message,
        ];
        if ($data) {
            $response = array_merge($response, [
                "data" => $data,
            ]);
        }

        return response()->json($response, $code);
    }
}
