<?php

namespace controller;

use dao\LikeDao;
use dao\impl\LikeMySqlDao;
use model\Like;
use util\JsonResponse;

class LikeController
{
    private LikeDao $likeDao;

    public function __construct()
    {
        $this->likeDao = new LikeMySqlDao(); // Ajusta según tu implementación
    }

    public function createPost()
    {
        $requestData = json_decode(file_get_contents('php://input'), true);

        $like = Like::fillLikeFromRequestData($requestData);

        $createdLike = $this->likeDao->createLike($like);

        if ($createdLike) {
            JsonResponse::send(200, 'Like creado exitosamente', $createdLike->getJson(), 'LIKE_CREATE_OK', 201);
        } else {
            JsonResponse::send(500, 'Error al crear el Like', [], 'LIKE_CREATE_ERROR', 500);
        }
    }

    public function likesByUserGet()
    {
        $id = $_GET['id'];

        $likes = $this->likeDao->getLikesByUserId($id);

        JsonResponse::send(200, 'Like creado exitosamente', $likes, 'LIKE_USER_OK', 201);
    }

    public function readLikesByPublicationId(int $publicationId)
    {
        try {
            $likes = $this->likeDao->readLikesByPublicationId($publicationId);

            if ($likes) {
                $likesJson = array_map(function ($like) {
                    return $like->getJson();
                }, $likes);

                JsonResponse::send(200, 'Likes obtenidos exitosamente', $likesJson, 'LIKES_GET_OK', 200);
            } else {
                JsonResponse::send(404, 'No se encontraron Likes', [], 'LIKES_NOT_FOUND', 404);
            }
        } catch (\Exception $e) {
            JsonResponse::send(500, 'Error interno del servidor', [], 'INTERNAL_SERVER_ERROR', 500);
        }
    }

    // Puedes implementar otros métodos según sea necesario
}
