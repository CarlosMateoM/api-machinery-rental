<?php

namespace dao\impl;

require_once __DIR__ . '../../../autoload.php';

use \util\DatabaseConnection;
use \dao\MunicipalityDao;
use \model\Municipality;
use \PDO;

class MunicipalityMySqlDao implements MunicipalityDao
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    public function createMunicipality(Municipality $municipality): ?Municipality
    {
        $sql = "INSERT INTO municipalities (name, departament_id) VALUES (:name, :departament_id)";
        $stmt = $this->pdo->prepare($sql);

        $name = $municipality->getName();
        $departamentId = $municipality->getDepartamentId();

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':departament_id', $departamentId);

        if ($stmt->execute()) {
            $lastInsertId = $this->pdo->lastInsertId();
            $municipality->setId($lastInsertId);
            return $municipality;
        }

        return null;
    }

    public function readMunicipalityById(int $id): ?Municipality
    {
        $sql = "SELECT * FROM municipalities WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $municipality = new Municipality();
            $municipality->setId($row['id']);
            $municipality->setName($row['name']);
            $municipality->setDepartamentId($row['departament_id']);
            return $municipality;
        } else {
            return null;
        }
    }

    public function updateMunicipality(Municipality $municipality): ?Municipality
    {
        $sql = "UPDATE municipalities SET name = :name, departament_id = :departament_id WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        $id = $municipality->getId();
        $name = $municipality->getName();
        $departamentId = $municipality->getDepartamentId();

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':departament_id', $departamentId);

        if ($stmt->execute()) {
            return $municipality;
        }

        return null;
    }

    public function deleteMunicipality(int $id): ?Municipality
    {
        $municipality = $this->readMunicipalityById($id);

        if ($municipality) {
            $sql = "DELETE FROM municipalities WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                return $municipality;
            }
        }

        return null;
    }

    public function allMunicipalities(): array
    {
        $sql = "SELECT * FROM municipalities";
        $stmt = $this->pdo->query($sql);
        $municipalities = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $municipality = new Municipality();
            $municipality->setId($row['id']);
            $municipality->setName($row['name']);
            $municipality->setDepartamentId($row['departament_id']);
            $municipalities[] = $municipality->getJson();
        }

        return $municipalities;
    }
}
