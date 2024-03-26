<?php

namespace dao\impl;

use \util\DatabaseConnection;
use dao\CommentDao;
use model\Comment;
use model\User;
use PDO;

class CommentMySqlDao implements CommentDao
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    public function createComment(Comment $comment): ?Comment
    {
        $sql = "INSERT INTO comments (publication_id, user_id, content, date) VALUES (:publicationId, :userId, :content, CURRENT_DATE)";
        $stmt = $this->pdo->prepare($sql);

        $publicationId = $comment->getPublicationId();
        $userId = $comment->getUser()->getId();
        $content = $comment->getContent();
            
        $stmt->bindParam(':publicationId', $publicationId);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':content', $content);
        
        

        if ($stmt->execute()) {
            $commentId = $this->pdo->lastInsertId();
            return $this->readCommentById($commentId);
        } else {
            return null;
        }
    }

    public function readCommentById(int $id): ?Comment
    {
        $sql = "SELECT * FROM comments WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $comment = new Comment();
            $comment->setId($row['id']);
            $comment->setPublicationId($row['publication_id']);
            $comment->setContent($row['content']);
            $comment->setDate($row['date']);

            // Cargar la relación con User según tus necesidades aquí
            $user = new User(); // Debes cargar el usuario real desde la base de datos
            $user->setId($row['user_id']); // Ajusta según el nombre real de la columna
            $comment->setUser($user);

            return $comment;
        } else {
            return null;
        }
    }

    public function updateComment(Comment $comment): ?Comment
    {
        $sql = "UPDATE comments SET publication_id = :publicationId, user_id = :userId, content = :content, date = :date WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':publicationId', $comment->getPublicationId());
        $stmt->bindParam(':userId', $comment->getUser()->getId());
        $stmt->bindParam(':content', $comment->getContent());
        $stmt->bindParam(':date', $comment->getDate());
        $stmt->bindParam(':id', $comment->getId());

        if ($stmt->execute()) {
            return $this->readCommentById($comment->getId());
        } else {
            return null;
        }
    }

    public function deleteComment(int $id): ?Comment
    {
        $existingComment = $this->readCommentById($id);

        if ($existingComment) {
            $sql = "DELETE FROM comments WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                return $existingComment;
            }
        }

        return null;
    }

    public function getCommentsByPublicationId(int $publicationId): array
    {
        $sql = "SELECT 
        c.*,
        u.names,
        u.last_names
        FROM comments AS c 
        INNER JOIN users AS u ON c.user_id = u.id 
        WHERE c.publication_id = :publicationId ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':publicationId', $publicationId);
        $stmt->execute();
        $comments = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $comment = new Comment();
            $comment->setId($row['id']);
            $comment->setPublicationId($row['publication_id']);
            $comment->setContent($row['content']);
            $comment->setDate($row['date']);

            // Cargar la relación con User según tus necesidades aquí
            $user = new User(); // Debes cargar el usuario real desde la base de datos
            $user->setId($row['user_id']); // Ajusta según el nombre real de la columna
            $user->setNames($row['names']);
            $user->setLastNames($row['last_names']);
            $comment->setUser($user);

            $comments[] = $comment->getJsonWithRelations();
        }

        return $comments;
    }
}
