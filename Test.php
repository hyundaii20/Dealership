<?php

include 'Database.php';
include 'Car.php';
include 'Customer.php';
include 'Employee.php';
include 'Sale.php';
include 'Payment.php';


new Database("we", "root", "", "we");



$car = new Car();
$car->create("Toyota", "Avensis", 2020, 24000);
$car->create("Honda", "Civic", 2022, 22000);
$car->create("Ford", "Fiesta", 2006, 2900);
$car->create("Hyundai", "Tucson", 2015, 14000);
$car->create("BMW", "520d", 2017, 19000);
$car->create("Audi", "A4", 2011, 6000);
$car->create("Volkswagen", "Golf", 2020, 20000);
$car->create("Honda", "Accord", 2012, 5400);
$car->create("Ford", "Focus", 2010, 2100);
$car->create("Hyundai", "Ioniq5", 2024, 35000);
$car->create("BMW", "318d", 2013, 14900);
$car->create("Audi", "Q8", 2023, 61000);
$car->create("Toyota", "Yaris", 2010, 2600);
$car->create("Bently", "Continental", 2022, 73000);
$car->create("Volkswagen", "Passat", 2012, 5800);
$car->create("Hyundai", "ix35", 2015, 14000);
$car->create("BMW", "X5", 2016, 22000);
$car->create("Mitsubishi", "Mirage", 2013, 4000);
$car->create("Volkswagen", "Polo", 2010, 2000);
$car->create("Suzuki", "Swift", 2016, 14000);
$car->create("Ford", "Mondeo", 2008, 2200);
$car->create("Audi", "TT", 2010, 5000);
$car->create("Volkswagen", "Golf GTI", 2022, 23000);
$car->create("Audi", "Q3", 2013, 15000);

$cars = $car->readAll();
foreach ($cars as $carData) {
    echo "Car ID: " . $carData['id'] . " Make: " . $carData['make'] . " Model: " . $carData['model'] . "\n";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Dealership</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }
        header {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 1rem 0;
        }
        main {
            padding: 1rem;
        }
        h1, h2 {
            margin: 0 0 1rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f4f4f4;
        }
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 1rem 0;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome to the Car Dealership</h1>
    </header>

    <main>
        <section class="cars">
            <h2>Available Cars</h2>
            <table>
                <tr>
                    <th>Make</th>
                    <th>Model</th>
                    <th>Year</th>
                    <th>Price</th>
                </tr>
                <?php
                $result = $conn->query("SELECT Make, Model, Year, Price FROM cars");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['Make']}</td>
                            <td>{$row['Model']}</td>
                            <td>{$row['Year']}</td>
                            <td>{$row['Price']}</td>
                          </tr>";
                }
                ?>
            </table>
        </section>

        <section class="customers">
            <h2>Customers</h2>
            <table>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                </tr>
                <?php
                $result = $conn->query("SELECT FirstName, LastName, Email, Phone FROM customers");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['FirstName']}</td>
                            <td>{$row['LastName']}</td>
                            <td>{$row['Email']}</td>
                            <td>{$row['Phone']}</td>
                          </tr>";
                }
                ?>
            </table>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Car Dealership</p>
    </footer>
    <?php 
$car->update(1, "Toyota", "Corolla", 2021, 20000);
$car->delete(2);


$customer = new Customer();
$customer->create("John Doe", "john@example.com", "555-1234");
$customers = $customer->readAll();
foreach ($customers as $customerData) {
    echo "Customer ID: " . $customerData['id'] . " Name: " . $customerData['name'] . "\n";
}


$employee = new Employee();
$employee->create("Alice Smith", "Manager", 55000);
$employees = $employee->readAll();
foreach ($employees as $employeeData) {
    echo "Employee ID: " . $employeeData['id'] . " Name: " . $employeeData['name'] . " Role: " . $employeeData['role'] . "\n";
}


$sale = new Sale();
$sale->create(1, 1, 1, "2024-12-10", 24000);
$sales = $sale->readAll();
foreach ($sales as $saleData) {
    echo "Sale ID: " . $saleData['id'] . " Car ID: " . $saleData['car_id'] . " Total: " . $saleData['total'] . "\n";
}


$payment = new Payment();
$payment->create(1, "Credit Card", 24000);
$payments = $payment->readAll();
foreach ($payments as $paymentData) {
    echo "Payment ID: " . $paymentData['id'] . " Method: " . $paymentData['method'] . " Amount: " . $paymentData['amount'] . "\n";
}
