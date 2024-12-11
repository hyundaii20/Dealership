<?php
class Customer {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->init();
    }

    public function init() {
        $this->db->query("CREATE TABLE IF NOT EXISTS Customers (
                id INT AUTO_INCREMENT PRIMARY KEY, 
                name VARCHAR(100) NOT NULL,        
                email VARCHAR(100) NOT NULL,      
                phone VARCHAR(15)                  
            );
        ");
    }

    public function create($name, $email, $phone) {
        $stmt = $this->db->prepare("INSERT INTO Customers (name, email, phone) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $phone);
        return $stmt->execute();
    }

    public function readAll() {
        $result = $this->db->query("SELECT * FROM Customers");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function update($id, $name, $email, $phone) {
        $stmt = $this->db->prepare("UPDATE Customers SET name = ?, email = ?, phone = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $email, $phone, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM Customers WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
