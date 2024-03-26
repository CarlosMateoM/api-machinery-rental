<?php

namespace controller;

require_once __DIR__ . '/../autoload.php';

use validation\Request;
use controller\BaseController;
use dao\impl\MachineryMySqlDao;
use util\JsonResponse;
use dao\MachineryDao;
use model\Machinery;

class MachineryController extends BaseController
{
    private MachineryDao $machinaryDao;

    public function __construct()
    {
        $this->machinaryDao = new MachineryMySqlDao();
    }

    public function allGet()
    {
        $machinaries = $this->machinaryDao->allMachinaries();
        JsonResponse::send(200, 'Listado de maquinarias', $machinaries, 'MACHINERIES_GET_OK', 200);
    }

    public function createPost()
    {
        $requestData = json_decode(file_get_contents('php://input'), true);

        Request::validate($requestData, Machinery::$rules);

        $machinary = Machinery::fillMachinaryFromRequestData($requestData);

        $machinaryCreated = $this->machinaryDao->createMachinery($machinary);

        if ($machinaryCreated) {
            JsonResponse::send(200, 'Registro satisfactorio', $machinaryCreated->getJsonWithRelations(), 'MACHINERY_INSERT_OK', 201);
        } else {
            JsonResponse::send(500, 'Registro erróneo', [], 'MACHINERY_INSERT_ERROR', 500);
        }
    }

    public function readByIdGet()
    {
        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $machinaryId = (int) $id;

        $machinary = $this->machinaryDao->readMachineryById($machinaryId);

        if ($machinary) {
            JsonResponse::send(200, 'Búsqueda satisfactoria', $machinary->getJsonWithRelations(), 'MACHINERY_GET_OK', 200);
        } else {
            JsonResponse::send(404, 'Maquinaria no encontrada', [], 'MACHINERY_GET_ERROR', 404);
        }
    }

    public function update()
    {
        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $requestData = json_decode(file_get_contents('php://input'), true);

        Request::validate($requestData, Machinery::$rules);

        $machinary = Machinery::fillMachinaryFromRequestData($requestData);
        $machinary->setId($id);

        $machinaryUpdated = $this->machinaryDao->updateMachinery($machinary);

        if ($machinaryUpdated) {
            JsonResponse::send(200, 'Maquinaria actualizada exitosamente', [$machinaryUpdated->getJsonWithRelations()], 'MACHINERY_UPDATE_OK', 200);
        } else {
            JsonResponse::send(500, 'Error al actualizar la maquinaria', [], 'MACHINERY_UPDATE_ERROR', 500);
        }
    }

    public function delete()
    {
        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $machinaryDeleted = $this->machinaryDao->deleteMachinery($id);

        if ($machinaryDeleted) {
            JsonResponse::send(200, 'Maquinaria eliminada exitosamente', [$machinaryDeleted->getJsonWithRelations()], 'MACHINERY_DELETE_OK', 200);
        } else {
            JsonResponse::send(301, 'Error al eliminar la maquinaria', [], 'MACHINERY_DELETE_ERROR', 500);
        }
    }
}
