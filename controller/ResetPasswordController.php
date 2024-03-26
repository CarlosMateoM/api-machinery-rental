<?php

namespace controller;

require_once __DIR__ . '/../autoload.php';

use util\Mailer;
use validation\Request;
use controller\BaseController;
use dao\impl\AuthMySqlDao;
use util\JsonResponse;
use dao\AuthDao;
use model\User;

class ResetPasswordController extends BaseController
{
    private AuthDao $authDao;

    public function __construct()
    {
        $this->authDao = new AuthMySqlDao();
    }

    public function sendPasswordResetLinkPost()
    {
        $requestData = json_decode(file_get_contents('php://input'), true);

        $rules = [
            'email' => 'required|email',
        ];

        Request::validate($requestData, $rules);

        $user = $this->authDao->getUserByEmail($requestData['email']);

        if ($user) {
            // Genera un token único (puedes utilizar funciones más seguras)
            $resetToken = bin2hex(random_bytes(32));

            // Asocia el token al usuario en la base de datos
            $this->authDao->setResetToken($user->getId(), $resetToken);

            // Envía el correo electrónico con el enlace para restablecer la contraseña
            $resetLink = "http://localhost:5173/resetPassword?email={$user->getEmail()}&token=$resetToken";

            $subject = 'Restablecimiento de Contraseña';
            $message = "Hola {$user->getNames()},\n\n";
            $message .= "Hemos recibido una solicitud para restablecer tu contraseña. ";
            $message .= "Si no hiciste esta solicitud, puedes ignorar este correo electrónico.\n\n";
            $message .= "Para restablecer tu contraseña, haz clic en el siguiente enlace:\n";
            $message .= $resetLink;



            //$to = 'carlosmateo484@gmail.com';

            if (Mailer::sendMail($user->getEmail(), $subject, $message)) {
                JsonResponse::send(200, 'Correo electrónico de restablecimiento de contraseña enviado', [], 'RESET_PASSWORD_EMAIL_SENT', 200);
            }
        } else {
            JsonResponse::send(404, 'Usuario no encontrado', [], 'USER_NOT_FOUND', 404);
        }
    }

    public function resetPasswordPost()
    {
        $requestData = json_decode(file_get_contents('php://input'), true);

        $rules = [
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string',
        ];

        Request::validate($requestData, $rules);

        $user = $this->authDao->getUserByEmail($requestData['email']);

        if ($user) {
            
            if ($requestData['token'] === $user->getResetToken()) {
                // Actualizar la contraseña del usuario
                $hashedPassword = password_hash($requestData['password'], PASSWORD_DEFAULT);
                $this->authDao->updatePassword($user->getId(), $hashedPassword);

                // Eliminar el token de restablecimiento de la base de datos (ya no es necesario)
                $this->authDao->removeResetToken($user->getId());

                JsonResponse::send(200, 'Contraseña restablecida con éxito', [], 'PASSWORD_RESET_SUCCESS', 200);
            } else {
                JsonResponse::send(401, 'Token de restablecimiento no válido', [], 'INVALID_RESET_TOKEN', 401);
            }
        } else {
            JsonResponse::send(404, 'Usuario no encontrado', [], 'USER_NOT_FOUND', 404);
        }
    }
}
