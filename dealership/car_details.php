<?php
// Include required files
require_once 'Database.php';
require_once 'cookie_manager.php';

// Initialize database connection
$db = new Database();
$conn = Database::getConnection();

// Get car ID from URL
$car_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Add to recently viewed cars
add_to_recently_viewed($car_id);

// Fetch car details
$stmt = $conn->prepare("SELECT * FROM cars WHERE car_id = ?");
$stmt->bind_param("i", $car_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if car exists
if ($result->num_rows === 0) {
    header("Location: cars.php");
    exit;
}

$car = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?> - Car Store</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/car-details.css">
</head>
<body>
    <div class="container">
        <h1><?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?></h1>
        
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="cars.php">Browse Cars</a></li>
                <li><a href="account.php">My Account</a></li>
                <li><a href="cart.php">Shopping Cart</a></li>
            </ul>
        </nav>
        
        <div class="car-details">
            <div class="car-header">
                <h2 class="car-title"><?= htmlspecialchars($car['year'] . ' ' . $car['make'] . ' ' . $car['model']) ?></h2>
                <div class="car-price">€<?= number_format($car['price'], 2) ?></div>
            </div>
            
            <div class="car-status <?= $car['status'] == 'available' ? 'status-available' : 'status-sold-out' ?>">
                <?= ucfirst($car['status']) ?>
            </div>
            
            <div class="car-specs">
                <table>
                    <tr>
                        <td>Make:</td>
                        <td><?= htmlspecialchars($car['make']) ?></td>
                    </tr>
                    <tr>
                        <td>Model:</td>
                        <td><?= htmlspecialchars($car['model']) ?></td>
                    </tr>
                    <tr>
                        <td>Year:</td>
                        <td><?= htmlspecialchars($car['year']) ?></td>
                    </tr>
                    <tr>
                        <td>Color:</td>
                        <td><?= htmlspecialchars($car['color']) ?></td>
                    </tr>
                    <tr>
                        <td>VIN:</td>
                        <td><?= htmlspecialchars($car['vin']) ?></td>
                    </tr>
                    <tr>
                        <td>Stock:</td>
                        <td><?= htmlspecialchars($car['stock']) ?></td>
                    </tr>
                </table>
            </div>
            
            <div class="car-description">
                <h3>Description</h3>
                <p><?= nl2br(htmlspecialchars($car['description'])) ?></p>
            </div>
            
            <div class="actions">
                <a href="cars.php" class="btn">Back to Cars</a>
                <?php if ($car['status'] == 'available' && $car['stock'] > 0): ?>
                    <a href="add_to_cart.php?id=<?= $car['car_id'] ?>" class="btn btn-success">Add to Cart</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html> 