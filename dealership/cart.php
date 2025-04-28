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

// Check for cart item removal
if (isset($_GET['remove_id']) && is_numeric($_GET['remove_id'])) {
    $remove_id = (int)$_GET['remove_id'];
    $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $remove_id, $user_id);
    $stmt->execute();
    
    // Redirect to avoid form resubmission on refresh
    header("Location: cart.php");
    exit();
}

// Handle quantity updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    // Loop through the cart items and update quantities
    foreach ($_POST['quantity'] as $cart_id => $quantity) {
        if ($quantity > 0) {
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ? AND user_id = ?");
            $stmt->bind_param("iii", $quantity, $cart_id, $user_id);
            $stmt->execute();
        } else {
            // Remove item if quantity is 0
            $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ? AND user_id = ?");
            $stmt->bind_param("ii", $cart_id, $user_id);
            $stmt->execute();
        }
    }
    
    // Redirect to avoid form resubmission on refresh
    header("Location: cart.php");
    exit();
}

// Get cart items
$cart_query = "SELECT c.cart_id, cr.car_id, cr.make, cr.model, cr.year, cr.color, cr.price, c.quantity 
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Car Store</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/cart.css">
</head>
<body>
    <div class="container">
        <h1>Your Shopping Cart</h1>
        
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="cars.php">Browse Cars</a></li>
                <li><a href="account.php">My Account</a></li>
                <li><a href="cart.php">Shopping Cart</a></li>
            </ul>
        </nav>
        
        <?php if (empty($cart_items)): ?>
            <div class="empty-cart">
                <p>Your cart is empty.</p>
                <a href="cars.php" class="continue-shopping">Continue Shopping</a>
            </div>
        <?php else: ?>
            <form method="POST" action="">
                <table>
                    <thead>
                        <tr>
                            <th>Car</th>
                            <th>Details</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($item['make'] . ' ' . $item['model']); ?>
                                </td>
                                <td>
                                    Year: <?php echo htmlspecialchars($item['year']); ?><br>
                                    Color: <?php echo htmlspecialchars($item['color']); ?>
                                </td>
                                <td>€<?php echo number_format($item['price'], 2); ?></td>
                                <td>
                                    <input type="number" name="quantity[<?php echo $item['cart_id']; ?>]" value="<?php echo $item['quantity']; ?>" min="0" class="quantity-input">
                                </td>
                                <td>€<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                <td>
                                    <a href="cart.php?remove_id=<?php echo $item['cart_id']; ?>" class="remove-btn">Remove</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <button type="submit" name="update_cart" class="update-btn">Update Cart</button>
                
                <div class="cart-summary">
                    <div class="cart-total">
                        Total: €<?php echo number_format($total, 2); ?>
                    </div>
                    <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
    
    <script src="js/cart.js"></script>
</body>
</html> 