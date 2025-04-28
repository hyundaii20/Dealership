<?php

// Database.php
class Database {
    private static $db = null;
    private $connection;

    public function __construct($servername = "localhost", $username = "root", $password = "", $dbname = "car_store") {
        if (self::$db != null) return;
        
        // First try to connect to the MySQL server without database
        $tempConn = new mysqli($servername, $username, $password);
        
        if ($tempConn->connect_error) {
            die("Connection failed: " . $tempConn->connect_error);
        }
        
        // Check if database exists, if not create it
        $result = $tempConn->query("SHOW DATABASES LIKE '$dbname'");
        if ($result->num_rows == 0) {
            // Database doesn't exist, include the setup script
            require_once 'database_setup.php';
        }
        $tempConn->close();
        
        // Now connect to the database
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