<?php

namespace App\Traits;

use Exception;
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
        $code = $exception->getCode();
        $code = ($code < Response::HTTP_OK || $code > Response::HTTP_INTERNAL_SERVER_ERROR) ? Response::HTTP_INTERNAL_SERVER_ERROR : $code;
        return $this->returnResponse($exception->getMessage(), $code);
    }

    public function successResponse(string|array $message, object $data = null, int $code = Response::HTTP_OK): JsonResponse
    {
        return $this->returnResponse($message, $code, $data);
    }

    public function returnResponse(string|array $message, int $code, object $data = null): JsonResponse
    {
        if ($data) {
            return response()->json([
                "response" => $message,
                "data" => $data,
            ], $code);
        }

        return response()->json([
            "response" => $message,
        ], $code);
    }
}
