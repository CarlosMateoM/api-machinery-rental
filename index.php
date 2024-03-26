<?php

require_once __DIR__ . '/autoload.php';

use exception\ExceptionHandler;
use exception\HttpException;

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$routes = [
    'user' => '\\controller\\UserController',
    'auth' => '\\controller\\AuthController',
    'password' => '\\controller\\ResetPasswordController',
    'machinery' => '\\controller\\MachineryController',
    'publication' => '\\controller\\PublicationController',
    'photo' => '\\controller\\MachineryPhotoController',
    'category' => '\\controller\\CategoryController',
    'departament' => '\\controller\\DepartamentController',
    'municipality' => '\\controller\\MunicipalityController',
    'comment' => '\\controller\\CommentController',
    'like' => '\\controller\\LikeController',
    'requestMachinery' => '\\controller\\SendRequestPropertyController'
];

$request_uri = $_SERVER['REQUEST_URI'];

// Parsea la URL para obtener los parámetros
$queryString = parse_url($request_uri, PHP_URL_QUERY);
parse_str($queryString, $queryParams);

// Obtiene el nombre del controlador y la acción
$controllerName = isset($queryParams['controller']) ? $queryParams['controller'] : 'default';
$action = isset($queryParams['action']) ? $queryParams['action'] : 'index';

try {
    if (isset($routes[$controllerName])) {

        $controllerClass = $routes[$controllerName];
        $controllerInstance = new $controllerClass();
        // Construye el nombre del método con el sufijo del tipo de método
        $methodSuffix = $_SERVER['REQUEST_METHOD'];
        $method = $action . ucfirst(strtolower($methodSuffix)); // Por ejemplo, si es 'GET', será 'get'

        // Verifica si el método existe en el controlador
        if (method_exists($controllerInstance, $method)) {
            // Llama al método del controlador
            $controllerInstance->$method();

        } else {
            throw new HttpException('Acción no encontrada para el método HTTP específico', 404);
        }

    } else {
        throw new HttpException('Controlador no encontrado', 404);
    }

} catch (\Throwable $exception) {
    
    ExceptionHandler::handleException($exception);
}
