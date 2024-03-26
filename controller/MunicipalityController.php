<?php

namespace controller;

require_once __DIR__ . '/../autoload.php';

use dao\impl\MunicipalityMySqlDao;
use model\Municipality;
use util\JsonResponse;
use validation\Request;

class MunicipalityController extends BaseController
{
    private $municipalityDao;

    public function __construct()
    {
        $this->municipalityDao = new MunicipalityMySqlDao();
    }

    public function createMunicipality()
    {
        $requestData = json_decode(file_get_contents('php://input'), true);

        Request::validate($requestData, Municipality::$rules);

        $municipality = new Municipality();
        $municipality->fillFromRequestData($requestData);

        $createdMunicipality = $this->municipalityDao->createMunicipality($municipality);

        if ($createdMunicipality) {
            JsonResponse::send(201, 'Municipio creado exitosamente', [$createdMunicipality->getJson()], 'MUNICIPALITY_INSERT_OK');
        } else {
            JsonResponse::send(500, 'Error al crear el municipio', [], 'MUNICIPALITY_INSERT_ERROR');
        }
    }

    public function readMunicipalityById()
    {
        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $municipality = $this->municipalityDao->readMunicipalityById($id);

        if ($municipality) {
            JsonResponse::send(200, 'Municipio obtenido exitosamente', [$municipality->getJson()], 'MUNICIPALITY_GET_OK');
        } else {
            JsonResponse::send(404, 'Municipio no encontrado', [], 'MUNICIPALITY_NOT_FOUND');
        }
    }

    public function updateMunicipality()
    {
        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $requestData = json_decode(file_get_contents('php://input'), true);

        $municipality = $this->municipalityDao->readMunicipalityById($id);

        $requestData = json_decode(file_get_contents('php://input'), true);

        Request::validate($requestData, Municipality::$rules);

        $municipality->fillFromRequestData($requestData);

        $updatedMunicipality = $this->municipalityDao->updateMunicipality($municipality);

        if ($updatedMunicipality) {
            JsonResponse::send(200, 'Municipio actualizado exitosamente', [$updatedMunicipality->getJson()], 'MUNICIPALITY_UPDATE_OK');
        } else {
            JsonResponse::send(500, 'Error al actualizar el municipio', [], 'MUNICIPALITY_UPDATE_ERROR');
        }
    }

    public function deleteMunicipality()
    {
        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $municipality = $this->municipalityDao->deleteMunicipality($id);

        if ($municipality) {
            JsonResponse::send(200, 'Municipio eliminado exitosamente', [$municipality->getJson()], 'MUNICIPALITY_DELETE_OK');
        } else {
            JsonResponse::send(404, 'Municipio no encontrado', [], 'MUNICIPALITY_NOT_FOUND');
        }
    }

    public function allGet()
    {
        $municipalities = $this->municipalityDao->allMunicipalities();

        JsonResponse::send(200, 'Lista de municipios obtenida exitosamente', $municipalities, 'MUNICIPALITIES_GET_OK');
    }
}
