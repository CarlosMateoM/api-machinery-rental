<?php

namespace dao\impl;

require_once 'util/DatabaseConnection.php';
require_once 'dao/ValidationDao.php';

use dao\ValidationDao;
use util\DatabaseConnection;

class ValidationMySqlDao implements ValidationDao
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    public function existsInDatabase(String $table, String $column, $id): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM $table WHERE $column = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();    
        $count = $stmt->fetchColumn();
    
        return $count > 0;
    }

    public function uniqueInDatabase(String $table, String $column, $key): bool
    {
        $sql = "SELECT COUNT(*) FROM $table WHERE $column = :uniqueValue";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':uniqueValue', $key);
        $stmt->execute();    
        $count = $stmt->fetchColumn();
    
        return $count == 0;
    }

}