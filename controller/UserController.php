<?php

namespace controller;

require_once __DIR__ . '/../autoload.php';

use validation\Request;
use controller\BaseController;
use dao\impl\UserMySqlDao;
use util\JsonResponse;
use dao\UserDao;
use model\User;

class UserController extends BaseController
{
    private UserDao $userDao;

    public function __construct()
    {
        $this->userDao = new UserMySqlDao();
    }

    public function all()
    {
        $users = $this->userDao->allUsers();
        JsonResponse::send(200, 'Listado de usuarios', $users, 'USERS_GET_OK', 200);
    }

    public function createPost()
    {
        $requestData = json_decode(file_get_contents('php://input'), true);

        Request::validate($requestData, User::$rules);

        $user = User::fillUserFromRequestData($requestData);

        $userCreated = $this->userDao->createUser($user);

        if ($userCreated) {
            JsonResponse::send(200, 'Registro satisfactorio', $userCreated->getJson(), 'USER_INSERT_OK', 201);
        } else {
            JsonResponse::send(500, 'Registro erróneo', [], 'USER_INSERT_ERROR', 500);
        }
    }

    public function readById()
    {
        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $userId = (int) $id;

        $user = $this->userDao->readUserById($userId);

        if ($user) {
            JsonResponse::send(200, 'Búsqueda satisfactoria', $user->getJson(), 'USER_GET_OK', 200);
        } else {
            JsonResponse::send(404, 'Usuario no encontrado', [], 'USER_GET_ERROR', 404);
        }
    }

    public function update()
    {
        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $requestData = json_decode(file_get_contents('php://input'), true);

        Request::validate($requestData, User::$rules);

        $user = User::fillUserFromRequestData($requestData);
        $user->setId($id);

        $userUpdated = $this->userDao->updateUser($user);

        if ($userUpdated) {
            JsonResponse::send(200, 'Usuario actualizado exitosamente', $userUpdated->getJson(), 'USER_UPDATE_OK', 200);
        } else {
            JsonResponse::send(500, 'Error al actualizar el usuario', [], 'USER_UPDATE_ERROR', 500);
        }
    }

    public function delete()
    {
        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $userDeleted = $this->userDao->deleteUser($id);

        if ($userDeleted) {
            JsonResponse::send(200, 'Usuario eliminado exitosamente', $userDeleted->getJson(), 'USER_DELETE_OK', 200);
        } else {
            JsonResponse::send(301, 'Error al eliminar el usuario', [], 'USER_DELETE_ERROR', 500);
        }
    }
}
