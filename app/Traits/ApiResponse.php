<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

trait ApiResponse
{
    protected function success($data = null, string $message = null, int $status = 200): JsonResponse
    {
        if ($data instanceof JsonResource) {
            $data = $data->resolve();
        }

        $response = ['success' => true];
        if ($message) {
            $response['message'] = $message;
        }
        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }

    protected function error(string $message, int $status = 400, $errors = null): JsonResponse
    {
        $response = ['success' => false, 'message' => $message];
        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }
}
