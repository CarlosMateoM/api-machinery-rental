<?php

namespace util;

class JsonResponse
{
    public static function send($code, $message, $data, $status, $statusCode = 200)
    {
        http_response_code($statusCode);
        header("Content-Type: application/json");

        $response = array(
            'code' => $code,
            'message' => $message,
            'data' => $data,
            'status' => $status,
        );

        echo json_encode($response);
    }
}
