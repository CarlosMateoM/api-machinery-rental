<?php

namespace exception;

require_once __DIR__ . '/../autoload.php';

class HttpException extends \Exception
{
    private $statusCode;

    public function __construct($message, $statusCode)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

}
