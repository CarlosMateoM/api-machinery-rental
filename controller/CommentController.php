<?php

namespace controller;

require_once __DIR__ . '/../autoload.php';

use dao\impl\CommentMySqlDao;
use dao\CommentDao;
use model\Comment;
use util\JsonResponse;
use validation\Request;

class CommentController extends BaseController
{
    private CommentDao $commentDao;

    public function __construct()
    {
        $this->commentDao = new CommentMySqlDao();
    }

    public function createPost()
    {
        $requestData = json_decode(file_get_contents('php://input'), true);

        
        Request::validate($requestData, Comment::$rules);

        $comment = Comment::fillCommentFromRequestData($requestData);

        $createdComment = $this->commentDao->createComment($comment);

        if ($createdComment) {
            JsonResponse::send(201, 'Comentario creado exitosamente', [$createdComment->getJson()], 'COMMENT_CREATE_OK', 201);
        } else {
            JsonResponse::send(500, 'Error al crear el comentario', [], 'COMMENT_CREATE_ERROR', 500);
        }
    }

    public function getCommentById()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            JsonResponse::send(400, 'ID de comentario no proporcionado', [], 'COMMENT_MISSING_ID', 400);
        }

        $comment = $this->commentDao->readCommentById($id);

        if ($comment) {
            JsonResponse::send(200, 'Comentario encontrado', $comment->getJsonWithRelations(), 'COMMENT_GET_OK', 200);
        } else {
            JsonResponse::send(404, 'Comentario no encontrado', [], 'COMMENT_NOT_FOUND', 404);
        }
    }

    public function updateComment()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            JsonResponse::send(400, 'ID de comentario no proporcionado', [], 'COMMENT_MISSING_ID', 400);
        }

        $requestData = json_decode(file_get_contents('php://input'), true);

        // Realiza la validación de los datos recibidos si es necesario
        // Request::validate($requestData, Comment::$rules);

        $comment = Comment::fillCommentFromRequestData($requestData);
        $comment->setId($id);

        $updatedComment = $this->commentDao->updateComment($comment);

        if ($updatedComment) {
            JsonResponse::send(200, 'Comentario actualizado exitosamente', [$updatedComment->getJson()], 'COMMENT_UPDATE_OK', 200);
        } else {
            JsonResponse::send(500, 'Error al actualizar el comentario', [], 'COMMENT_UPDATE_ERROR', 500);
        }
    }

    public function deleteComment()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            JsonResponse::send(400, 'ID de comentario no proporcionado', [], 'COMMENT_MISSING_ID', 400);
        }

        $deletedComment = $this->commentDao->deleteComment($id);

        if ($deletedComment) {
            JsonResponse::send(200, 'Comentario eliminado exitosamente', [$deletedComment->getJson()], 'COMMENT_DELETE_OK', 200);
        } else {
            JsonResponse::send(404, 'Comentario no encontrado', [], 'COMMENT_DELETE_ERROR', 404);
        }
    }

    public function getCommentsByPublicationIdGet()
    {
        $publicationId = $_GET['id'] ?? null;

        $this->validateIdParameter($publicationId);

        $comments = $this->commentDao->getCommentsByPublicationId((int) $publicationId);

        JsonResponse::send(200, 'Listado de comentarios', $comments, 'COMMENT_GET_OK', 200);
    }
}
