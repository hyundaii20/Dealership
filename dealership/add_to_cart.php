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

// Check if car ID was provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Redirect to cars page if no car ID specified
    header("Location: cars.php");
    exit();
}

$car_id = (int)$_GET['id'];

// Verify car exists and is in stock
$car_check = "SELECT car_id, make, model, stock, status FROM cars WHERE car_id = ? AND stock > 0 AND status = 'available'";
$stmt = $conn->prepare($car_check);
$stmt->bind_param("i", $car_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Car not found or out of stock, redirect with error
    header("Location: cars.php?error=Car is not available");
    exit();
}

$car = $result->fetch_assoc();

// Check if the car is already in the cart
$cart_check = "SELECT cart_id, quantity FROM cart WHERE user_id = ? AND car_id = ?";
$stmt = $conn->prepare($cart_check);
$stmt->bind_param("ii", $user_id, $car_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Car already in cart, update quantity
    $cart_item = $result->fetch_assoc();
    $new_quantity = $cart_item['quantity'] + 1;
    
    // Make sure we don't exceed available stock
    if ($new_quantity > $car['stock']) {
        $new_quantity = $car['stock'];
    }
    
    $update_cart = "UPDATE cart SET quantity = ? WHERE cart_id = ?";
    $stmt = $conn->prepare($update_cart);
    $stmt->bind_param("ii", $new_quantity, $cart_item['cart_id']);
    $stmt->execute();
    
    // Redirect to cart page with success message
    header("Location: cart.php?success=Car quantity updated in your cart");
    exit();
} else {
    // Add car to cart
    $insert_cart = "INSERT INTO cart (user_id, car_id, quantity) VALUES (?, ?, 1)";
    $stmt = $conn->prepare($insert_cart);
    $stmt->bind_param("ii", $user_id, $car_id);
    $stmt->execute();
    
    // Redirect to cart page with success message
    header("Location: cart.php?success=Car added to your cart");
    exit();
}
?> 