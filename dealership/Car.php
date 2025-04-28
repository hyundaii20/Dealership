<?php
class Car {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create($make, $model, $year, $price, $color = null, $vin = null, $description = null, $image_url = null, $stock = 1) {
        $stmt = $this->db->prepare("INSERT INTO cars (make, model, year, price, color, vin, description, image_url, stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssidsssi", $make, $model, $year, $price, $color, $vin, $description, $image_url, $stock);
        return $stmt->execute();
    }

    public function readAll() {
        $result = $this->db->query("SELECT * FROM cars ORDER BY make, model");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function read($id) {
        $stmt = $this->db->prepare("SELECT * FROM cars WHERE car_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function update($id, $make, $model, $year, $price, $color = null, $vin = null, $description = null, $image_url = null, $stock = null, $status = null) {
        $stmt = $this->db->prepare("UPDATE cars SET make = ?, model = ?, year = ?, price = ?, color = ?, vin = ?, description = ?, image_url = ?, stock = ?, status = ? WHERE car_id = ?");
        $stmt->bind_param("ssidssssis", $make, $model, $year, $price, $color, $vin, $description, $image_url, $stock, $status, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM cars WHERE car_id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function search($filters = []) {
        $where_clauses = [];
        $params = [];
        $types = '';
        
        if (!empty($filters['make'])) {
            $where_clauses[] = "make LIKE ?";
            $params[] = "%" . $filters['make'] . "%";
            $types .= 's';
        }
        
        if (!empty($filters['model'])) {
            $where_clauses[] = "model LIKE ?";
            $params[] = "%" . $filters['model'] . "%";
            $types .= 's';
        }
        
        if (!empty($filters['year'])) {
            $where_clauses[] = "year = ?";
            $params[] = (int)$filters['year'];
            $types .= 'i';
        }
        
        if (!empty($filters['min_price'])) {
            $where_clauses[] = "price >= ?";
            $params[] = (float)$filters['min_price'];
            $types .= 'd';
        }
        
        if (!empty($filters['max_price'])) {
            $where_clauses[] = "price <= ?";
            $params[] = (float)$filters['max_price'];
            $types .= 'd';
        }
        
        $sql = "SELECT * FROM cars";
        if (!empty($where_clauses)) {
            $sql .= " WHERE " . implode(" AND ", $where_clauses);
        }
        $sql .= " ORDER BY make, model";
        
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?> 