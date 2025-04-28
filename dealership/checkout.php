<?php
// Start session
session_start();

// Include required files
require_once 'Database.php';

// Initialize database connection
$db = new Database();
$conn = Database::getConnection();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page
    header("Location: login.php");
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Log to file for debugging
$log_file = fopen("order_debug.log", "a");
fwrite($log_file, date("Y-m-d H:i:s") . " - Starting checkout for user_id from session: $user_id\n");
fclose($log_file);

// Get cart items
$cart_query = "SELECT c.cart_id, cr.car_id, cr.make, cr.model, cr.year, cr.price, c.quantity 
               FROM cart c
               JOIN cars cr ON c.car_id = cr.car_id
               WHERE c.user_id = ?";
$stmt = $conn->prepare($cart_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Process checkout form
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    // Get form data
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $state = $_POST['state'] ?? '';
    $zip = $_POST['zip'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';
    
    // Validate form data (simplified)
    if (empty($first_name) || empty($last_name) || empty($email) || empty($address) || 
        empty($city) || empty($state) || empty($zip) || empty($payment_method)) {
        $error_message = 'Please fill in all required fields';
    } else {
        // Begin transaction
        $conn->begin_transaction();
        
        try {
            // Check/update user information
            // First, ensure we're using the user ID from the session
            $session_user_id = $_SESSION['user_id'];
            
            $log_file = fopen("order_debug.log", "a");
            fwrite($log_file, date("Y-m-d H:i:s") . " - Using session user_id=$session_user_id for checkout\n");
            fclose($log_file);
            
            // Get the user's current information
            $user_query = "SELECT * FROM users WHERE user_id = ?";
            $stmt = $conn->prepare($user_query);
            $stmt->bind_param("i", $session_user_id);
            $stmt->execute();
            $user_result = $stmt->get_result();
            
            if ($user_result->num_rows > 0) {
                // Update the existing user with form data
                $user_id = $session_user_id; // Ensure we use the session user ID
                
                $update_user = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ? WHERE user_id = ?";
                $stmt = $conn->prepare($update_user);
                $phone = $_POST['phone'] ?? '';
                $full_address = $address . ', ' . $city . ', ' . $state . ' ' . $zip;
                $stmt->bind_param("sssssi", $first_name, $last_name, $email, $phone, $full_address, $user_id);
                $stmt->execute();
                
                $log_file = fopen("order_debug.log", "a");
                fwrite($log_file, date("Y-m-d H:i:s") . " - Updated user information for user_id=$user_id\n");
                fclose($log_file);
            } else {
                // This shouldn't happen as user is logged in, but handle it anyway
                $log_file = fopen("order_debug.log", "a");
                fwrite($log_file, date("Y-m-d H:i:s") . " - ERROR: Logged in user not found in database. Session user_id=$session_user_id\n");
                fclose($log_file);
                
                throw new Exception("User not found in database.");
            }
            
            // Create the order
            $shipping_address = $address . ', ' . $city . ', ' . $state . ' ' . $zip;
            $order_insert = "INSERT INTO orders (user_id, total_amount, shipping_address, status) VALUES (?, ?, ?, 'pending')";
            $stmt = $conn->prepare($order_insert);
            
            $log_file = fopen("order_debug.log", "a");
            fwrite($log_file, date("Y-m-d H:i:s") . " - Creating order with user_id=$user_id\n");
            fclose($log_file);
            
            $stmt->bind_param("ids", $user_id, $total, $shipping_address);
            $stmt->execute();
            $order_id = $conn->insert_id;
            
            // Add order items
            $order_items_insert = "INSERT INTO order_items (order_id, car_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($order_items_insert);
            
            foreach ($cart_items as $item) {
                $stmt->bind_param("iiid", $order_id, $item['car_id'], $item['quantity'], $item['price']);
                $stmt->execute();
                
                // Update car stock
                $update_stock = "UPDATE cars SET stock = stock - ? WHERE car_id = ?";
                $stmt_stock = $conn->prepare($update_stock);
                $stmt_stock->bind_param("ii", $item['quantity'], $item['car_id']);
                $stmt_stock->execute();
            }
            
            // Add payment record
            $payment_insert = "INSERT INTO payments (order_id, amount, payment_method, status, transaction_id) 
                               VALUES (?, ?, ?, 'completed', ?)";
            $stmt = $conn->prepare($payment_insert);
            $transaction_id = 'TRANS-' . time() . '-' . rand(1000, 9999); // Mock transaction ID
            $stmt->bind_param("idss", $order_id, $total, $payment_method, $transaction_id);
            $stmt->execute();
            
            // Clear the cart
            $clear_cart = "DELETE FROM cart WHERE user_id = ?";
            $stmt = $conn->prepare($clear_cart);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            
            // Commit transaction
            $conn->commit();
            
            // Set success message
            $success_message = "Thank you for your purchase! Your order has been confirmed.";
            
        } catch (Exception $e) {
            // Rollback on error
            $conn->rollback();
            $error_message = "An error occurred: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Car Store</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/checkout.css">
</head>
<body>
    <div class="container">
        <h1>Checkout</h1>
        
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="cars.php">Browse Cars</a></li>
                <li><a href="account.php">My Account</a></li>
                <li><a href="cart.php">Shopping Cart</a></li>
            </ul>
        </nav>
        
        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success_message)): ?>
            <div class="success-message">
                <?php echo $success_message; ?>
                <p><a href="account.php" class="btn">View Your Orders</a></p>
            </div>
        <?php elseif (empty($cart_items)): ?>
            <div class="empty-cart">
                <p>Your cart is empty. You need to add items to your cart before checkout.</p>
                <a href="cars.php" class="btn">Browse Cars</a>
            </div>
        <?php else: ?>
            <div class="checkout-container">
                <div class="checkout-form">
                    <form method="POST" action="" id="checkout-form">
                        <div class="form-section">
                            <h2 class="section-title">Contact Information</h2>
                            
                            <div class="form-group">
                                <label for="first_name">First Name *</label>
                                <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="last_name">Last Name *</label>
                                <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="form-section">
                            <h2 class="section-title">Shipping Address</h2>
                            
                            <div class="form-group">
                                <label for="address">Address *</label>
                                <input type="text" id="address" name="address" required>
                            </div>
                            
                            <div class="address-fields">
                                <div class="form-group">
                                    <label for="city">City *</label>
                                    <input type="text" id="city" name="city" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="state">State/Province *</label>
                                    <input type="text" id="state" name="state" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="zip">ZIP/Postal Code *</label>
                                    <input type="text" id="zip" name="zip" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-section">
                            <h2 class="section-title">Payment Method</h2>
                            
                            <div class="payment-methods">
                                <div class="payment-method">
                                    <input type="radio" id="credit_card" name="payment_method" value="credit_card" required>
                                    <label for="credit_card">Credit Card</label>
                                </div>
                                
                                <div class="payment-method">
                                    <input type="radio" id="paypal" name="payment_method" value="paypal">
                                    <label for="paypal">PayPal</label>
                                </div>
                                
                                <div class="payment-method">
                                    <input type="radio" id="bank_transfer" name="payment_method" value="bank_transfer">
                                    <label for="bank_transfer">Bank Transfer</label>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" name="checkout" class="checkout-button">Complete Order</button>
                    </form>
                </div>
                
                <div class="order-summary">
                    <h2 class="section-title">Order Summary</h2>
                    
                    <?php foreach ($cart_items as $item): ?>
                        <div class="order-item">
                            <div class="order-item-name">
                                <?= htmlspecialchars($item['make'] . ' ' . $item['model']) ?> (x<?= $item['quantity'] ?>)
                            </div>
                            <div class="order-item-price">
                                €<?= number_format($item['price'] * $item['quantity'], 2) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="order-total">
                        <div>Total:</div>
                        <div>€<?= number_format($total, 2) ?></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="js/checkout.js"></script>
</body>
</html> 