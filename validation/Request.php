<?php

namespace validation;

require_once './util/JsonResponse.php';
require_once './dao/ValidationDao.php';
require_once './dao/impl/ValidationMySqlDao.php';

use \dao\impl\ValidationMySqlDao;
use \util\JsonResponse;

class Request
{

    public static function validate($input, $rules)
    {
        $errors = [];

        foreach ($rules as $field => $rule) {
            $fieldRules = explode('|', $rule);

            foreach ($fieldRules as $fieldRule) {
                $ruleName = $fieldRule;

                if ($fieldRule === 'required' && empty($input[$field])) {
                    $errors[$field] = "El campo $field es obligatorio.";
                } elseif ($fieldRule === 'email' && !filter_var($input[$field], FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = "El campo $field debe ser una dirección de correo electrónico válida.";
                } elseif ($fieldRule === 'time' && !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $input[$field])) {
                    $errors[$field] = "El campo $field debe ser una hora válida en formato HH:MM:SS.";
                } elseif ($fieldRule === 'date' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $input[$field])) {
                    $errors[$field] = "El campo $field debe ser una fecha válida en formato AAAA-MM-DD.";
                } elseif ($fieldRule === 'string' && !is_string($input[$field])) {
                    $errors[$field] = "El campo $field debe ser una cadena de texto.";
                } elseif ($fieldRule === 'boolean' && !is_bool($input[$field])) {
                    $errors[$field] = "El campo $field debe ser un valor booleano (true o false).";
                } elseif ($fieldRule === 'numeric' && !is_numeric($input[$field])) {
                    $errors[$field] = "El campo $field debe ser un valor numérico.";
                }  elseif (strpos($fieldRule, 'exists:') === 0) {
                    $validationDao = new ValidationMySqlDao();
                    $tableAndColumn = substr($fieldRule, 7); // Elimina "exists:" de la regla
                    // Separar la tabla y la columna utilizando la coma como delimitador
                    list($table, $column) = explode(',', $tableAndColumn);
                    $id = $input[$field];
                    if (!$validationDao->existsInDatabase($table, $column, $id)) {
                        $errors[$field] = "El campo $field no existe en los registro.";
                    }
                }  elseif (strpos($fieldRule, 'unique:') === 0) {
                    $validationDao = new ValidationMySqlDao();
                    $tableAndColumn = substr($fieldRule, 7); // Elimina "exists:" de la regla
                    // Separar la tabla y la columna utilizando la coma como delimitador
                    list($table, $column) = explode(',', $tableAndColumn);
                    $key = $input[$field];
                    
                    if (!$validationDao->uniqueInDatabase($table, $column, $key)) {
                        $errors[$field] = "El campo $field existe en los registros.";
                    }
                }




            }
        }

        if (!empty($errors)) {
            // Si hay errores, envía una respuesta JSON con los errores y detén la ejecución del método en el controlador.
            JsonResponse::send(400, 'Error de validación', $errors, "VALIDATION_ERROR", 400);
            exit;
        }
    }
}
