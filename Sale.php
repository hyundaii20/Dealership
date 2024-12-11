<?php
class Sale {
    private $db;
    public function __construct() {
        $this->db = Database::getConnection();
        $this->init();
    }

    public function init() {
        $this->db->query("CREATE TABLE IF NOT EXISTS sales (
            id INT AUTO_INCREMENT PRIMARY KEY, 
            car_id INT NOT NULL,               
            customer_id INT NOT NULL,          
            employee_id INT NOT NULL,          
            date DATE NOT NULL,                
            total DECIMAL(10, 2) NOT NULL      
        );");
    }

    public function create($car_id, $customer_id, $employee_id, $date, $total) {
        $stmt = $this->db->prepare("INSERT INTO Sales (car_id, customer_id, employee_id, date, total) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiisd", $car_id, $customer_id, $employee_id, $date, $total);
        return $stmt->execute();
    }

    public function readAll() {
        $result = $this->db->query("SELECT * FROM Sales");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function update($id, $car_id, $customer_id, $employee_id, $date, $total) {
        $stmt = $this->db->prepare("UPDATE Sales SET car_id = ?, customer_id = ?, employee_id = ?, date = ?, total = ? WHERE id = ?");
        $stmt->bind_param("iiisdi", $car_id, $customer_id, $employee_id, $date, $total, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM Sales WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
