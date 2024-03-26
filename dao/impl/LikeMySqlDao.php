<?php

namespace dao\impl;

require_once __DIR__ . '/../../autoload.php';

use \util\DatabaseConnection;
use dao\LikeDao;
use model\Like;
use model\User;
use model\Publication;
use PDO;

class LikeMySqlDao implements LikeDao
{

    private $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    public function readLikesByPublicationId(int $publicationId): array
    {
        $sql = "SELECT * FROM likes WHERE publication_id = :publicationId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':publicationId', $publicationId);
        $stmt->execute();

        $likes = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $like = new Like();
            $like->setId($row['id']);
            $publication = new Publication();
            $publication->setId($row['publication_id']);

            $like->setPublication($publication);

            // Obtenemos el usuario que dio el like
            $userDao = new UserMySqlDao(); // Reemplaza con tu implementación de UserDao
            $user = $userDao->readUserById($row['user_id']);
            $like->setUser($user);

            $likes[] = $like;
        }

        return $likes;
    }

    public function createLike(Like $like): ?Like
    {

        $sql = "INSERT INTO likes (publication_id, user_id) VALUES (:publicationId, :userId)";
        $stmt = $this->pdo->prepare($sql);

        $publicationId = $like->getPublication()->getId();
        $userId = $like->getUser()->getId();

        $stmt->bindParam(':publicationId', $publicationId);
        $stmt->bindParam(':userId', $userId);

        if ($stmt->execute()) {
            $lastInsertId = $this->pdo->lastInsertId();
            $like->setId($lastInsertId);
            return $like;
        }

        return null;
    }

    public function deleteLike(int $id): ?Like
    {
        $sql = "DELETE FROM likes WHERE id = :id";
        $like = $this->readLikeById($id);
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return $like;
        }

        return null;
    }

    public function readLikeById(int $id): ?Like
    {

        $sql = "SELECT * FROM likes WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $like = new Like();
            $like->setId($row['id']);
            $publication = new Publication();
            $publication->setId($row['publication_id']);
            $like->setPublication($publication);

            // Obtenemos el usuario que dio el like
            $user = new User(); // Reemplaza con tu implementación de UserDao
            $user = $user->setId($row['user_id']);
            $like->setUser($user);

            return $like;
        } else {
            return null;
        }
    }

    public function userLikedPublication(int $userId, int $publicationId): bool
    {
        $sql = "SELECT COUNT(*) FROM likes WHERE user_id = :userId AND publication_id = :publicationId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':publicationId', $publicationId);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    public function getLikesByUserId(int $userId): array
    {
        $sql = "SELECT 
        l.id as like_id,
        p.id as publication_id,
        p.title
        FROM likes as l 
        INNER JOIN publications AS p ON l.publication_id = p.id
        WHERE l.user_id = :userId";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();

        $likes = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            
            $like = [
                "likeId" => $row['like_id'],
                "publicationId" => $row['publication_id'],
                "publicationTitle" => $row['title']
            ];

            $likes[] = $like;
        }

        return $likes;
    }
}
