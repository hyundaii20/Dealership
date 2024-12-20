<?php


class Database {
    private static $db = null;

    public function __construct($servername = "we", $username = "root", $password = "", $dbname = "we") {
        if (self::$db != null) return;
        
        self::$db = new mysqli($servername, $username, $password);

        $stmt = self::$db->prepare("CREATE DATABASE IF NOT EXISTS " . $dbname);
        $stmt->execute();

        self::$db = new mysqli($servername, $username, $password, $dbname);
        
        if (self::$db->connect_error) {
            die("Connection failed: " . self::$db->connect_error);
        }
    }

    public static function getConnection() {
        return self::$db;
    }

    public static function query( $query, $params = array() ) {

        $stmt = self::$db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->get_result();
        return $result;
    }
}
