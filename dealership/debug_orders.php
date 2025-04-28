<?php
// Start session
session_start();

// Include required files
require_once 'Database.php';

// Initialize database connection
$db = new Database();
$conn = Database::getConnection();

// Check total number of orders in the system
$orders_count_query = "SELECT COUNT(*) as count FROM orders";
$result = $conn->query($orders_count_query);
$orders_count = $result->fetch_assoc()['count'];
echo "<p>Total orders in the system: $orders_count</p>";

// If user is logged in, check their orders
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    echo "<p>Logged in user ID: $user_id</p>";
    
    // Get user's orders with debugging information
    $orders_query = "SELECT o.*, COUNT(oi.item_id) as items_count, 
                 MAX(p.payment_method) as payment_method, MAX(p.status) as payment_status
                 FROM orders o 
                 LEFT JOIN order_items oi ON o.order_id = oi.order_id
                 LEFT JOIN payments p ON o.order_id = p.order_id
                 WHERE o.user_id = ?
                 GROUP BY o.order_id
                 ORDER BY o.order_date DESC";
    
    echo "<p>SQL Query: " . htmlspecialchars($orders_query) . "</p>";
    
    $stmt = $conn->prepare($orders_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $orders_result = $stmt->get_result();
    
    echo "<p>Query returned " . $orders_result->num_rows . " rows.</p>";
    
    if ($orders_result->num_rows > 0) {
        echo "<h3>Orders found:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>Order ID</th><th>Date</th><th>Total</th><th>Status</th><th>Items Count</th><th>Payment Method</th></tr>";
        
        while ($order = $orders_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $order['order_id'] . "</td>";
            echo "<td>" . $order['order_date'] . "</td>";
            echo "<td>$" . number_format($order['total_amount'], 2) . "</td>";
            echo "<td>" . $order['status'] . "</td>";
            echo "<td>" . $order['items_count'] . "</td>";
            echo "<td>" . $order['payment_method'] . " - " . $order['payment_status'] . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p>No orders found for this user.</p>";
        
        // Check if the user has any orders without the joins
        $simple_query = "SELECT * FROM orders WHERE user_id = ?";
        $stmt = $conn->prepare($simple_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $simple_result = $stmt->get_result();
        
        if ($simple_result->num_rows > 0) {
            echo "<p>Found " . $simple_result->num_rows . " orders with a simpler query (no joins).</p>";
            
            echo "<table border='1'>";
            echo "<tr><th>Order ID</th><th>Date</th><th>Total</th><th>Status</th></tr>";
            
            while ($order = $simple_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $order['order_id'] . "</td>";
                echo "<td>" . $order['order_date'] . "</td>";
                echo "<td>$" . number_format($order['total_amount'], 2) . "</td>";
                echo "<td>" . $order['status'] . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
        }
    }
} else {
    echo "<p>No user is logged in.</p>";
}

// Check query for a specific user ID if provided
if (isset($_GET['user_id'])) {
    $test_user_id = intval($_GET['user_id']);
    echo "<h3>Testing with specific user ID: $test_user_id</h3>";
    
    $test_query = "SELECT * FROM orders WHERE user_id = ?";
    $stmt = $conn->prepare($test_query);
    $stmt->bind_param("i", $test_user_id);
    $stmt->execute();
    $test_result = $stmt->get_result();
    
    if ($test_result->num_rows > 0) {
        echo "<p>Found " . $test_result->num_rows . " orders for user ID $test_user_id</p>";
    } else {
        echo "<p>No orders found for user ID $test_user_id</p>";
    }
}

// Add a link back to the account page
echo "<p><a href='account.php'>Back to account</a></p>"; 