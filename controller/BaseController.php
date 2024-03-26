<?php

namespace controller;

require_once 'util/JsonResponse.php';

use util\JsonResponse;

class BaseController
{
    protected function validateIdParameter($id)
    {
        if (!isset($id) || !is_numeric($id) || $id <= 0) {
            JsonResponse::send(422, "El parámetro 'id' es obligatorio y debe ser un número entero positivo", [], 'UNIT_GET_ERROR', 422);
            die();
        }
    }
}