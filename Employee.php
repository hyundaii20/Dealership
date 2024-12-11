<?php
class Employee {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->init();
    }

    public function init() {
        $this->db->query("CREATE TABLE IF NOT EXISTS Employees (
            id INT AUTO_INCREMENT PRIMARY KEY, 
            name VARCHAR(100) NOT NULL,        
            role VARCHAR(50) NOT NULL,         
            salary DECIMAL(10, 2) NOT NULL     
        );");
    }
    

    public function create($name, $role, $salary) {
        $stmt = $this->db->prepare("INSERT INTO Employees (name, role, salary) VALUES (?, ?, ?)");
        $stmt->bind_param("ssd", $name, $role, $salary);
        return $stmt->execute();
    }

    public function readAll() {
        $result = $this->db->query("SELECT * FROM Employees");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function update($id, $name, $role, $salary) {
        $stmt = $this->db->prepare("UPDATE Employees SET name = ?, role = ?, salary = ? WHERE id = ?");
        $stmt->bind_param("ssdi", $name, $role, $salary, $id);
        return $stmt->execute();
    }

    public function delete($id) { $stmt = $this->db->prepare("DELETE FROM Employees WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
