<?php

namespace dao\impl;

use PDO;
use model\Category;
use util\DatabaseConnection;
use dao\CategoryDao;

class CategoryMySqlDao implements CategoryDao
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    public function createCategory(Category $category): ?Category
    {
        $sql = "INSERT INTO categories (name) VALUES (:name)";
        $stmt = $this->pdo->prepare($sql);

        $name = $category->getName();
        $stmt->bindParam(':name', $name);

        if ($stmt->execute()) {
            $lastInsertId = $this->pdo->lastInsertId();
            $category->setId($lastInsertId);
            return $category;
        }

        return null;
    }

    public function readCategoryById(int $id): ?Category
    {
        $sql = "SELECT * FROM categories WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $category = new Category();
            $category->setId($row['id']);
            $category->setName($row['name']);
            return $category;
        } else {
            return null;
        }
    }

    public function updateCategory(Category $category): ?Category
    {
        $sql = "UPDATE categories SET name = :name WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        $id = $category->getId();
        $name = $category->getName();

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);

        if ($stmt->execute()) {
            return $category;
        }

        return null;
    }

    public function deleteCategory(int $id): ?Category
    {
        $sql = "DELETE FROM categories WHERE id = :id";
        $category = $this->readCategoryById($id);
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return $category;
        }

        return null;
    }

    public function allCategories(): array
    {
        $sql = "SELECT * FROM categories";
        $stmt = $this->pdo->query($sql);
        $categories = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $category = new Category();
            $category->setId($row['id']);
            $category->setName($row['name']);
            $categories[] = $category->getJson();
        }

        return $categories;
    }
}
