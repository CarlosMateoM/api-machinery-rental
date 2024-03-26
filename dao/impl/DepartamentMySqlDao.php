<?php

namespace dao\impl;

require_once __DIR__ . '/../../autoload.php';

use \util\DatabaseConnection;
use \dao\DepartamentDao;
use \model\Departament;
use \model\Municipality;
use \PDO;

class DepartamentMySqlDao implements DepartamentDao
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    public function createDepartament(Departament $departament): ?Departament
    {
        $sql = "INSERT INTO departaments (name) VALUES (:name)";
        $stmt = $this->pdo->prepare($sql);

        $name = $departament->getName();

        $stmt->bindParam(':name', $name);

        if ($stmt->execute()) {
            $lastInsertId = $this->pdo->lastInsertId();
            $departament->setId($lastInsertId);
            return $departament;
        }

        return null;
    }

    public function readDepartamentById(int $id): ?Departament
    {
        // Obtener el departamento por ID
        $sqlDepartament = "SELECT * FROM departaments WHERE id = :id";
        $stmtDepartament = $this->pdo->prepare($sqlDepartament);
        $stmtDepartament->bindParam(':id', $id);
        $stmtDepartament->execute();
        $rowDepartament = $stmtDepartament->fetch(PDO::FETCH_ASSOC);

        if (!$rowDepartament) {
            return null;
        }

        // Crear el objeto Departament
        $departament = new Departament();
        $departament->setId($rowDepartament['id']);
        $departament->setName($rowDepartament['name']);

        $departamentId = $departament->getId();

        // Obtener los municipios asociados al departamento
        $sqlMunicipalities = "SELECT * FROM municipalities WHERE departament_id = :departament_id";
        $stmtMunicipalities = $this->pdo->prepare($sqlMunicipalities);
        $stmtMunicipalities->bindParam(':departament_id', $departamentId);
        $stmtMunicipalities->execute();

        // Cargar los municipios al departamento
        while ($rowMunicipality = $stmtMunicipalities->fetch(PDO::FETCH_ASSOC)) {
            $municipality = new Municipality();
            $municipality->setId($rowMunicipality['id']);
            $municipality->setName($rowMunicipality['name']);
            $municipality->setDepartamentId($departament->getId());
            $departament->addMunicipality($municipality);
        }

        return $departament;
    }


    public function updateDepartament(Departament $departament): ?Departament
    {
        $sql = "UPDATE departaments SET name = :name WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        $id = $departament->getId();
        $name = $departament->getName();

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);

        if ($stmt->execute()) {
            return $departament;
        }

        return null;
    }

    public function deleteDepartament(int $id): ?Departament
    {
        $departament = $this->readDepartamentById($id);

        if ($departament) {
            $sql = "DELETE FROM departaments WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                return $departament;
            }
        }

        return null;
    }

    public function allDepartaments(): array
    {
        $sql = "SELECT * FROM departaments";
        $stmt = $this->pdo->query($sql);
        $departaments = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $departament = new Departament();
            $departament->setId($row['id']);
            $departament->setName($row['name']);

            $departaments[] = $departament->getJson();
        }

        return $departaments;
    }
}
