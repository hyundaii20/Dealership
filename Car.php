<?php
class Car {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->init();
    }

    public function init() {
        Database::query("CREATE TABLE IF NOT EXISTS Cars (
                id INT AUTO_INCREMENT PRIMARY KEY,
                make VARCHAR(50) NOT NULL,         
                model VARCHAR(50) NOT NULL,        
                year INT NOT NULL,                 
                price DECIMAL(10, 2) NOT NULL      
            );"
        );
    }

    public function create($make, $model, $year, $price) {
        $stmt = $this->db->prepare("INSERT INTO Cars (make, model, year, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssdi", $make, $model, $year, $price);
        return $stmt->execute();
    }

    public function readAll() {
        $result = $this->db->query("SELECT * FROM Cars");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function update($id, $make, $model, $year, $price) {
        $stmt = $this->db->prepare("UPDATE Cars SET make = ?, model = ?, year = ?, price = ? WHERE id = ?");
        $stmt->bind_param("ssdii", $make, $model, $year, $price, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM Cars WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
