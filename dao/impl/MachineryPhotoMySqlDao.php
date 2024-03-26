<?php

namespace dao\impl;

require_once __DIR__ . '../../../autoload.php';

use \util\DatabaseConnection;
use \dao\MachineryPhotoDao;
use \model\MachineryPhoto;
use \PDO;

class MachineryPhotoMySqlDao implements MachineryPhotoDao
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    public function createPhoto(MachineryPhoto $photo): ?MachineryPhoto
    {
        $sql = "INSERT INTO machinery_photos (src, machinery_id) VALUES (:src, :machinery_id)";

        $stmt = $this->pdo->prepare($sql);

        $src = $photo->getSrc();
        $machineryId = $photo->getMachineryId();

        $stmt->bindParam(':src', $src);
        $stmt->bindParam(':machinery_id', $machineryId);

        if ($stmt->execute()) {
            $lastInsertId = $this->pdo->lastInsertId();
            $photo->setId($lastInsertId);
            return $photo;
        }

        return null;
    }

    public function readPhotoById(int $id): ?MachineryPhoto
    {
        $sql = "SELECT * FROM machinery_photos WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $photo = new MachineryPhoto();
            $photo->setId($row['id']);
            $photo->setSrc($row['src']);
            $photo->setMachineryId($row['machinery_id']);

            return $photo;
        } else {
            return null;
        }
    }

    public function updatePhoto(MachineryPhoto $photo): ?MachineryPhoto
    {
        $sql = "UPDATE machinery_photos SET src = :src, machinery_id = :machinery_id WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $id = $photo->getId();
        $src = $photo->getSrc();
        $machineryId = $photo->getMachineryId();

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':src', $src);
        $stmt->bindParam(':machinery_id', $machineryId);

        if ($stmt->execute()) {
            return $photo;
        }

        return null;
    }

    public function deletePhoto(int $id): ?MachineryPhoto
    {
        $photo = $this->readPhotoById($id);

        if ($photo) {
            $sql = "DELETE FROM machinery_photos WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                return $photo;
            }
        }

        return null;
    }

    public function getPhotosByMachineryId(int $machineryId): array
    {
        $sql = "SELECT * FROM machinery_photos WHERE machinery_id = :machinery_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':machinery_id', $machineryId);
        $stmt->execute();
        $photos = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $photo = new MachineryPhoto();
            $photo->setId($row['id']);
            $photo->setSrc($row['src']);
            $photo->setMachineryId($row['machinery_id']);

            $photos[] = $photo;
        }

        return $photos;
    }
}
