<?php

namespace controller;

require_once __DIR__ . '/../autoload.php';

use validation\Request;
use controller\BaseController;
use dao\impl\DepartamentMySqlDao;
use util\JsonResponse;
use dao\DepartamentDao;
use model\Departament;

class DepartamentController extends BaseController
{
    private DepartamentDao $departamentDao;

    public function __construct()
    {
        $this->departamentDao = new DepartamentMySqlDao();
    }

    public function allGet()
    {
        $departaments = $this->departamentDao->allDepartaments();
        JsonResponse::send(200, 'Listado de departamentos', $departaments, 'DEPARTAMENTS_GET_OK', 200);
    }

    public function createPost()
    {
        $requestData = json_decode(file_get_contents('php://input'), true);

        Request::validate($requestData, Departament::$rules);

        $departament = new Departament();
        $departament->fillDepartamentFromRequestData($requestData);

        $departamentCreated = $this->departamentDao->createDepartament($departament);

        if ($departamentCreated) {
            JsonResponse::send(200, 'Departamento creado exitosamente', [$departamentCreated->getJson()], 'DEPARTMENT_INSERT_OK', 201);
        } else {
            JsonResponse::send(500, 'Error al crear el departamento', [], 'DEPARTMENT_INSERT_ERROR', 500);
        }
    }

    public function readByIdGet()
    {
        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $departamentId = (int) $id;

        $departament = $this->departamentDao->readDepartamentById($departamentId);

        if ($departament) {
            JsonResponse::send(200, 'Búsqueda satisfactoria', $departament->getJsonWithMunipalities(), 'DEPARTMENT_GET_OK', 200);
        } else {
            JsonResponse::send(404, 'Departamento no encontrado', [], 'DEPARTMENT_GET_ERROR', 404);
        }
    }

    public function update()
    {
        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $requestData = json_decode(file_get_contents('php://input'), true);

        Request::validate($requestData, Departament::$rules);

        $departament = new Departament();
        $departament->fillDepartamentFromRequestData($requestData);
        $departament->setId($id);

        $departamentUpdated = $this->departamentDao->updateDepartament($departament);

        if ($departamentUpdated) {
            JsonResponse::send(200, 'Departamento actualizado exitosamente', [$departamentUpdated->getJson()], 'DEPARTMENT_UPDATE_OK', 200);
        } else {
            JsonResponse::send(500, 'Error al actualizar el departamento', [], 'DEPARTMENT_UPDATE_ERROR', 500);
        }
    }

    public function delete()
    {
        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $departamentDeleted = $this->departamentDao->deleteDepartament($id);

        if ($departamentDeleted) {
            JsonResponse::send(200, 'Departamento eliminado exitosamente', [$departamentDeleted->getJson()], 'DEPARTMENT_DELETE_OK', 200);
        } else {
            JsonResponse::send(301, 'Error al eliminar el departamento', [], 'DEPARTMENT_DELETE_ERROR', 500);
        }
    }
}
