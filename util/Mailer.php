<?php
namespace util;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; 

class Mailer
{
    public static function sendMail($to, $subject, $body)
    {
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();

            $mail->Host       = 'smtp.gmail.com'; // Cambia esto
            $mail->SMTPAuth   = true;
            $mail->Username   = 'sincelejo22222@gmail.com'; // Cambia esto
            $mail->Password   = 'srzn pbze oxln shyt'; // Cambia esto
            $mail->SMTPSecure = 'tls'; // Cambia esto si es necesario
            $mail->Port       = 587; // Cambia esto según la configuración de tu servidor SMTP

            // Destinatario y contenido del correo
            $mail->setFrom('sincelejo22222@gmail.com', 'Sincelejo'); // Cambia esto
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            // Enviar el correo
            $mail->send();

            return true;
            
        } catch (Exception $e) {
            return false;
        }
    }
}