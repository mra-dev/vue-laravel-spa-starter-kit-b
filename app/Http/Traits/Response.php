<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait Response
{

    public static function arr(bool $ok, string $message, array $data = [], $notify = null): array
    {
        return [
            'ok' => $ok,
            'message' => $message,
            'data' => $data,
            'notify' => $notify ?? ($ok ? 'positive' : 'negative')
        ];
    }


    public static function error(string $message, array $errors = [], $notify = 'info', $status = 200): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'errors' => $errors,
            'notify' => $notify
        ], $status);
    }

    public static function api(bool $ok, string $message, array $data = [], int $status = 200, array $headers = []): JsonResponse
    {
        $notify = $data['notify'] ?? null;
        unset($data['notify']);

        return response()->json(
            self::arr(
                $ok,
                $message,
                $data,
                $notify
            ),
            $status,
            $headers
        );
    }

}
