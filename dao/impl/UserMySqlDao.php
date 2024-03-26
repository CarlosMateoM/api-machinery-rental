<?php

namespace dao\impl;

require_once __DIR__ . '../../../autoload.php';

use \util\DatabaseConnection;
use \dao\UserDao;
use \model\User;
use \PDO;

class UserMySqlDao implements UserDao
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    public function createUser(User $user): ?User
    {
        $sql = "INSERT INTO users (names, last_names, email, date_of_birth, username, password, phone) 
                VALUES (:names, :last_names, :email, :date_of_birth, :username, :password, :phone)";

        $stmt = $this->pdo->prepare($sql);


        $names = $user->getNames();
        $lastNames = $user->getLastNames();
        $email = $user->getEmail();
        $dateOfBirth = $user->getDateOfBirth();
        $username = $user->getUsername();
        $password = password_hash($user->getPassword(), PASSWORD_BCRYPT);
        $phone = $user->getPhone();

        $user->setPassword($password);

        $stmt->bindParam(':names', $names);
        $stmt->bindParam(':last_names', $lastNames);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':date_of_birth', $dateOfBirth);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':phone', $phone);

        if($stmt->execute()) {
            $lastInsertId = $this->pdo->lastInsertId();
            $user->setId($lastInsertId);
            return $user;
        }
        
        return null;
    }

    public function readUserById(int $id): ?User
    {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
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
            $user->setPassword($row['password']);
            $user->setPhone($row['phone']);

            return $user;
        } else {
            return null;
        }
    }

    public function updateUser(User $user): ?User
    {
        $sql = "UPDATE users SET names = :names, last_names = :last_names, email = :email, date_of_birth = :date_of_birth, 
                username = :username, password = :password, phone = :phone WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        
        $id = $user->getId();
        $names = $user->getNames();
        $lastNames = $user->getLastNames();
        $email = $user->getEmail();
        $dateOfBirth = $user->getDateOfBirth();
        $username = $user->getUsername();
        $password = password_hash($user->getPassword(), PASSWORD_BCRYPT);
        $phone = $user->getPhone();

        $user->setPassword($password);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':names', $names);
        $stmt->bindParam(':last_names', $lastNames);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':date_of_birth', $dateOfBirth);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':phone', $phone);

        if($stmt->execute()) {
            return $user;
        }
        
        return null;
    }

    public function deleteUser(int $id): ?User
    {
        $sql = "DELETE FROM users WHERE id = :id";
        $user = $this->readUserById($id);
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);

        if($stmt->execute()) {
            return $user;
        }

        return null;
    }

    public function allUsers(): array
    {
        $sql = "SELECT * FROM users";
        $stmt = $this->pdo->query($sql);
        $users = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = new User();
            $user->setId($row['id']);
            $user->setNames($row['names']);
            $user->setLastNames($row['last_names']);
            $user->setEmail($row['email']);
            $user->setDateOfBirth($row['date_of_birth']);
            $user->setUsername($row['username']);
            $user->setPassword($row['password']);
            $user->setPhone($row['phone']);

            $users[] = $user->getJson();
        }

        return $users;
    }
}
