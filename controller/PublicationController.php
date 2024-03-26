<?php

namespace controller;

require_once __DIR__ . '/../autoload.php';

use validation\Request;
use controller\BaseController;
use dao\impl\PublicationMySqlDao;
use util\JsonResponse;
use dao\PublicationDao;
use model\Publication;

class PublicationController extends BaseController
{
    private PublicationDao $publicationDao;

    public function __construct()
    {
        $this->publicationDao = new PublicationMySqlDao();
    }

    public function allGet()
    {
        $publications = $this->publicationDao->allPublications();
        JsonResponse::send(200, 'Listado de publicaciones', $publications, 'PUBLICATIONS_GET_OK', 200);
    }

    public function searchGet()
    {
        $name = $_GET['name'];
        $publications = $this->publicationDao->searchPublicationsByName($name);
        JsonResponse::send(200, 'Listado de publicaciones', $publications, 'PUBLICATIONS_GET_OK', 200);
    }

    public function createPost()
    {
        $requestData = json_decode(file_get_contents('php://input'), true);

        Request::validate($requestData, Publication::$rules);

        $publication = Publication::fillPublicationFromRequestData($requestData);

        $publicationCreated = $this->publicationDao->createPublication($publication);

        if ($publicationCreated) {
            JsonResponse::send(200, 'Registro satisfactorio', [$publicationCreated->getJson()], 'PUBLICATION_INSERT_OK', 201);
        } else {
            JsonResponse::send(500, 'Registro erróneo', [], 'PUBLICATION_INSERT_ERROR', 500);
        }
    }

    public function readByIdGet()
    {
        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $publicationId = (int) $id;

        $publication = $this->publicationDao->readPublicationById($publicationId);

        if ($publication) {
            JsonResponse::send(200, 'Búsqueda satisfactoria', $publication->getJsonWithRelations(), 'PUBLICATION_GET_OK', 200);
        } else {
            JsonResponse::send(404, 'Publicación no encontrada', [], 'PUBLICATION_GET_ERROR', 404);
        }
    }

    public function update()
    {
        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $requestData = json_decode(file_get_contents('php://input'), true);

        Request::validate($requestData, Publication::$rules);

        $publication = Publication::fillPublicationFromRequestData($requestData);
        $publication->setId($id);

        $publicationUpdated = $this->publicationDao->updatePublication($publication);

        if ($publicationUpdated) {
            JsonResponse::send(200, 'Publicación actualizada exitosamente', [$publicationUpdated->getJson()], 'PUBLICATION_UPDATE_OK', 200);
        } else {
            JsonResponse::send(500, 'Error al actualizar la publicación', [], 'PUBLICATION_UPDATE_ERROR', 500);
        }
    }

    public function delete()
    {
        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $publicationDeleted = $this->publicationDao->deletePublication($id);

        if ($publicationDeleted) {
            JsonResponse::send(200, 'Publicación eliminada exitosamente', [$publicationDeleted->getJson()], 'PUBLICATION_DELETE_OK', 200);
        } else {
            JsonResponse::send(301, 'Error al eliminar la publicación', [], 'PUBLICATION_DELETE_ERROR', 500);
        }
    }
}
