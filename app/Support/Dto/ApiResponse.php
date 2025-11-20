<?php

namespace App\Support\Dto;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    static function response(int|string $code, string $message, $data = null, string|null $type = null): JsonResponse
    {
        return response()->json([
            'code' => (int)$code,
            'type' => $type ?? request()->getMethod(),
            'message' => $message,
            'data' => $data,
        ]);
    }

    static function pagination($paginate): JsonResponse
    {
        return response()->json(ApiResponse::getPaginateResponse($paginate));
    }

    static function getPaginateResponse($paginate): array
    {
        return [
            'total' => $paginate['total'],
            'per_page' => $paginate['per_page'],
            'current_page' => $paginate['current_page'],
            'from' => $paginate['from'],
            'to' => $paginate['to'],
            'data' => $paginate['data'],
        ];
    }
}
