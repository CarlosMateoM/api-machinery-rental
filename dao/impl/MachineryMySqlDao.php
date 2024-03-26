<?php

namespace dao\impl;

require_once __DIR__ . '/../../autoload.php';

use \util\DatabaseConnection;
use dao\MachineryDao;
use model\Machinery;
use model\Category;
use PDO;

class MachineryMySqlDao implements MachineryDao
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    public function createMachinery(Machinery $machinery): ?Machinery
    {
        $sql = "INSERT INTO machinaries (name, brand, description, category_id) 
                VALUES (:name, :brand, :description, :category_id)";

        $stmt = $this->pdo->prepare($sql);

        $name = $machinery->getName();
        $brand = $machinery->getBrand();
        $description = $machinery->getDescription();
        $category_id = $machinery->getCategory()->getId(); // Suponemos que ya tienes la categoría asignada

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':brand', $brand);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':category_id', $category_id);

        if ($stmt->execute()) {
            $lastInsertId = $this->pdo->lastInsertId();
            $machinery->setId($lastInsertId);
            return $machinery;
        }

        return null;
    }

    public function readMachineryById(int $id): ?Machinery
    {
        $sql = "SELECT * FROM machinaries WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $machinery = new Machinery();
            $machinery->setId($row['id']);
            $machinery->setName($row['name']);
            $machinery->setBrand($row['brand']);
            $machinery->setDescription($row['description']);
            // Aquí deberías cargar la categoría (puedes implementar la lógica según tus necesidades)
            $category = new Category(); // Suponemos que existe un modelo Category
            $category->setId($row['category_id']);
            $machinery->setCategory($category);

            return $machinery;
        } else {
            return null;
        }
    }

    public function updateMachinery(Machinery $machinery): ?Machinery
    {
        $sql = "UPDATE machinaries SET name = :name, brand = :brand, description = :description, category_id = :category_id WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $id = $machinery->getId();
        $name = $machinery->getName();
        $brand = $machinery->getBrand();
        $description = $machinery->getDescription();
        $category_id = $machinery->getCategory()->getId(); // Suponemos que ya tienes la categoría asignada

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':brand', $brand);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':category_id', $category_id);

        if ($stmt->execute()) {
            return $machinery;
        }

        return null;
    }

    public function deleteMachinery(int $id): ?Machinery
    {
        $sql = "DELETE FROM machinaries WHERE id = :id";
        $machinery = $this->readMachineryById($id);
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return $machinery;
        }

        return null;
    }

    public function allMachinaries(): array
    {
        $sql = "SELECT * FROM machinaries";
        $stmt = $this->pdo->query($sql);
        $machinaries = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $machinery = new Machinery();
            $machinery->setId($row['id']);
            $machinery->setName($row['name']);
            $machinery->setBrand($row['brand']);
            $machinery->setDescription($row['description']);
            // Aquí deberías cargar la categoría (puedes implementar la lógica según tus necesidades)
            $category = new Category(); // Suponemos que existe un modelo Category
            $category->setId($row['category_id']);
            $machinery->setCategory($category);

            $machinaries[] = $machinery->getJsonWithRelations();
        }

        return $machinaries;
    }
}
