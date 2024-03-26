<?php

namespace controller;

require_once __DIR__ . '/../autoload.php';

use \dao\UserDao;
use \dao\impl\UserMySqlDao;
use \util\Mailer;
use \util\JsonResponse;
use \validation\Request;

class SendRequestPropertyController extends BaseController
{

    private UserDao $userDao;

    public function __construct()
    {
        $this->userDao = new UserMySqlDao();
    }

    public function sendRequestPropertyPost()
    {
        $requestData = json_decode(file_get_contents('php://input'), true);

        $rules = [
            'user_id' => 'required',
            'property_id' => 'required',
            'maquinaria' => 'required',
            'horas' => 'required'
        ];

        Request::validate($requestData, $rules);

        $user = $this->userDao->readUserById($requestData['user_id']);
        $property = $this->userDao->readUserById($requestData['property_id']);

        if ($user) {

            $subject = 'Solicitud de alquiler';

            $message = "Hola {$property->getNames()},\n\n";
            $message .= "Usted ha recibido una solicitud de alquiler por parte de {$user->getNames()}. ";
            $message .= "interesado en la maquinaria {$requestData['maquinaria']} por {$requestData['horas']} horas.\n\n";
            $message .= "le agradecemos responder esta solicitud comunicandose al correo: {$user->getEmail()}\n";

            if (Mailer::sendMail($property->getEmail(), $subject, $message)) {
                JsonResponse::send(200, 'Solicitud enviada', [], 'REQUEST_EMAIL_SENT', 200);
            }
        } else {
            JsonResponse::send(404, 'Usuario no encontrado', [], 'USER_NOT_FOUND', 404);
        }
    }
}
