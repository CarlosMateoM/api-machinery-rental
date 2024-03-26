<?php

namespace util;

use PDO;
use PDOException;

class DatabaseConnection
{

    private static $instance;
    private $conn;

    private $password = "";
    private $username = "root";
    private $dbname = "alquiler_maquinarias";
    private $servername = "localhost";

    private function __construct()
    {
        try {
            $this->conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Conexión fallida: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
