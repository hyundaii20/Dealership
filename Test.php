<?php

include 'Database.php';
include 'Car.php';
include 'Customer.php';
include 'Employee.php';
include 'Sale.php';
include 'Payment.php';


new Database("we", "root", "", "we");

// $car = new Car();
// $car->create("Toyota", "Avensis", 2020, 24000);
// $car->create("Honda", "Civic", 2022, 22000);
// $car->create("Ford", "Fiesta", 2006, 2900);
// $car->create("Hyundai", "Tucson", 2015, 14000);
// $car->create("BMW", "520d", 2017, 19000);
// $car->create("Audi", "A4", 2011, 6000);
// $car->create("Volkswagen", "Golf", 2020, 20000);
// $car->create("Honda", "Accord", 2012, 5400);
// $car->create("Ford", "Focus", 2010, 2100);
// $car->create("Hyundai", "Ioniq5", 2024, 35000);
// $car->create("BMW", "318d", 2013, 14900);
// $car->create("Audi", "Q8", 2023, 61000);
// $car->create("Toyota", "Yaris", 2010, 2600);
// $car->create("Bently", "Continental", 2022, 73000);
// $car->create("Volkswagen", "Passat", 2012, 5800);
// $car->create("Hyundai", "ix35", 2015, 14000);
// $car->create("BMW", "X5", 2016, 22000);
// $car->create("Mitsubishi", "Mirage", 2013, 4000);
// $car->create("Volkswagen", "Polo", 2010, 2000);
// $car->create("Suzuki", "Swift", 2016, 14000);
// $car->create("Ford", "Mondeo", 2008, 2200);
// $car->create("Audi", "TT", 2010, 5000);
// $car->create("Volkswagen", "Golf GTI", 2022, 23000);
// $car->create("Audi", "Q3", 2013, 15000);

$models = Database::query("SELECT DISTINCT model FROM cars");
$makes = Database::query("SELECT DISTINCT make FROM cars");

$selectedModel = null;
$selectedMake = null;
$cars = [];

if (isset($_GET['model']) && isset($_GET['make'])) {
    $selectedModel = $_GET['model'];
    $selectedMake = $_GET['make'];
    $cars = Database::query("SELECT * FROM Cars WHERE make = '$selectedMake' AND model = '$selectedModel'");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Dealership</title>
    <style>
        /* General page styling */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        /* Header styling */
        h1 {
            text-align: center;
            color: #333;
            margin-top: 20px;
        }

        /* Form styling */
        form {
            display: flex;
            justify-content: center;
            margin: 20px 0;
            gap: 15px;
        }

        label {
            font-weight: bold;
            margin-right: 10px;
            font-size: 16px;
        }

        select, input[type="submit"] {
            padding: 10px;
            font-size: 16px;
            margin-left: 10px;
            border: 2px solid #ccc;
            border-radius: 5px;
            outline: none;
        }

        select:focus, input[type="submit"]:hover {
            border-color: #007BFF;
        }

        input[type="submit"] {
            background-color: #007BFF;
            color: white;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        /* Table styling */
        table {
            width: 80%;
            margin: 30px auto;
            border-collapse: collapse;
            background-color: white;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007BFF;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        /* No cars message */
        p {
            text-align: center;
            font-size: 18px;
            color: #333;
        }
    </style>
</head>
<body>

    <h1>Welcome to Our Car Dealership</h1>

    <!-- Form to select car make and model -->
    <form method="GET">
        <label for="make">Select Car Make:</label>
        <select name="make" id="make" required>
            <option value="">-- Select Make --</option>
            <?php foreach ($makes as $make): ?>
                <option value="<?= htmlspecialchars($make['make']); ?>" <?= isset($selectedMake) && $selectedMake == $make['make'] ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($make['make']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="model">Select Car Model:</label>
        <select name="model" id="model" required>
            <option value="">-- Select Model --</option>
            <?php foreach ($models as $model): ?>
                <option value="<?= htmlspecialchars($model['model']); ?>" <?= isset($selectedModel) && $selectedModel == $model['model'] ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($model['model']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <input type="submit" value="Show Cars">
    </form>

    <?php if (!empty($cars)): ?>
        <table>
            <thead>
                <tr>
                    <th>Make</th>
                    <th>Model</th>
                    <th>Year</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cars as $car): ?>
                    <tr>
                        <td><?= htmlspecialchars($car['make']); ?></td>
                        <td><?= htmlspecialchars($car['model']); ?></td>
                        <td><?= htmlspecialchars($car['year']); ?></td>
                        <td>$<?= number_format($car['price'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif (isset($selectedMake) && isset($selectedModel)): ?>
        <p>No cars found for the selected make and model.</p>
    <?php endif; ?>

</body>
</html>