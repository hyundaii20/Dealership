<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page
    header("Location: login.php");
    exit();
}

// Include required files
require_once 'Database.php';

// Initialize database connection
$db = new Database();
$conn = Database::getConnection();

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Get user information
$user_query = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();

// If user doesn't exist in database (shouldn't happen normally)
if ($user_result->num_rows === 0) {
    // Destroy session and redirect to login
    session_destroy();
    header("Location: login.php?error=account_not_found");
    exit();
}

$user = $user_result->fetch_assoc();

// Get user's orders with proper error handling
try {
    // Add debugging info to a hidden div
    echo "<!-- DEBUG: User ID being used for orders query: " . $user_id . " -->";
    
    $orders_query = "SELECT o.*, COUNT(oi.item_id) as items_count, 
                 MAX(p.payment_method) as payment_method, MAX(p.status) as payment_status
                 FROM orders o 
                 LEFT JOIN order_items oi ON o.order_id = oi.order_id
                 LEFT JOIN payments p ON o.order_id = p.order_id
                 WHERE o.user_id = ?
                 GROUP BY o.order_id
                 ORDER BY o.order_date DESC";
    $stmt = $conn->prepare($orders_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $orders_result = $stmt->get_result();
    $orders = $orders_result->fetch_all(MYSQLI_ASSOC);
    
    // More debugging info
    echo "<!-- DEBUG: Number of orders found: " . count($orders) . " -->";
} catch (Exception $e) {
    $orders = [];
    echo "<!-- DEBUG: Error in orders query: " . $e->getMessage() . " -->";
}

// Process form submissions
$success_message = '';
$error_message = '';

// Update user profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    
    if (empty($first_name) || empty($last_name) || empty($email)) {
        $error_message = "Please fill in all required fields.";
    } else {
        $update_user = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ? WHERE user_id = ?";
        $stmt = $conn->prepare($update_user);
        $stmt->bind_param("sssssi", $first_name, $last_name, $email, $phone, $address, $user_id);
        
        if ($stmt->execute()) {
            $success_message = "Profile updated successfully!";
            // Refresh user data
            $stmt = $conn->prepare($user_query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $user_result = $stmt->get_result();
            $user = $user_result->fetch_assoc();
        } else {
            $error_message = "Error updating profile: " . $conn->error;
        }
    }
}

// Add user's current car
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_car'])) {
    $car_make = $_POST['car_make'] ?? '';
    $car_model = $_POST['car_model'] ?? '';
    $car_year = (int)($_POST['car_year'] ?? 0);
    $car_color = $_POST['car_color'] ?? '';
    
    if (empty($car_make) || empty($car_model) || $car_year < 1900) {
        $error_message = "Please fill in all car information correctly.";
    } else {
        // Check if the user_cars table exists, if not create it
        $check_table = $conn->query("SHOW TABLES LIKE 'user_cars'");
        if ($check_table->num_rows === 0) {
            $create_table = "CREATE TABLE user_cars (
                car_id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                make VARCHAR(50) NOT NULL,
                model VARCHAR(50) NOT NULL,
                year INT NOT NULL,
                color VARCHAR(30),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(user_id)
            )";
            $conn->query($create_table);
        }
        
        $insert_car = "INSERT INTO user_cars (user_id, make, model, year, color) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_car);
        $stmt->bind_param("issis", $user_id, $car_make, $car_model, $car_year, $car_color);
        
        if ($stmt->execute()) {
            $success_message = "Car added successfully!";
        } else {
            $error_message = "Error adding car: " . $conn->error;
        }
    }
}

