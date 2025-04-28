<?php
// Main entry point for the application
// Autoload classes and initialize database connection

// Require database connection
require_once 'Database.php';
require_once 'Car.php';
require_once 'cookie_manager.php';

// Initialize database connection
// This will automatically create the database and tables if they don't exist
$db = new Database();

// Get database connection
$conn = Database::getConnection();

// Record current page for "last visited" functionality
set_last_visited_page('index.php');

// Check if cars exist in database, if not add sample cars automatically
$cars_count_result = $conn->query("SELECT COUNT(*) as count FROM cars");
$cars_count = $cars_count_result->fetch_assoc()['count'];

if ($cars_count == 0) {
    // Include sample cars file and populate the database
    require_once 'add_sample_cars.php';
}

// Get featured cars for homepage
$featured_cars_query = "SELECT * FROM cars ORDER BY RAND() LIMIT 3";
$featured_result = $conn->query($featured_cars_query);
$featured_cars = $featured_result->fetch_all(MYSQLI_ASSOC);

// Get recently viewed cars if the cookie exists
$recently_viewed = [];
$viewed_car_ids = get_viewed_cars();

if (!empty($viewed_car_ids)) {
    $ids_string = implode(',', array_map('intval', $viewed_car_ids));
    if (!empty($ids_string)) {
        $recently_viewed_query = "SELECT * FROM cars WHERE car_id IN ($ids_string) ORDER BY FIELD(car_id, $ids_string) LIMIT 3";
        $recently_viewed_result = $conn->query($recently_viewed_query);
        if ($recently_viewed_result) {
            $recently_viewed = $recently_viewed_result->fetch_all(MYSQLI_ASSOC);
        }
    }
}

// Website content below
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Store</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/home.css">
</head>
<body>
    <div class="container">
        <h1>Welcome to Our Car Store</h1>
        
        <nav>
            <ul>
                <li><a href="cars.php">Browse Cars</a></li>
                <li><a href="account.php">My Account</a></li>
                <li><a href="cart.php">Shopping Cart</a></li>
            </ul>
        </nav>
        
        <div class="hero-section">
            <h2>Find Your Perfect Car Today</h2>
            <p>Browse our extensive collection of quality vehicles at competitive prices.</p>
            <a href="cars.php" class="btn">Browse All Cars</a>
        </div>
        
        <div class="featured-section">
            <h2>Featured Cars</h2>
            <div class="featured-cars">
                <?php foreach ($featured_cars as $car): ?>
                <div class="car-card">
                    <div class="car-title"><?= htmlspecialchars($car['year'] . ' ' . $car['make'] . ' ' . $car['model']) ?></div>
                    <div class="car-price">€<?= number_format($car['price'], 2) ?></div>
                    <div class="car-details">
                        Color: <?= htmlspecialchars($car['color']) ?><br>
                        Stock: <?= htmlspecialchars($car['stock']) ?>
                    </div>
                    <p><?= htmlspecialchars(substr($car['description'], 0, 100)) ?>...</p>
                    <a href="car_details.php?id=<?= $car['car_id'] ?>" class="view-btn">View Details</a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <?php if (!empty($recently_viewed)): ?>
        <div class="featured-section">
            <h2>Recently Viewed Cars</h2>
            <div class="featured-cars">
                <?php foreach ($recently_viewed as $car): ?>
                <div class="car-card">
                    <div class="car-title"><?= htmlspecialchars($car['year'] . ' ' . $car['make'] . ' ' . $car['model']) ?></div>
                    <div class="car-price">€<?= number_format($car['price'], 2) ?></div>
                    <div class="car-details">
                        Color: <?= htmlspecialchars($car['color']) ?><br>
                        Stock: <?= htmlspecialchars($car['stock']) ?>
                    </div>
                    <p><?= htmlspecialchars(substr($car['description'], 0, 100)) ?>...</p>
                    <a href="car_details.php?id=<?= $car['car_id'] ?>" class="view-btn">View Details</a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <div id="cookie-consent-banner" style="display: <?= cookie_exists('cookie_consent') ? 'none' : 'block' ?>">
        <p>This website uses cookies to ensure you get the best experience on our website.
        <button id="accept-cookies">Accept</button></p>
    </div>
    
    <script src="js/cookie-consent.js"></script>
</body>
</html> 