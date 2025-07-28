<?php

namespace App\Database;

use PDO;
use PDOException;

class Database
{
    private $pdo;
    public function __construct()
    {
        $dbPath = dirname(__FILE__). '/../../' .$_ENV['DB_PATH'];
        $this->pdo = new PDO("sqlite:$dbPath");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}