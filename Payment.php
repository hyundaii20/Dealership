<?php
class Payment {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->init();
    }

    public function init() {
        $this->db->query("CREATE TABLE IF NOT EXISTS Payments (
            id INT AUTO_INCREMENT PRIMARY KEY, 
            sale_id INT NOT NULL,              
            method VARCHAR(50) NOT NULL,      
            amount DECIMAL(10, 2) NOT NULL   
        );");
    }

    public function create($sale_id, $method, $amount) {
        $stmt = $this->db->prepare("INSERT INTO Payments (sale_id, method, amount) VALUES (?, ?, ?)");
        $stmt->bind_param("isd", $sale_id, $method, $amount);
        return $stmt->execute();
    }

    public function readAll() {
        $result = $this->db->query("SELECT * FROM Payments");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function update($id, $sale_id, $method, $amount) {
        $stmt = $this->db->prepare("UPDATE Payments SET sale_id = ?, method = ?, amount = ? WHERE id = ?");
        $stmt->bind_param("isdi", $sale_id, $method, $amount, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM Payments WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
