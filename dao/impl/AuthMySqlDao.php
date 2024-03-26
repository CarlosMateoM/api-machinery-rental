<?php

namespace dao\impl;

require_once './util/DatabaseConnection.php';
require_once './dao/AuthDao.php';
require_once './model/User.php';

use util\DatabaseConnection;
use model\User;
use dao\AuthDao;
use PDO;


class AuthMySqlDao implements AuthDao
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    public function login(User $user): ?User
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);

        $email = $user->getEmail();
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            if (password_verify($user->getPassword(), $row['password'])) {
                $user->setId($row['id']);
                $user->setNames($row['names']);
                $user->setLastNames($row['last_names']);
                $user->setEmail($row['email']);
                $user->setDateOfBirth($row['date_of_birth']);
                $user->setUsername($row['username']);
                $user->setPassword($row['password']);
                $user->setPhone($row['phone']);
                return $user;
            }
        }

        return null;
    }

    public function getUserByEmail(string $email): ?User
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $user = new User();
            
            $user->setId($row['id']);
            $user->setNames($row['names']);
            $user->setLastNames($row['last_names']);
            $user->setEmail($row['email']);
            $user->setDateOfBirth($row['date_of_birth']);
            $user->setUsername($row['username']);
            $user->setPhone($row['phone']);
            $user->setResetToken($row['reset_token']);
            
            return $user;
        }

        return null;
    }

    public function setResetToken($userId, $token): void
    {
        try {
            $sql = "UPDATE users SET reset_token = :token WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
        } catch (\PDOException $e) {
            // Manejo de errores: puedes lanzar una excepción personalizada o manejar de otra manera
            throw new \Exception('Error al establecer el token de restablecimiento de contraseña', 500);
        }
    }

    public function updatePassword($userId, $hashedPassword): void
    {
        try {
            $sql = "UPDATE users SET password = :password WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
        } catch (\PDOException $e) {
            // Manejo de errores: puedes lanzar una excepción personalizada o manejar de otra manera
            throw new \Exception('Error al actualizar la contraseña', 500);
        }
    }

    public function removeResetToken($userId): void
    {
        try {
            $sql = "UPDATE users SET reset_token = NULL WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
        } catch (\PDOException $e) {
            // Manejo de errores: puedes lanzar una excepción personalizada o manejar de otra manera
            throw new \Exception('Error al eliminar el token de restablecimiento de contraseña', 500);
        }
    }
}