// Get user's current cars
try {
    // Check if the user_cars table exists, if not create it
    $check_table = $conn->query("SHOW TABLES LIKE 'user_cars'");
    if ($check_table->num_rows === 0) {
        $create_table = "CREATE TABLE user_cars (
            car_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            make VARCHAR(50) NOT NULL,
            model VARCHAR(50) NOT NULL,
            year INT NOT NULL,
            color VARCHAR(30),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(user_id)
        )";
        $conn->query($create_table);
    }
    
    $user_cars_query = "SELECT * FROM user_cars WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($user_cars_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_cars_result = $stmt->get_result();
    $user_cars = $user_cars_result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    // If there's an error with user_cars, just create an empty array
    $user_cars = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - Car Store</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/account.css">
</head>
<body>
    <div class="container">
        <h1>My Account</h1>
        
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="cars.php">Browse Cars</a></li>
                <li><a href="account.php">My Account</a></li>
                <li><a href="cart.php">Shopping Cart</a></li>
            </ul>
        </nav>
        
        <?php if (!empty($success_message)): ?>
            <div class="success-message">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <div class="tab-container">
            <div class="tab-buttons">
                <button class="tab-button active" onclick="openTab(event, 'profile')">My Profile</button>
                <button class="tab-button" onclick="openTab(event, 'orders')">Order History</button>
                <button class="tab-button" onclick="openTab(event, 'cars')">My Cars</button>
            </div>
            
            <div id="profile" class="tab-content active">
                <h2>My Profile</h2>
                <div class="profile-container">
                    <div class="profile-info">
                        <h3>Account Information</h3>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo !empty($user['phone']) ? htmlspecialchars($user['phone']) : 'Not provided'; ?></p>
                        <p><strong>Address:</strong> <?php echo !empty($user['address']) ? htmlspecialchars($user['address']) : 'Not provided'; ?></p>
                        <p><strong>Member Since:</strong> <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                    </div>
                    
                    <div class="profile-form">
                        <h3>Update Profile</h3>
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="first_name">First Name *</label>
                                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="last_name">Last Name *</label>
                                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="address">Address</label>
                                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
                            </div>
                            
                            <button type="submit" name="update_profile">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div id="orders" class="tab-content">
                <h2>Order History</h2>
                <?php if (empty($orders)): ?>
                    <p>You haven't placed any orders yet.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Payment</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?php echo $order['order_id']; ?></td>
                                    <td><?php echo date('M j, Y', strtotime($order['order_date'])); ?></td>
                                    <td><?php echo $order['items_count']; ?> item(s)</td>
                                    <td>€<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td><?php echo ucfirst($order['status']); ?></td>
                                    <td>
                                        <?php 
                                        echo ucfirst(str_replace('_', ' ', $order['payment_method'])) . ' - ';
                                        echo ucfirst($order['payment_status']);
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            
            <div id="cars" class="tab-content">
                <h2>My Cars</h2>
                <div class="section">
                    <h3>Add a Car</h3>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="car_make">Make *</label>
                            <input type="text" id="car_make" name="car_make" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="car_model">Model *</label>
                            <input type="text" id="car_model" name="car_model" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="car_year">Year *</label>
                            <input type="number" id="car_year" name="car_year" min="1900" max="<?php echo date('Y') + 1; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="car_color">Color</label>
                            <input type="text" id="car_color" name="car_color">
                        </div>
                        
                        <button type="submit" name="add_car">Add Car</button>
                    </form>
                </div>
                
                <div class="section">
                    <h3>My Current Cars</h3>
                    <?php if (empty($user_cars)): ?>
                        <p>You haven't added any cars yet.</p>
                    <?php else: ?>
                        <div class="car-grid">
                            <?php foreach ($user_cars as $car): ?>
                                <div class="car-card">
                                    <div class="car-title">
                                        <?php echo htmlspecialchars($car['year'] . ' ' . $car['make'] . ' ' . $car['model']); ?>
                                    </div>
                                    <div class="car-details">
                                        <p><strong>Color:</strong> <?php echo htmlspecialchars($car['color']); ?></p>
                                        <p><strong>Added:</strong> <?php echo date('M j, Y', strtotime($car['created_at'])); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script src="js/account.js"></script>
</body>
</html> 