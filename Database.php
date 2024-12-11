<?php


class Database {
    private static $db = null;
    private $connection;

    public function __construct($servername = "we", $username = "root", $password = "", $dbname = "we") {
        if (self::$db != null) return;
        
        $this->connection = new mysqli($servername, $username, $password);

        $stmt = $this->connection->prepare("CREATE DATABASE IF NOT EXISTS " . $dbname);
        $stmt->execute();

        $this->connection = new mysqli($servername, $username, $password, $dbname);
        
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }

        self::$db = $this;
    }

    public static function getConnection() {
        return self::$db->connection;
    }
}
