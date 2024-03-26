<?php

namespace exception;

require_once __DIR__ . '/../autoload.php';

use exception\HttpException;
use util\JsonResponse;

class ExceptionHandler
{
    public static function handleException(\Throwable $exception)
    {
        $statusCode = $exception instanceof HttpException ? $exception->getStatusCode() : 500;

        JsonResponse::send($statusCode, $exception->getMessage(), [], 'ERROR');
        
        exit;
    }
}
