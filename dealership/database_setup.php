<?php
// database_setup.php
// This script automatically creates the database and tables if they don't exist

// Set to true to display setup messages, false to hide them
$display_messages = false;

// Function to echo messages only if display_messages is true
function setup_message($message) {
    global $display_messages;
    if ($display_messages) {
        echo $message . "<br>";
    }
}

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "car_store";

// Create connection to MySQL server (without database selected)
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    setup_message("Database created or already exists");
} else {
    die("Error creating database: " . $conn->error);
}

// Connect to the database
$conn->select_db($dbname);

// Create tables
$tables = [
    "CREATE TABLE IF NOT EXISTS users (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        phone VARCHAR(20),
        address VARCHAR(200),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS cars (
        car_id INT AUTO_INCREMENT PRIMARY KEY,
        make VARCHAR(50) NOT NULL,
        model VARCHAR(50) NOT NULL,
        year INT NOT NULL,
        color VARCHAR(30),
        vin VARCHAR(17) UNIQUE,
        price DECIMAL(10, 2) NOT NULL,
        description TEXT,
        stock INT NOT NULL DEFAULT 1,
        status ENUM('available', 'sold_out') DEFAULT 'available',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS cart (
        cart_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        car_id INT NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id),
        FOREIGN KEY (car_id) REFERENCES cars(car_id)
    )",
    
    "CREATE TABLE IF NOT EXISTS orders (
        order_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        total_amount DECIMAL(10, 2) NOT NULL,
        status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
        shipping_address VARCHAR(255) NOT NULL,
        order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id)
    )",
    
    "CREATE TABLE IF NOT EXISTS order_items (
        item_id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        car_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(order_id),
        FOREIGN KEY (car_id) REFERENCES cars(car_id)
    )",
    
    "CREATE TABLE IF NOT EXISTS payments (
        payment_id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        amount DECIMAL(10, 2) NOT NULL,
        payment_method ENUM('credit_card', 'paypal', 'bank_transfer') NOT NULL,
        transaction_id VARCHAR(100),
        status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
        FOREIGN KEY (order_id) REFERENCES orders(order_id)
    )"
];

// Create each table
foreach ($tables as $sql) {
    if ($conn->query($sql) === TRUE) {
        setup_message("Table created or already exists");
    } else {
        setup_message("Error creating table: " . $conn->error);
    }
}

// Close connection
$conn->close();

setup_message("Database setup complete!");
?> 